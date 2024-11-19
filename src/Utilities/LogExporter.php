<?php
/**
 * The main plugin class for the LogAction - Activity Log Plugin.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Utilities;

use LogAction\Database\DatabaseHandler;
/**
 * Class to handle logs.
 */
class LogExporter {

	/**
	 * Export logs as CSV.
	 *
	 * @return void
	 */
	public function export_logs_to_csv(): void {
		$logs = DatabaseHandler::get_logs_for_export();
		// Set headers for the CSV file.
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="logs_export_' . date( 'Y-m-d_H-i', time() ) . '.csv"' );

		// Open output stream.
		$output = fopen( 'php://output', 'w' );

		// Add column headers to the CSV.
		fputcsv( $output, array( 'ID', 'Action', 'User ID', 'Description', 'Date' ) );
		// Write each log entry    to the CSV.
		foreach ( $logs as $log ) {
			fputcsv(
				$output,
				array(
					$log->id,
					LogHelper::get_readable_action( $log->action ), // Adjust these fields based on your log structure.
					$log->user_id,
					$log->description,
					LogHelper::format_log_date( $log->date ), // Ensure this matches your log's date field.
				)
			);
		}
		fclose( $output );
		exit;
	}
}
