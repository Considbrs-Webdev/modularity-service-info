<?php

namespace ModularityServiceInfo\Admin;

/**
 * Class Settings
 * 
 * Registers the ACF options page for Service Information settings.
 * 
 * @package ModularityServiceInfo\Admin
 */
class Settings
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'registerOptionsPage']);
    }

    /**
     * Register ACF options page for Service Information
     * 
     * @return void
     */
    public function registerOptionsPage(): void
    {
        if (function_exists('acf_add_options_sub_page')) {
            acf_add_options_sub_page([
                'page_title'  => __('Service Information Settings', 'modularity-service-info'),
                'menu_title'  => __('Service Information', 'modularity-service-info'),
                'menu_slug'   => 'service-information-settings',
                'parent_slug' => 'options-general.php',
                'post_id'     => 'service-information-settings',
                'capability'  => 'manage_options',
            ]);
        }
    }
}
