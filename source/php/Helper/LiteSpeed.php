<?php

namespace ModularityServiceInfo\Helper;

/**
 * Class LiteSpeed
 *
 * Helper for detecting LiteSpeed plugin and server environment.
 *
 * @package ModularityServiceInfo\Helper
 */
class LiteSpeed
{
    /**
     * Detect if LiteSpeed Cache plugin is active
     *
     * @return bool
     */
    public static function isPluginActive(): bool
    {
        if (class_exists('LiteSpeed_Cache')) {
            return true;
        }

        if (defined('LSCWP_VERSION')) {
            return true;
        }

        if (function_exists('litespeed_purge') || function_exists('litespeed_buddy')) {
            return true;
        }

        if (!function_exists('is_plugin_active')) {
            if (defined('ABSPATH')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
        }

        if (function_exists('is_plugin_active') && is_plugin_active('litespeed-cache/litespeed-cache.php')) {
            return true;
        }

        return false;
    }

    /**
     * Detect if the current PHP environment is running on a LiteSpeed server
     *
     * @return bool
     */
    public static function isRunningOnServer(): bool
    {
        if (!empty($_SERVER['SERVER_SOFTWARE'])) {
            $s = strtolower($_SERVER['SERVER_SOFTWARE']);
            if (strpos($s, 'litespeed') !== false || strpos($s, 'openlitespeed') !== false) {
                return true;
            }
        }

        $env = getenv('SERVER_SOFTWARE');
        if ($env && (stripos($env, 'litespeed') !== false || stripos($env, 'openlitespeed') !== false)) {
            return true;
        }

        if (stripos(php_sapi_name(), 'litespeed') !== false) {
            return true;
        }

        return false;
    }
}
