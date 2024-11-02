<?php
/**
 * Plugin Name: LogAction - Activity Log Plugin
 * Plugin URI: https://gblessylva.com/logaction
 * Description: Logs various WordPress actions to help site administrators monitor user activity and system events.
 * Version: 1.0.0
 * Author: gblessylva
 * Author URI: https://gblessylva.com
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: logaction
 * Domain Path: /languages
 */

defined('ABSPATH') || exit; // Exit if accessed directly

// Autoload Classes
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Use the main plugin namespace
use LogAction\LogAction;

// Run plugin activation and deactivation hooks
register_activation_hook(__FILE__, ['LogAction\LogAction', 'activate']);
register_deactivation_hook(__FILE__, ['LogAction\LogAction', 'deactivate']);

/**
 * Initializes the plugin.
 */
function logaction_initialize_plugin() {
    $plugin = LogAction::getInstance();
    $plugin->init();
}
add_action('plugins_loaded', 'logaction_initialize_plugin');
