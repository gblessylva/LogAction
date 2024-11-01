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
		switch ($action) {
			case 'post_published':
				return 'Published Post';
			case 'login':
				return 'User Login';
			case 'logout':
				return 'User Logout';
			case 'post_updated':
				return 'Updated Post';
			case 'register':
				  return 'Updated Post';
			// Add more cases as needed
			default:
				return 'Unknown Action'; // Default case for unknown actions
		}
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
