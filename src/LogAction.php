<?php
/**
 * The main plugin class for the LogAction - Activity Logs for Admin.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction;

use Exception;
use LogAction\Controllers\AdminMenuController;
use LogAction\Database\DatabaseHandler;
use LogAction\Utilities\EventLogger;
use LogAction\Hooks\LoadAdminScripts;

/**
 * Class LogAction.
 */
class LogAction {
	/**
	 * The singleton instance of the LogAction class.
	 *
	 * @var LogAction|null
	 */
	private static ?LogAction $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
	}

	/**
	 * Returns the singleton instance of the LogAction class.
	 *
	 * @return LogAction
	 */
	public static function get_logaction_instance(): LogAction {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Activates the plugin, called on activation.
	 *
	 * @return void
	 * @throws Exception If the table creation fails.
	 */
	public static function activate(): void {
		DatabaseHandler::create_logaction_table();
	}

	/**
	 * Deactivates the plugin, called on deactivation.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		DatabaseHandler::delete_logaction_table();
	}

	/**
	 * Initializes the plugin's functionalities.
	 *
	 * @return void
	 */
	public function init(): void {
		// add_action('init', [$this, 'setup_hooks']);.
		new EventLogger();
		new LoadAdminScripts();
		$admin_menu_controller = new AdminMenuController();
		$admin_menu_controller->register_log_menus();
	}

	/**
	 * Sets up hooks for event logging.
	 *
	 * @return void
	 */
	public function setup_hooks(): void {
	}
}
