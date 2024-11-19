<?php
/**
 * The Interface for Admin Menu.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Interfaces;

/**
 * Interface AdminMenuInterface
 *
 * Defines a contract for classes that add admin menus.
 */
interface AdminMenuInterface {

	/**
	 * Register the menu and submenus.
	 */
	public function register_log_menus(): void;

	/**
	 * Add the main menu and submenus in the WordPress admin dashboard.
	 */
	public function add_admin_log_menus(): void;
}
