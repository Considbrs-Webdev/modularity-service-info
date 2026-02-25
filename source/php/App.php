<?php

namespace ModularityServiceInfo;

use ModularityServiceInfo\Helper\CacheBust;
use ModularityServiceInfo\PostType\ServiceInformation;
use ModularityServiceInfo\Admin\Settings;
use ModularityServiceInfo\Helper\Settings as SettingsHelper;
use ModularityServiceInfo\Cron\UnpublishExpiredPosts;
use ModularityServiceInfo\Import\ImporterRegistry;
use ModularityServiceInfo\Import\ImportCron;
use ModularityServiceInfo\Admin\ServiceInfoMenu;
use ModularityServiceInfo\Validation\ACF_Validation;
use ModularityServiceInfo\Decorators\Decorators;

/**
 * Class App
 * 
 * Main application bootstrap class.
 * Initialize your plugin components here.
 * 
 * @package ModularityServiceInfo
 */
class App {
    public function __construct() {
        // Initialize custom post type
        new ServiceInformation();

        // Initialize options page
        new Settings();

        // Initialize cron
        new UnpublishExpiredPosts();

        // Initialize import cron (with registry for third-party importers)
        $importerRegistry = new ImporterRegistry();
        new ImportCron($importerRegistry);

        // Initialize menu functionality
        new ServiceInfoMenu();

        // Initialize validation handlers (ACF)
        new ACF_Validation();

        // Initialize decorators
        new Decorators();

        // Typesense search integration
        new TypesenseSearchIntegration();

        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        // Add general view path (service_information pages and search)
        add_action('template_redirect', function () {
            if (get_post_type() === 'service_information' || is_search()) {
                add_filter('Municipio/viewPaths', array($this, 'addViewPaths'), 2, 1);
            }
        }, 10);

        // Enqueue frontend styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendStyles']);

        // Fix breadcrumbs if service archive is custom page
        add_filter('Municipio/Breadcrumbs/Items', [$this, 'maybeInsertArchiveIntoBreadcrumbs'], 20);
    }

    /**
     * Add searchable blade template paths
     * @param array  $array Template paths
     * @return array        Modified template paths
     */
    public function addViewPaths($array)
    {
        return array_merge( [MODULARITYSERVICEINFO_VIEW_PATH], $array );
    }

    /**
     * Register the module with Modularity
     * 
     * @return void
     */
    public function registerModule(): void {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYSERVICEINFO_MODULE_PATH,
                'ServiceInfo',
            );
        }
    }

    public function enqueueFrontendStyles(): void {
        $styleFile = CacheBust::name('css/modularity-service-info-general.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-service-info-general',
                MODULARITYSERVICEINFO_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * Insert archive page and its ancestors into breadcrumb items after the first item.
     *
     * @param array|null $items
     * @return array|null Modified breadcrumb items or null if no changes made
     */
    public function maybeInsertArchiveIntoBreadcrumbs($items): array|null
    {
        if (!is_array($items)) {
            return $items;
        }

        if (!is_singular(ServiceInformation::POST_TYPE_NAME)) {
            return $items;
        }

        $customArchiveLinkId = SettingsHelper::getArchivePage(false);
        if (empty($customArchiveLinkId)) {
            return $items;
        }

        // Ensure we have the post ID
        $archiveId = (int) $customArchiveLinkId;
        if ($archiveId <= 0 || get_post_status($archiveId) === false) {
            return $items;
        }

        // Get ancestors from top-most down to direct parent
        $ancestors = array_reverse(get_post_ancestors($archiveId));

        // If no ancestors and archive is already present, nothing to do
        if (empty($ancestors) && array_key_exists($archiveId, $items)) {
            return $items;
        }

        // Build insertion items keyed by post ID, skipping duplicates
        $insertItems = [];
        foreach ($ancestors as $ancestorId) {
            if (array_key_exists($ancestorId, $items)) {
                continue;
            }

            $insertItems[$ancestorId] = [
                'label' => get_the_title($ancestorId),
                'href' => get_permalink($ancestorId),
                'current' => false,
                'icon' => 'chevron_right',
            ];
        }

        // Add archive page itself if not already in items
        if (!array_key_exists($archiveId, $items)) {
            $insertItems[$archiveId] = [
                'label' => get_the_title($archiveId),
                'href' => get_permalink($archiveId),
                'current' => false,
                'icon' => 'chevron_right',
            ];
        }

        if (empty($insertItems)) {
            return $items;
        }

        // Rebuild items: keep first element, insert new items, then remaining
        $newItems = [];
        $keys = array_keys($items);
        $firstKey = array_shift($keys);

        // Add first original item
        $newItems[$firstKey] = $items[$firstKey];

        // Add inserted ancestors + archive
        foreach ($insertItems as $k => $v) {
            $newItems[$k] = $v;
        }

        // Add remaining original items in original order
        foreach ($keys as $k) {
            if (isset($items[$k])) {
                $newItems[$k] = $items[$k];
            }
        }

        return $newItems;
    }
}
