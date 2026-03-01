<?php

namespace ModularityServiceInfo\Cron;

class UnpublishExpiredPosts
{
    private const CRON_HOOK = 'modularity_service_info_unpublish_expired';

    public function __construct()
    {
        add_action('init', array($this, 'registerCommand'));
        add_action(self::CRON_HOOK, array($this, 'handleCron'));
        add_action('acf/save_post', array($this, 'handleSettingsSave'), 20);
    }

    /**
     * Fires after ACF options are saved. Reschedules the cron job
     * according to the 'schedule_unpublish_task' setting.
     *
     * @param mixed $postId
     * @return void
     */
    public function handleSettingsSave($postId): void
    {
        if ($postId !== 'service-information-settings') {
            return;
        }

        $setting = get_field('schedule_unpublish_task', 'service-information-settings') ?: '-';
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
        // Remove existing scheduled event regardless of recurrence
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }

        if (in_array($setting, ['hourly', 'daily'], true)) {
            wp_schedule_event(time(), $setting, self::CRON_HOOK);
        }
    }

    public function registerCommand()
    {
        if (defined('WP_CLI') && constant('WP_CLI') === true) {
            \WP_CLI::add_command('service-info unpublish', array($this, 'unpublishCommand'));
        }
    }

    public function handleCron()
    {
        $this->unpublishExpiredPosts(false, 100);
    }

    public function unpublishCommand($args, $assoc_args)
    {
        $dryRun = isset($assoc_args['dry-run']);
        $limit = isset($assoc_args['limit']) ? (int)$assoc_args['limit'] : 100;

        $this->unpublishExpiredPosts($dryRun, $limit);
    }

    public function unpublishExpiredPosts($dryRun = false, $limit = 100)
    {
        $args = array(
            'post_type'      => 'service_information',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'unpublish_automatically',
                    'value'   => '1',
                    'compare' => '='
                ),
                array(
                    'key'     => 'end_date',
                    'value'   => current_time('Y-m-d H:i:s'),
                    'compare' => '<=',
                    'type'    => 'DATETIME'
                ),
                array(
                    'key'     => 'end_date',
                    'value'   => '',
                    'compare' => '!='
                )
            )
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $postId = get_the_ID();
                $action = get_field('on_unpublish', $postId) ?: 'draft';
                
                // Sanitize action
                if (!in_array($action, ['draft', 'trash'])) {
                    $action = 'draft';
                }

                if ($dryRun) {
                    if (defined('WP_CLI') && constant('WP_CLI') === true) {
                        \WP_CLI::log(sprintf('Would move post "%s" (ID: %d) to %s', get_the_title(), $postId, $action));
                    }
                } else {
                    $updated = wp_update_post(array(
                        'ID'          => $postId,
                        'post_status' => $action
                    ));

                    if (is_wp_error($updated)) {
                        if (defined('WP_CLI') && constant('WP_CLI') === true) {
                            \WP_CLI::warning(sprintf('Failed to move post "%s" (ID: %d) to %s', get_the_title(), $postId, $action));
                        }
                    } else {
                        if (defined('WP_CLI') && constant('WP_CLI') === true) {
                            \WP_CLI::success(sprintf('Moved post "%s" (ID: %d) to %s', get_the_title(), $postId, $action));
                        }
                    }
                }
            }
            wp_reset_postdata();
        } else {
            if ($dryRun && defined('WP_CLI') && constant('WP_CLI') === true) {
                \WP_CLI::log('No expired posts found.');
            }
        }
    }
}
