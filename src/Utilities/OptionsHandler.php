<?php
/**
 * Handlles all Setting page Options.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

namespace LogAction\Utilities;

/**
 * Helper Class to Handle all options retrieval.
 */
class OptionsHandler {
	/**
	 * Check if bulk delete is enabled.
	 *
	 * @return bool True if bulk delete is enabled, false otherwise.
	 */
	public static function is_bulk_delete_enabled(): bool {
		$is_bulk_delete = get_option( 'enable_log_deletion' );
		if ( ! empty( $is_bulk_delete ) && $is_bulk_delete === '1' ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Check if logs should be deleted after 30 days.
	 *
	 * @return bool True if logs should be deleted after 30 days, false otherwise.
	 */
	public static function is_delete_logs_after_days_enabled(): bool {
		$is_delete_after_days = get_option( 'delete_logs_after_days' );
		return ! empty( $is_delete_after_days ) && $is_delete_after_days === '1';
	}

	/**
	 * Check if log export is enabled.
	 *
	 * @return bool True if log export is enabled, false otherwise.
	 */
	public static function is_log_export_enabled(): bool {
		$is_log_export = get_option( 'enable_logs_export' );
		return ! empty( $is_log_export ) && $is_log_export === '1';
	}
}
