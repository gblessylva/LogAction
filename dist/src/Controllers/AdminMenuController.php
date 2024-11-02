<?php

declare(strict_types=1);

namespace LogAction\Controllers;

use LogAction\Interfaces\AdminMenuInterface;

/**
 * Class AdminMenuController
 *
 * Handles the addition of the admin menu and submenus for the LogAction plugin.
 */
class AdminMenuController implements AdminMenuInterface {

    /**
     * Register the menu and submenus in the WordPress admin.
     */
    public function registerMenus(): void {
        add_action('admin_menu', [$this, 'addAdminMenus']);
    }

    /**
     * Adds the LogAction menu and its submenus.
     */
    public function addAdminMenus(): void {
        add_menu_page(
            'LogAction',               // Page title
            'LogAction',               // Menu title
            'manage_options',          // Capability
            'logaction',               // Menu slug
            [RenderMenuPages::class, 'renderLogsPage'],  // Callback function
            'dashicons-clipboard',     // Icon URL
            80                         // Position
        );

        add_submenu_page(
            'logaction',               // Parent slug
            'Logs',                    // Page title
            'Logs',                    // Menu title
            'manage_options',          // Capability
            'logaction_logs',          // Submenu slug
            [RenderMenuPages::class, 'renderLogsPage']  // Callback function
        );

        add_submenu_page(
            'logaction',
            'Settings',
            'Settings',
            'manage_options',
            'logaction_settings',
            [RenderMenuPages::class, 'renderSettingsPage']
        );
    }

}
