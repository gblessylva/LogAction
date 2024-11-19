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
use LogAction\Utilities\OptionsHandler;

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
		register_setting( 'logaction_settings', 'keep_my_log_data' );
		register_setting( 'logaction_settings', 'allow_users_view_logs' );
		register_setting( 'logaction_settings', 'user_roles' );
	}
	/**
	 * Adds the LogAction menu and its submenus.
	 */
	public function add_admin_log_menus(): void {
		// Get options to check if users are allowd to view logs.
		$users_can_view_logs = get_option( 'allow_users_view_logs' );

		add_menu_page(
			'LogAction',               // Page title.
			'LogAction',               // Menu title.
			'manage_options',          // Capability.
			'logaction',               // Menu slug.
			array( RenderMenuPages::class, 'render_logs_page' ),  // Callback function.
			'dashicons-clipboard',     // Icon URL.
			80                         // Position.
		);

		if ( ! $users_can_view_logs ) {
			add_submenu_page(
				'logaction',               // Parent slug.
				'Logs of all user actions', // Page title.
				'Logs',                    // Menu title.
				'manage_options',                    // Capability (set to a generic capability).
				'logaction_logs',          // Submenu slug.
				array( RenderMenuPages::class, 'render_logs_page' ) // Callback function.
			);
		} else {
			// Fetch allowed roles from settings.
			$allowed_roles = get_option( 'user_roles', array() );

			// Determine the minimum role allowed based on the settings.
			$minimum_role = OptionsHandler::logaction_get_minimum_role( $allowed_roles );

			// Add submenu only if the user meets the role hierarchy.
			if ( OptionsHandler::logaction_user_meets_role_hierarchy( $minimum_role ) ) {
				add_submenu_page(
					'logaction',               // Parent slug.
					'Logs of all user actions', // Page title.
					'Logs',                    // Menu title.
					'read',                    // Capability (set to a generic capability).
					'logaction_logs',          // Submenu slug.
					array( RenderMenuPages::class, 'render_logs_page' ) // Callback function.
				);
			}
		}
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
