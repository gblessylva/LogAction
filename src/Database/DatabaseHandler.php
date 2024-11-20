<?php
/**
 *
 * Handles the database operations for the LogAction plugin.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0
 */

declare(strict_types=1);

namespace LogAction\Database;

use DateTime;
use DateTimeZone;
use wpdb;
use Exception;
use LogAction\Utilities\Configs;

/**
 * Class DatabaseHandler
 */
class DatabaseHandler {

	/**
	 * Private Static
	 *
	 * @var string $table_name
	 */
	private static $table_name;

	/**
	 * DatabaseHandler constructor.
	 * Creates the logging table when the class is instantiated.
	 */
	public function __construct() {
		self::create_logaction_table();
	}

	/**
	 * Creates the logs table in the database.
	 *
	 * @return void
	 * @throws Exception If the table creation fails.
	 */
	public static function create_logaction_table(): void {
		global $wpdb;

		// Define the table name and charset collate.
		$table_name      = Configs::$logaction_table;
		$charset_collate = $wpdb->get_charset_collate();

		// Correct SQL query.
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			action varchar(255) NOT NULL,
			user_id bigint(20) NOT NULL,
			action_id bigint(20),
			date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			description text NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";

		// Include the WordPress upgrade library.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		try {
			// Execute the query using dbDelta.
			dbDelta( $sql );
		} catch ( Exception $e ) {
			// Handle exception.
			throw new Exception( 'Failed to create database table for logging actions: ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Deletes the logs table from the database.
	 *
	 * @return void
	 * @throws Exception If the table deletion fails.
	 */
	public static function delete_logaction_table(): void {
		global $wpdb;
		$sql = 'DROP TABLE IF EXISTS ' . Configs::$logaction_table . ';';
		try {
			$wpdb->query( $sql );
		} catch ( Exception $e ) {
			throw new Exception( 'Failed to delete database table for logging actions.' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Checks if a log entry with the same action and action_id exists.
	 *
	 * @param string $action The action type to check, e.g., 'post_published'.
	 * @param int    $action_id The action ID to check (e.g., post ID).
	 * @return bool True if an entry exists, otherwise false.
	 */
	public static function check_if_action_exists( string $action, int $action_id ): bool {
		global $wpdb;
		$table = esc_sql( Configs::$logaction_table );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM {%f} WHERE action = %s AND action_id = %d',
				$table,
				$action,
				$action_id
			)
		);

		return $count > 0;
	}

	/**
	 * Inserts log data into the database.
	 *
	 * @param array $data The data to insert into the log table.
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	public static function insert_log( array $data ): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert( Configs::$logaction_table, $data );
	}

	/**
	 * Get all logs months from table.
	 */
	public static function get_all_months() {
		global $wpdb;

		$table  = esc_sql( Configs::$logaction_table );
		$months = $wpdb->get_results( "SELECT date FROM $table" );
		return $months;
	}

	/**
	 * Get all logs from the database table.
	 *
	 * @param string $order_clause   SQL clause to order results (e.g., 'ORDER BY date DESC').
	 * @param int    $logs_per_page  Number of logs per page to fetch.
	 * @param int    $offset         Offset for pagination.
	 * @return array                 Array containing the logs and total log count.
	 */
	public static function get_all_logs( $order_clause, $logs_per_page, $offset ) {
		global $wpdb;
		$table = esc_sql( Configs::$logaction_table );
		// Attempt to get cached results.
		$cache_key = 'logs_' . md5( "{$order_clause}_{$logs_per_page}_{$offset}" );
		$logs      = wp_cache_get( $cache_key );

		if ( false === $logs ) {
			// Fetch results if not cached.
			$logs = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $table {$order_clause}  LIMIT %d OFFSET %d",
					$logs_per_page,
					$offset
				)
			);
			// Cache the results.
			wp_cache_set( $cache_key, $logs, '', 3600 ); // Cache for 1 hour.
		}

		$total = self::get_logs_count();

		return array(
			'logs'  => $logs,
			'total' => $total,
		);
	}


	/**
	 * Get all logs count table.
	 *
	 * @return $months
	 */
	public static function get_logs_count() {
		global $wpdb;

		$table_name = \esc_sql( Configs::$logaction_table );
		$count      = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name " );
		return $count;
	}
	/**
	 * Generates a date filter SQL condition based on the month parameter.
	 *
	 * @param string $month_param The month parameter in "YYYYMM" format.
	 * @return string SQL condition for filtering by date.
	 */
	public static function get_date_filter( $month_param ): string {
		if ( empty( $month_param ) || $month_param === '0' ) {
			return ''; // No filter if "All dates" is selected.
		}
		// Extract year and month from the parameter.
		$year  = substr( sanitize_text_field( $month_param ), 2, 2 );
		$month = substr( sanitize_text_field( $month_param ), 0, 2 );

		// Calculate the start date for the specified month.
		$start_date = "20{$year}-{$month}-01 00:00:00";
		$timestamp  = strtotime( $start_date );
		if ( false === $timestamp ) {
			// If the date is invalid, return an empty string to skip the filter.
			return '';
		}
		$end_date_utc = gmdate( 'Y-m-t 23:59:59', $timestamp );

		// Create a DateTime object from the UTC date.
		$end_date = new DateTime( $end_date_utc, new DateTimeZone( 'UTC' ) );

		// Convert to the desired timezone (WordPress site timezone).
		$site_timezone = get_option( 'timezone_string' ) ?: 'UTC';
		$end_date->setTimezone( new DateTimeZone( $site_timezone ) );

		// Format the date back to string if necessary.
		$localized_end_date = $end_date->format( 'Y-m-t H:i:s' );
		// Return the SQL date filter condition.
		return "AND date >= '{$start_date}' AND date <= '{$localized_end_date}'";
	}

	/**
	 * Function to get logs by month
	 *
	 * @param string $month_param the month param.
	 * @param string $order_clause the order clause param.
	 * @param string $logs_per_page the number of logs per page.
	 * @param string $offset page offset.
	 * @return array
	 */
	public static function get_logs_by_month( $month_param, $order_clause, $logs_per_page, $offset ): array {
		global $wpdb;
		// Extract year and month from the parameter.
		$year  = substr( sanitize_text_field( $month_param ), 2, 2 );
		$month = substr( sanitize_text_field( $month_param ), 0, 2 );
		// Calculate start and end date for the month.
		$start_date = "20{$year}-{$month}-01 00:00:00";
		$timestamp  = strtotime( $start_date );
		if ( false === $timestamp ) {
			// If the date is invalid, return an empty string to skip the filter.
			return '';
		}
		$end_date = date( 'Y-m-t 23:59:59', strtotime( $start_date ) );
		// Prepare the SQL query with date range filter, ordering, and pagination.
		$table = Configs::$logaction_table;
		$query = $wpdb->prepare(
			"SELECT * FROM {$table} WHERE date >= %s AND date <= %s {$order_clause} LIMIT %d OFFSET %d",
			$start_date,
			$end_date,
			$logs_per_page,
			$offset
		);

		// Execute the query and return the results.
		$logs   = $wpdb->get_results( $query );
		$count  = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE date >= %s AND date <= %s",
				$start_date,
				$end_date
			)
		);
		$total  = $count;
		$result = array(
			'logs'  => $logs,
			'total' => $total,
		);

		return $result;
	}


	/**
	 * A fucntion to get all logs for export
	 *
	 * @return array
	 */
	public static function get_logs_for_export(): array {
		global $wpdb;
		$table = Configs::$logaction_table;
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( \wp_unslash( $_GET['_wpnonce'] ) ), 'export_logs_nonce' ) ) {
			wp_create_nonce( 'export_logs_nonce' );
		}

		// Nounce already checked on export_all_logs ajax function.
		$month_param = isset( $_GET['m'] ) ? sanitize_text_field( \wp_unslash( $_GET['m'] ) ) : null;

		if ( $month_param ) {
			$year  = substr( sanitize_text_field( $month_param ), 2, 2 );
			$month = substr( sanitize_text_field( $month_param ), 0, 2 );
			// Calculate start and end date for the month.
			$start_date = "20{$year}-{$month}-01 00:00:00";
			$timestamp  = strtotime( $start_date );
			if ( false === $timestamp ) {
				return array();
			}
			$end_date = date( 'Y-m-t 23:59:59', strtotime( $start_date ) );
			// Prepare and execute query.
			$logs = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE date >= %s AND date <= %s",
					$start_date,
					$end_date
				)
			);
		} else {
			$logs = $wpdb->get_results( "SELECT * FROM {$table}" );
		}

		return $logs;
	}
	/**
	 * A function to delete all logs based on the selected array of log IDs.
	 *
	 * @param array $logs_id An array of selected log IDs.
	 * @return int|false The number of rows deleted, or false on failure.
	 */
	public static function delete_logs_by_id( array $logs_id ) {
		global $wpdb;

		// Ensure $logs_id is not empty.
		if ( empty( $logs_id ) ) {
			return false; // Nothing to delete.
		}

		$table = Configs::$logaction_table;

		// Sanitize the IDs to prevent SQL injection.
		$placeholders = implode( ',', array_fill( 0, count( $logs_id ), '%d' ) );

		// Prepare the query.
		$query = $wpdb->prepare(
			"DELETE FROM {$table} WHERE id IN ({$placeholders})",
			$logs_id
		);

		// Execute the query.
		$result = $wpdb->query( $query );

		// Return the number of rows deleted, or false on failure.
		return $result;
	}

	/**
	 * A function to delete all logs. Does not drop the table.
	 *
	 * @return int|false The number of rows deleted, or false on failure.
	 */
	public static function delete_all_logs() {
		global $wpdb;

		$table = Configs::$logaction_table;

		// Check if the table exists.
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
			return false; // Table does not exist.
		}

		// Prepare and execute the DELETE query.
		$query = "DELETE FROM {$table}";

		$result = $wpdb->query( $query );

		// Return the number of rows deleted, or false on failure.
		return $result;
	}

	/**
	 * A function to delete all logs after 30 Days. Does not drop the table.
	 *
	 * @return int|false The number of rows deleted, or false on failure.
	 */
	public static function delete_logs_after_30_days() {
		global $wpdb;

		$table = Configs::$logaction_table;

		// Calculate the date 30 days ago.
		$date_30_days_ago = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );

		// Prepare the query to delete logs older than 30 days.
		$query = $wpdb->prepare(
			"DELETE FROM {$table} WHERE log_date < %s",
			$date_30_days_ago
		);

		// Execute the query.
		$result = $wpdb->query( $query );

		// Return the number of rows deleted, or false on failure.
		return $result;
	}
}
