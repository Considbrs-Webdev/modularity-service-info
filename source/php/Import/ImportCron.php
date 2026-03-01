<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Import;

use ModularityServiceInfo\PostType\ServiceInformation;

/**
 * Class ImportCron
 *
 * Runs registered importers on a scheduled cron and creates/updates
 * service information posts. Also provides a WP-CLI command for
 * manual/debug execution.
 *
 * @package ModularityServiceInfo\Import
 */
class ImportCron
{
    private const CRON_HOOK = 'modularity_service_info_import';
    private const META_SOURCE_KEY = '_service_info_import_source';

    private ImporterRegistry $registry;

    public function __construct(ImporterRegistry $registry)
    {
        $this->registry = $registry;

        add_action('init', [$this, 'init']);
        add_action(self::CRON_HOOK, [$this, 'handleCron']);
        add_action('acf/save_post', [$this, 'handleSettingsSave'], 20);
    }

    /**
     * Initialize: collect importers, register WP-CLI command, schedule cron.
     */
    public function init(): void
    {
        // Let other plugins register their importers
        do_action('modularity_service_info_register_importers', $this->registry);

        // Register WP-CLI command
        if (defined('WP_CLI') && constant('WP_CLI') === true) {
            \WP_CLI::add_command('service-info import', [$this, 'cliCommand']);
        }
    }

    /**
     * Fires after ACF options are saved. Reschedules the import cron job
     * according to the 'schedule_import_external_info' setting.
     *
     * @param mixed $postId
     * @return void
     */
    public function handleSettingsSave($postId): void
    {
        if ($postId !== 'service-information-settings') {
            return;
        }

        $setting = get_field('schedule_import_external_info', 'service-information-settings') ?: '-';
        $this->applySchedule((string) $setting);
    }

    /**
     * Clear any existing schedule and set a new one based on $setting.
     * Passing '-' (or any unrecognised value) will only clear the schedule.
     *
     * @param string $setting  'hourly', 'daily', or '-'
     * @return void
     */
    public function applySchedule(string $setting): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }

        if (in_array($setting, ['hourly', 'daily'], true)) {
            wp_schedule_event(time(), $setting, self::CRON_HOOK);
        }
    }

    /**
     * Cron handler — runs all registered importers.
     */
    public function handleCron(): void
    {
        $this->runAllImporters();
    }

    /**
     * WP-CLI command: wp service-info import [--importer=<key>] [--dry-run]
     *
     * @param array $args
     * @param array $assocArgs
     */
    public function cliCommand(array $args, array $assocArgs): void
    {
        $dryRun = isset($assocArgs['dry-run']);
        $importerKey = $assocArgs['importer'] ?? null;

        if ($importerKey) {
            $importer = $this->registry->getImporter($importerKey);

            if (!$importer) {
                \WP_CLI::error(sprintf('Importer "%s" is not registered.', $importerKey));
                return;
            }

            $this->runImporter($importer, $dryRun);
        } else {
            $this->runAllImporters($dryRun);
        }
    }

    /**
     * Run all registered importers.
     */
    private function runAllImporters(bool $dryRun = false): void
    {
        $importers = $this->registry->getImporters();

        if (empty($importers)) {
            $this->log('No importers registered.');
            return;
        }

        foreach ($importers as $importer) {
            $this->runImporter($importer, $dryRun);
        }
    }

    /**
     * Run a single importer and upsert its items.
     */
    private function runImporter(ImporterInterface $importer, bool $dryRun = false): void
    {
        $this->log(sprintf('Running importer: %s (%s)', $importer->getName(), $importer->getKey()));

        try {
            $items = $importer->import();
        } catch (\Throwable $e) {
            $this->log(
                sprintf('Importer "%s" threw an exception: %s', $importer->getKey(), $e->getMessage()),
                'warning'
            );
            return;
        }

        $this->log(sprintf('Received %d item(s) from "%s".', count($items), $importer->getKey()));

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            if (!$item instanceof ServiceInfoItem) {
                $this->log('Skipping non-ServiceInfoItem value.', 'warning');
                $skipped++;
                continue;
            }

            $sourceIdentifier = $importer->getKey() . ':' . $item->getSourceId();

            if ($dryRun) {
                $existingId = $this->findExistingPost($sourceIdentifier);
                $action = $existingId ? 'update' : 'create';
                $this->log(sprintf(
                    '[DRY-RUN] Would %s: "%s" (source: %s)',
                    $action,
                    $item->getTitle(),
                    $sourceIdentifier
                ));
                continue;
            }

            $result = $this->upsertServiceInfo($item, $sourceIdentifier);

            if ($result === 'created') {
                $created++;
            } elseif ($result === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
        }

        if (!$dryRun) {
            $this->log(sprintf(
                'Importer "%s" finished: %d created, %d updated, %d skipped.',
                $importer->getKey(),
                $created,
                $updated,
                $skipped
            ));
        }
    }

    /**
     * Create or update a service information post from an imported item.
     *
     * @param ServiceInfoItem $item
     * @param string $sourceIdentifier  Composite key: "importerKey:sourceId"
     * @return string 'created'|'updated'|'skipped'
     */
    private function upsertServiceInfo(ServiceInfoItem $item, string $sourceIdentifier): string
    {
        $existingPostId = $this->findExistingPost($sourceIdentifier);

        $postData = [
            'post_type'    => ServiceInformation::POST_TYPE_NAME,
            'post_title'   => $item->getTitle(),
            'post_content' => $item->getContent(),
            'post_status'  => 'publish',
        ];

        if ($existingPostId) {
            $postData['ID'] = $existingPostId;
            $result = wp_update_post($postData, true);

            if (is_wp_error($result)) {
                $this->log(
                    sprintf('Failed to update post "%s": %s', $item->getTitle(), $result->get_error_message()),
                    'warning'
                );
                return 'skipped';
            }

            $postId = $existingPostId;
            $action = 'updated';
        } else {
            $result = wp_insert_post($postData, true);

            if (is_wp_error($result)) {
                $this->log(
                    sprintf('Failed to create post "%s": %s', $item->getTitle(), $result->get_error_message()),
                    'warning'
                );
                return 'skipped';
            }

            $postId = $result;
            $action = 'created';

            // Store the source identifier for future deduplication
            update_post_meta($postId, self::META_SOURCE_KEY, $sourceIdentifier);
        }

        // Update ACF fields
        $this->updateAcfFields($postId, $item);

        // Assign taxonomy terms (creating them if necessary)
        $this->assignCategories($postId, $item->getCategories());

        $this->log(sprintf(
            '%s post "%s" (ID: %d, source: %s)',
            ucfirst($action),
            $item->getTitle(),
            $postId,
            $sourceIdentifier
        ));

        return $action;
    }

    /**
     * Update ACF fields for a service information post.
     */
    private function updateAcfFields(int $postId, ServiceInfoItem $item): void
    {
        $startDate = $item->getStartDate()->format('Y-m-d H:i:s');
        update_field('start_date', $startDate, $postId);

        if ($item->getEndDate()) {
            $endDate = $item->getEndDate()->format('Y-m-d H:i:s');
            update_field('end_date', $endDate, $postId);
            update_field('unpublish_automatically', true, $postId);
            update_field('on_unpublish', 'trash', $postId);
        } else {
            update_field('end_date', '', $postId);
            update_field('unpublish_automatically', false, $postId);
        }
    }

    /**
     * Assign taxonomy terms to a post, creating them if they don't exist.
     *
     * @param int      $postId
     * @param string[] $categoryNames
     */
    private function assignCategories(int $postId, array $categoryNames): void
    {
        if (empty($categoryNames)) {
            return;
        }

        $termIds = [];

        foreach ($categoryNames as $categoryName) {
            $term = get_term_by('name', $categoryName, 'service_category');

            if ($term) {
                $termIds[] = $term->term_id;
            } else {
                // Create the term
                $result = wp_insert_term($categoryName, 'service_category');

                if (is_wp_error($result)) {
                    $this->log(
                        sprintf('Failed to create term "%s": %s', $categoryName, $result->get_error_message()),
                        'warning'
                    );
                    continue;
                }

                $termIds[] = $result['term_id'];
                $this->log(sprintf('Created taxonomy term "%s" (ID: %d)', $categoryName, $result['term_id']));
            }
        }

        if (!empty($termIds)) {
            wp_set_object_terms($postId, $termIds, 'service_category');
        }
    }

    /**
     * Find an existing post by its import source identifier.
     *
     * @param string $sourceIdentifier
     * @return int|null Post ID or null if not found
     */
    private function findExistingPost(string $sourceIdentifier): ?int
    {
        $query = new \WP_Query([
            'post_type'      => ServiceInformation::POST_TYPE_NAME,
            'post_status'    => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => self::META_SOURCE_KEY,
                    'value'   => $sourceIdentifier,
                    'compare' => '=',
                ],
            ],
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);

        $posts = $query->get_posts();

        return !empty($posts) ? (int) $posts[0] : null;
    }

    /**
     * Log a message (WP-CLI if available, otherwise error_log).
     */
    private function log(string $message, string $level = 'info'): void
    {
        if (defined('WP_CLI') && constant('WP_CLI') === true) {
            match ($level) {
                'warning' => \WP_CLI::warning($message),
                'error'   => \WP_CLI::error($message, false),
                'success' => \WP_CLI::success($message),
                default   => \WP_CLI::log($message),
            };
        } else {
            error_log(sprintf('[ServiceInfo Import][%s] %s', strtoupper($level), $message));
        }
    }
}
