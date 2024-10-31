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
}
