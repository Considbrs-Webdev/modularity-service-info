<?php

namespace ModularityServiceInfo;

use ModularityServiceInfo\Helper\CacheBust;
use ModularityServiceInfo\PostType\ServiceInformation;
use ModularityServiceInfo\Admin\Settings;

/**
 * Class App
 * 
 * Main application bootstrap class.
 * Initialize your plugin components here.
 * 
 * @package ModularityServiceInfo
 */
class App
{
    public function __construct()
    {
        // Initialize custom post type
        new ServiceInformation();

        // Initialize options page
        new Settings();

        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    /**
     * Enqueue styles
     * 
     * @return void
     */
    public function enqueueStyles(): void
    {
        $styleFile = CacheBust::name('css/modularity-service-info.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-service-info',
                MODULARITYSERVICEINFO_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * Register the module with Modularity
     * 
     * @return void
     */
    public function registerModule(): void
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYSERVICEINFO_MODULE_PATH,
                'ServiceInfo',
            );
        }
    }
}
