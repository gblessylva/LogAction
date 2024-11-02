<?php

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Use the autoloader if necessary for any classes (assuming Composer autoload is enabled in the plugin)
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Cleanup class for uninstalling
class LogActionUninstall {
    
    /**
     * Clean up the custom database tables.
     */
    public static function remove_database_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'logaction_events'; // Custom event table name
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    }

    /**
     * Delete plugin options
     */
    public static function delete_options() {
        delete_option( 'logaction_settings' ); // Plugin settings option
    }

    /**
     * Perform uninstall actions
     */
    public static function uninstall() {
        self::remove_database_tables();
        self::delete_options();
    }
}

// Execute uninstall actions
LogActionUninstall::uninstall();
