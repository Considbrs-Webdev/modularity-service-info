<?php

/**
 * Plugin Name:       Modularity Service Information
 * Plugin URI:        https://github.com/considbrs-webdev/modularity-service-info
 * Description:       A Modularity module to display service information in an elegant way.
 * Version: 1.0.0
 * Author:            Consid Borås AB
 * Author URI:        https://github.com/considbrs-webdev
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-service-info
 * Domain Path:       /languages
 */

// Protect against direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITYSERVICEINFO_PATH', plugin_dir_path(__FILE__));
define('MODULARITYSERVICEINFO_URL', plugins_url('', __FILE__));
define('MODULARITYSERVICEINFO_MODULE_VIEW_PATH', plugin_dir_path(__FILE__) . 'source/php/Module/views');
define('MODULARITYSERVICEINFO_MODULE_PATH', MODULARITYSERVICEINFO_PATH . 'source/php/Module/');

// Load text domain
add_action('init', function () {
    load_plugin_textdomain('modularity-service-info', false, plugin_basename(dirname(__FILE__)) . '/languages');
});

// Autoload from plugin
if (file_exists(MODULARITYSERVICEINFO_PATH . 'vendor/autoload.php')) {
    require_once MODULARITYSERVICEINFO_PATH . 'vendor/autoload.php';
}

// ACF auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-service-info');
    $acfExportManager->setExportFolder(MODULARITYSERVICEINFO_PATH . '/source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'general-settings'  => 'group_694a9d25909d8',
        'module-settings'   => 'group_694aa3a8a7d33',
        'taxonomy-settings' => 'group_695288c6283da',
        'post-data'         => 'group_694a913636a90',
    ));
    $acfExportManager->import();
});

// Modularity 3.0 ready - ViewPath for Component library
add_filter('/Modularity/externalViewPath', function ($arr) {
    $arr['mod-service-info'] = MODULARITYSERVICEINFO_MODULE_VIEW_PATH;
    return $arr;
}, 10, 3);

// Start application
new ModularityServiceInfo\App();

