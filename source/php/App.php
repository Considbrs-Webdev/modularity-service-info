<?php

namespace ModularityServiceInfo;

use ModularityServiceInfo\Helper\CacheBust;
use ModularityServiceInfo\PostType\ServiceInformation;
use ModularityServiceInfo\Admin\Settings;
use ModularityServiceInfo\Cron\UnpublishExpiredPosts;
use ModularityServiceInfo\Admin\ServiceInfoMenu;

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

        // Initialize menu functionality
        new ServiceInfoMenu();

        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        add_filter('acf/load_field_group', [$this, 'removeAdvancedTermSettings']);
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

    public function removeAdvancedTermSettings($field_group) {
        // Target this specific field group
        if ($field_group['key'] !== 'group_63e6002cc129c') {
            return $field_group;
        }

        // Only on taxonomy edit screens
        if (!is_admin() || empty($_GET['taxonomy'])) {
            return $field_group;
        }

        $taxonomy = sanitize_text_field($_GET['taxonomy']);

        // Disable for specific taxonomy
        if ($taxonomy === 'service_category') {
            return false; // ← removes the field group completely
        }

        return $field_group;
    }
}
