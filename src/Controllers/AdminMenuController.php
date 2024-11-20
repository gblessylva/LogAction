<?php
/**
 * Controller to  Admin Menus.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

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
	public function register_log_menus(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_log_menus' ) );
		add_action( 'admin_init', array( $this, 'logaction_register_settings' ) );
	}

	/**
	 * Register the menu and submenus in the WordPress admin.
	 */
	public function logaction_register_settings() {
		register_setting( 'logaction_settings', 'enable_log_deletion' );
		register_setting( 'logaction_settings', 'delete_logs_after_days' );
		register_setting( 'logaction_settings', 'enable_logs_export' );
	}
	/**
	 * Adds the LogAction menu and its submenus.
	 */
	public function add_admin_log_menus(): void {

		add_menu_page(
			'LogAction',               // Page title.
			'LogAction',               // Menu title.
			'manage_options',          // Capability.
			'logaction',               // Menu slug.
			array( RenderMenuPages::class, 'render_logs_page' ),  // Callback function.
			'dashicons-clipboard',     // Icon URL.
			80                         // Position.
		);
		add_submenu_page(
			'logaction',               // Parent slug.
			'Logs of all user actions', // Page title.
			'Logs',                    // Menu title.
			'manage_options',                    // Capability (set to a generic capability).
			'logaction_logs',          // Submenu slug.
			array( RenderMenuPages::class, 'render_logs_page' ) // Callback function.
		);
		add_submenu_page(
			'logaction',
			'Settings page to configure all actions',
			'Settings',
			'manage_options',
			'logaction_settings',
			array( RenderMenuPages::class, 'render_settings_page' )
		);
	}
}
