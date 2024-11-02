<?php

namespace LogAction\Utilities;

use LogAction\Database\DatabaseHandler;
use WP_Query;

class LogHelper
{
	/**
	 * Sort logs based on the specified parameters.
	 *
	 * @param string $order_by The column to sort by.
	 * @param string $order The order direction (ASC or DESC).
	 * @return string The prepared SQL ORDER BY clause.
	 */
	public static function prepareSort(string $order_by, string $order): string
	{
		// Define valid columns for sorting
		$valid_columns = ['action', 'user_id', 'date', 'description', 'action_id'];

		// Validate the order_by parameter
		if (!in_array($order_by, $valid_columns)) {
			$order_by = 'date'; // Default sorting column
		}

		// Validate the order direction
		if (strtoupper($order) !== 'ASC' && strtoupper($order) !== 'DESC') {
			$order = 'ASC'; // Default sorting order
		}

		return "ORDER BY {$order_by} {$order}";
	}
	/**
 * Get the human-readable description for the action type.
 *
 * @param string $action The action type.
 * @return string The human-readable description of the action.
 */

public static function getReadableAction(string $action): string {
	// Define action descriptions in a static array
	$action_labels = [
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
		'comment_posted'      => 'Comment Posted',
		'plugin_activated'    => 'Activated Plugin',
		'plugin_deactivated'  => 'Deactivated Plugin',
		'plugin_deleted'      => 'Deleted Plugin',
		'theme_switched'      => 'Switched Theme',
		'option_updated'      => 'Updated Option',
		// Add more as needed
	];

	// Allow other plugins to filter and add custom action descriptions
	$action_labels = apply_filters('logaction_readable_actions', $action_labels);

	// Return the human-readable action or a default label for unknown actions
	return $action_labels[$action] ?? 'Unknown Action';
}


	 /**
	 * Convert a date to a human-readable date format.
	 *
	 * @param string $date The date in 'Y-m-d H:i:s' format.
	 * @return string The human-readable date format.
	 */
	public static function formatdate(string $date): string {
		// Convert the date to a DateTime object
		$dateTime = new \DateTime($date);
		// Format it to a human-readable format
		return $dateTime->format('F j, Y, g:i a'); // Example: November 1, 2024, 9:56 am
	}

	/**
	 * Get readable user information based on user ID.
	 *
	 * @param int $userId The user ID.
	 * @return array An associative array containing user information: username, email, full name.
	 */
	public static function getUserInfo(int $userId): array {
		// Get the user object by ID
		$user = get_userdata($userId);

		// Check if the user exists
		if (!$user) {
			return [
				'username' => 'Unknown User',
				'email' => '',
				'full_name' => 'N/A'
			];
		}

		// Retrieve user information
		$fullName = trim($user->first_name . ' ' . $user->last_name);
		if (empty($fullName)) {
			$fullName = 'N/A'; // Default value if full name is not available
		}

		return [
			'username' => $user->user_login,
			'email' => $user->user_email,
			'full_name' => $fullName
		];
	}
	 /**
	* Get an array of months from the log dates.
	*
	* This function retrieves unique months from the log entries,
	* formatted as 'MMYY' => 'Month Year'.
	*
	* @param array $logs Array of log entries.
	* @return array Array of formatted months.
	*/
  public static function get_months_from_logs() {
		$logs = DatabaseHandler::getAllMonths();
		$months = [];
   
	   // Loop through the logs to extract month and year
		foreach ($logs as $log) {
		   $date = strtotime($log->date); // Convert date to timestamp
		   $monthYear = date('my', $date); // Format to 'MMYY'
		   $monthName = date('F Y', $date); // Format to 'Month Year'
   
		   // Check if the monthYear is already in the array
		   if (!isset($months[$monthYear])) {
			   $months[$monthYear] = $monthName; // Add to array if not present
		   }
		}
   
	   return $months;
   }
   
}
