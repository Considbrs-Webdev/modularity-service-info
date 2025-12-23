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
