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
	 * Check if only admins can delete logs.
	 *
	 * @return bool True if only admins can delete logs, false otherwise.
	 */
	public static function is_only_admin_delete_logs_enabled(): bool {
		$is_only_admin_delete = get_option( 'only_admin_delete_logs' );
		return ! empty( $is_only_admin_delete ) && $is_only_admin_delete === '1';
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

	/**
	 * Check if log access restriction is enabled.
	 *
	 * @return bool True if log access restriction is enabled, false otherwise.
	 */
	public static function is_log_access_restricted(): bool {
		$is_restrict_access = get_option( 'restrict_log_access' );
		return ! empty( $is_restrict_access ) && $is_restrict_access === '1';
	}

	/**
	 * Check if log access restriction is enabled.
	 *
	 * @return bool True if log access restriction is enabled, false otherwise.
	 */
	public static function is_admin_user() {
		$user = wp_get_current_user();
        return $user->roles;
	}
}
