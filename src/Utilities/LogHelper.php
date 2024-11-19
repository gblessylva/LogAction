<?php
/**
 * The main plugin class for the LogAction Helpers.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

namespace LogAction\Utilities;

use LogAction\Database\DatabaseHandler;
use WP_Query;

/**
 * Utility class for logs.
 */
class LogHelper {
	/**
	 * Sort logs based on the specified parameters.
	 *
	 * @param string $order_by The column to sort by.
	 * @param string $order The order direction (ASC or DESC).
	 * @return string The prepared SQL ORDER BY clause.
	 */
	public static function prepare_sort( string $order_by, string $order ): string {
		// Define valid columns for sorting.
		$valid_columns = array( 'action', 'user_id', 'date', 'description', 'action_id' );
		// Validate the order_by parameter.
		if ( ! in_array( $order_by, $valid_columns ) ) {
			$order_by = 'date'; // Default sorting column.
		}
		// Validate the order direction.
		if ( strtoupper( $order ) !== 'ASC' && strtoupper( $order ) !== 'DESC' ) {
			$order = 'ASC'; // Default sorting order.
		}

		return "ORDER BY {$order_by} {$order}";
	}
	/**
	 * Get the human-readable description for the action type.
	 *
	 * @param string $action The action type.
	 * @return string The human-readable description of the action.
	 */
	public static function get_readable_action( string $action ): string {
		// Define action descriptions in a static array.
		$action_labels = array(
			'post_published'      => 'Published Post',
			'login'               => 'User Login',
			'logout'              => 'User Logout',
			'post_updated'        => 'Updated Post',
			'register'            => 'User Registration',
			'login_failed'        => 'Failed Login Attempt',
			'password_retrieve'   => 'Password Retrieval Request',
			'user_deleted'        => 'User Deletion',
			'post_trashed'        => 'Trashed Post',
			'page_trashed'        => 'Trashed Page',
			'post_type_trashed'   => 'Trashed Custom Post Type',
			'post_untrashed'      => 'Restored Post',
			'page_untrashed'      => 'Restored Page',
			'post_type_untrashed' => 'Restored Custom Post Type',
			'post_deleted'        => 'Deleted Post',
			'post_type_deleted'   => 'Deleted Custom Post Type',
			'page_deleted'        => 'Deleted Page',
			'page_published'      => 'Published Page',
			'post_type_published' => 'Custom Post Type Published',
			'comment_posted'      => 'Comment Posted',
			'plugin_activated'    => 'Activated Plugin',
			'plugin_deactivated'  => 'Deactivated Plugin',
			'plugin_deleted'      => 'Deleted Plugin',
			'theme_switched'      => 'Switched Theme',
			'option_updated'      => 'Updated Option',
			// Add more as needed.
		);

		// Allow other plugins to filter and add custom action descriptions.
		$action_labels = apply_filters( 'logaction_readable_actions', $action_labels );

		// Return the human-readable action or a default label for unknown actions.
		return $action_labels[ $action ] ?? 'Unknown Action';
	}


	/**
	 * Convert a date to a human-readable date format.
	 *
	 * @param string $date The date in 'Y-m-d H:i:s' format.
	 * @return string The human-readable date format.
	 */
	public static function format_log_date( string $date ): string {
		// Convert the date to a DateTime object.
		$date_time = new \DateTime( $date );
		// Format it to a human-readable format.
		return $date_time->format( 'F j, Y, g:i a' ); // Example: November 1, 2024, 9:56 am.
	}

	/**
	 * Get readable user information based on user ID.
	 *
	 * @param int $user_id The user ID.
	 * @return array An associative array containing user information: username, email, full name.
	 */
	public static function get_user_info( int $user_id ): array {
		// Get the user object by ID.
		$user = get_userdata( $user_id );

		// Check if the user exists.
		if ( ! $user ) {
			return array(
				'username'  => 'Unknown User',
				'email'     => '',
				'full_name' => 'N/A',
				'avatar'    => 'https://via.placeholder.com/150',
			);
		}

		// Retrieve user information.
		$full_name = trim( $user->first_name . ' ' . $user->last_name );
		if ( empty( $full_name ) ) {
			$full_name = $user->user_login; // Default value if full name is not available.
		}

		$avatar_url = get_avatar_url( $user_id, array( 'size' => 150 ) );
		if ( ! $avatar_url ) {
			$avatar_url = 'https://via.placeholder.com/150'; // Fallback placeholder avatar.
		}

		return array(
			'username'  => $user->user_login,
			'email'     => $user->user_email,
			'full_name' => $full_name,
			'avatar'    => $avatar_url,
		);
	}
	/**
	 *  Get an array of months from the log dates.
	 *  This function retrieves unique months from the log entries,
	 *  formatted as 'MMYY' => 'Month Year'.
	 *
	 * @return array Array of formatted months.
	 */
	public static function get_months_from_logs() {
		$logs   = DatabaseHandler::get_all_months();
		$months = array();
		foreach ( $logs as $log ) {
			$date       = strtotime( $log->date ); // Convert date to timestamp.
			$month_year = date( 'my', $date ); // Format to 'MMYY'.
			$month_name = date( 'F Y', $date ); // Format to 'Month Year'.
			// Check if the month_year is already in the array.
			if ( ! isset( $months[ $month_year ] ) ) {
				$months[ $month_year ] = $month_name; // Add to array if not present.
			}
		}
		return $months;
	}
}
