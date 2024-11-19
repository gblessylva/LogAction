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
	public static function is_allow_users_view_logs_enabled(): bool {
		$is_only_admin_delete = get_option( 'allow_users_view_logs' );
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
		$is_restrict_access = get_option( 'keep_my_log_data' );
		return ! empty( $is_restrict_access ) && $is_restrict_access === '1';
	}

	/**
	 * Check the minimum available roles.
	 *
	 * @param mixed $allowed_roles The allowed role.
	 * @return bool True if log access restriction is enabled, false otherwise.
	 */
	public static function logaction_get_minimum_role( $allowed_roles ) {
		$role_hierarchy = self::logaction_get_role_hierarchy();

		// Set default role to administrator if no roles are allowed.
		if ( empty( $allowed_roles ) ) {
			return 'administrator';
		}

		// Find the role with the lowest hierarchy in allowed roles.
		$min_role = 'administrator';
		foreach ( $allowed_roles as $role ) {
			if ( isset( $role_hierarchy[ $role ] ) && $role_hierarchy[ $role ] < $role_hierarchy[ $min_role ] ) {
				$min_role = $role;
			}
		}

		return $min_role;
	}
	/**
	 * Check if log use meets role hierarchy.
	 *
	 * @param mixed $minimum_role The minimum required role.
	 * @return bool True if log access restriction is enabled, false otherwise.
	 */
	public static function logaction_user_meets_role_hierarchy( $minimum_role ) {
		$role_hierarchy = self::logaction_get_role_hierarchy();
		$user           = wp_get_current_user();

		// Ensure the minimum role exists in the hierarchy.
		if ( ! isset( $role_hierarchy[ $minimum_role ] ) ) {
			return false;
		}

		$min_role_level = $role_hierarchy[ $minimum_role ];

		// Check if the user has a role equal to or higher than the minimum role.
		foreach ( $user->roles as $role ) {
			if ( isset( $role_hierarchy[ $role ] ) && $role_hierarchy[ $role ] >= $min_role_level ) {
				return true;
			}
		}

		return false;
	}
	/**
	 * Assigns user hierarchy by roles.
	 *
	 * @return bool True if log access restriction is enabled, false otherwise.
	 */
	public static function logaction_get_role_hierarchy() {
		// Default hierarchy.
		$default_hierarchy = array(
			'subscriber'    => 1,
			'contributor'   => 2,
			'author'        => 3,
			'editor'        => 4,
			'administrator' => 5,
		);

		/**
		 * Filter: `logaction_role_hierarchy`
		 *
		 * Allows modifying the role hierarchy for LogAction.
		 *
		 * @param array $default_hierarchy The default role hierarchy.
		 * @return array Modified role hierarchy.
		 */
		return apply_filters( 'logaction_role_hierarchy', $default_hierarchy );
	}
}
