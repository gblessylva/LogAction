<?php
/**
 * Controller to render Admin Pages.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Controllers;

/**
 * Main class to render pages.
 */
class RenderMenuPages {
	/**
	 * Render the logs page.
	 *
	 * @return void
	 */
	public static function render_logs_page(): void {
		// Include the template for the logs page.
		include plugin_dir_path( __DIR__ ) . '../templates/logs.php';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function render_settings_page(): void {
		include plugin_dir_path( __DIR__ ) . '../templates/settings.php';
	}
}
