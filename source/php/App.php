<?php

namespace ModularityServiceInfo;

use ModularityServiceInfo\Helper\CacheBust;
use ModularityServiceInfo\PostType\ServiceInformation;
use ModularityServiceInfo\Admin\Settings;
use ModularityServiceInfo\Cron\UnpublishExpiredPosts;
use ModularityServiceInfo\Admin\ServiceInfoMenu;
use ModularityServiceInfo\Validation\ACF_Validation;

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

        // Initialize validation handlers (ACF)
        new ACF_Validation();

        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        // Add general view path
        add_action('template_redirect', function () {
            if (get_post_type() === 'service_information') {
                add_filter('Municipio/viewPaths', array($this, 'addViewPaths'), 2, 1);
            }
        }, 10);

        // Enqueue frontend styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendStyles']);
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
}
