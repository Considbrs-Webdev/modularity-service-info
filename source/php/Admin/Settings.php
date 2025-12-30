<?php

namespace ModularityServiceInfo\Admin;

use ModularityServiceInfo\Helper\LiteSpeed;

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
        
        // Hide the LiteSpeed ESI toggle if we're not running on a LiteSpeed server
        add_filter('acf/prepare_field/name=litespeed_esi_cache_support', [$this, 'maybeHideEsiField']);
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

    /**
     * Hide the LiteSpeed ESI field when the server does not support LiteSpeed
     *
     * @param array|bool $field
     * @return array|bool
     */
    public function maybeHideEsiField($field)
    {
        if (!LiteSpeed::isRunningOnServer()) {
            return false; // ACF will not render the field
        }

        return $field;
    }
}
