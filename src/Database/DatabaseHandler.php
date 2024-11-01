<?php

declare(strict_types=1);

namespace LogAction\Database;

use wpdb;
use Exception;

/**
 * Class DatabaseHandler
 *
 * Handles the database operations for the LogAction plugin.
 */
class DatabaseHandler
{
	private const TABLE_NAME = 'logaction_logs';
	/**
	 * The name of the database table for storing logs.
	 *
	 * @var string
	 */
	public static string $tableName = 'wp_logaction_logs';


	/**
	 * DatabaseHandler constructor.
	 * Creates the logging table when the class is instantiated.
	 */
	public function __construct()
	{
		$this->createTable();
	}

	/**
	 * Creates the logs table in the database.
	 *
	 * @return void
	 * @throws Exception If the table creation fails.
	 */
	public static function createTable(): void
	{
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			action varchar(255) NOT NULL,
			user_id bigint(20) NOT NULL,
			action_id bigint(20),
			date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			description text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		try{
			dbDelta($sql);
		}catch(Exception $e){
			error_log('Database table creation failed: ' . $e->getMessage());
			throw new Exception('Failed to create database table for logging actions.');
   
		}
		
	}

	/**
	 * Deletes the logs table from the database.
	 *
	 * @return void
	 * @throws Exception If the table deletion fails.
	 */
	public static function deleteTable(): void
	{
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$sql = "DROP TABLE IF EXISTS $table_name;";
		try{ 
			$wpdb->query($sql);
		}catch(Exception $e){
			error_log('Database table deletion failed: ' . $e->getMessage());
			throw new Exception('Failed to delete database table for logging actions.');
   
		}
	   
	}

	 /**
	 * Checks if a log entry with the same action and action_id exists.
	 *
	 * @param string $action The action type to check, e.g., 'post_published'.
	 * @param int $actionId The action ID to check (e.g., post ID).
	 * @return bool True if an entry exists, otherwise false.
	 */
	public static function checkIfActionExists(string $action, int $actionId): bool
	{
		global $wpdb;
		$table = self::$tableName;

		$query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE action = %s AND action_id = %d",
			$action,
			$actionId
		);

		$count = (int) $wpdb->get_var($query);

		return $count > 0;
	}

	/**
	 * Inserts a log entry into the database.
	 *
	 * @param array $data Associative array of log data.
	 * @return void
	 */
	public static function insertLog(array $data): void
	{
		global $wpdb;
		$table = self::$tableName;

		$wpdb->insert($table, $data);
	}

	/**
	 * Get all logs months from table.
	 *
	 * @param array $data Associative array of log data.
	 * @return $months
	 */
	public static function getAllMonths(){
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$months = $wpdb->get_results("SELECT date FROM $table_name");
		return $months;
	}	

	/**
	 * Get all logs from table.
	 *
	 * @param array $data Associative array of log data.
	 * @return $months
	 */
	public static function getAllLogs($order_clause, $logs_per_page, $offset) {
		global $wpdb;
	
		$table_name = $wpdb->prefix . self::TABLE_NAME;
	
		// Add date filter condition if provided
		$query = "SELECT * FROM $table_name {$order_clause} LIMIT $logs_per_page OFFSET $offset";

		$logs = $wpdb->get_results($query);
		
		$total = self::getLogsCount();
		
		return ['logs'=>$logs, 'total'=>$total];
	}
		

	/**
	 * Get all logs count table.
	 *
	 * @param array $data Associative array of log data.
	 * @return $months
	 */
	public static function getLogsCount( ){
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
		return $count;
	}
	 /**
     * Generates a date filter SQL condition based on the month parameter.
     *
     * @param string $month_param The month parameter in "YYYYMM" format.
     * @return string SQL condition for filtering by date.
     */
    public static function getDateFilter($month_param) {
		if (empty($month_param) || $month_param === '0') {
			return ''; // No filter if "All dates" is selected.
		}
	
		// Extract year and month from the parameter
		$year = substr(sanitize_text_field($month_param), 2, 2);
		$month = substr(sanitize_text_field($month_param), 0, 2);
		
	
		// Calculate the start date for the specified month
		$start_date = "20{$year}-{$month}-01 00:00:00";
		$timestamp = strtotime($start_date);
		if ($timestamp === false) {
			// If the date is invalid, return an empty string to skip the filter
			return '';
		}
	
		// Calculate the end date based on the start date
		$end_date = date("Y-m-t 23:59:59", $timestamp);
	
		// Return the SQL date filter condition
		return "AND date >= '{$start_date}' AND date <= '{$end_date}'";
	}


	public static function getLogsByMonth($month_param, $order_clause, $logs_per_page, $offset) {
		global $wpdb;
	
		// Extract year and month from the parameter
		$year = substr(sanitize_text_field($month_param), 2, 2);
		$month = substr(sanitize_text_field($month_param), 0, 2);
		// Calculate start and end date for the month
		$start_date = "20{$year}-{$month}-01 00:00:00";
		$timestamp = strtotime($start_date);
		if ($timestamp === false) {
			// If the date is invalid, return an empty string to skip the filter
			return '';
		}
		$end_date = date("Y-m-t 23:59:59", strtotime($start_date));
	
		// Prepare the SQL query with date range filter, ordering, and pagination
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE date >= %s AND date <= %s {$order_clause} LIMIT %d OFFSET %d",
			$start_date,
			$end_date,
			$logs_per_page,
			$offset
		);
	
		// Execute the query and return the results
		$logs = $wpdb->get_results($query);
		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_name} WHERE date >= %s AND date <= %s",
			$start_date,
			$end_date
		));
		$total = $count;
		$result = ['logs'=>$logs, 'total'=>$total ];
	
		return $result;
	}
	
	
}
