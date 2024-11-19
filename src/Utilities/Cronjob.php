<?php
/**
 * Handles Cronjobs.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Utilities;

use LogAction\Database\DatabaseHandler;

/**
 * Class to handle Cronjob Activities.
 */
class Cronjob {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		// Only schedule the cron job if the setting is enabled.
		add_action( 'admin_init', array( $this, 'check_and_schedule_log_deletion' ) );

		// Hook into the cron job to execute the deletion task.
		add_action( 'delete_logs_older_than_30_days_cron', array( $this, 'delete_logs_older_than_30_days' ) );

		// Clear the cron job on deactivation if scheduled.
		register_deactivation_hook( __FILE__, array( $this, 'clear_scheduled_event' ) );
	}

	/**
	 * Check if the setting is enabled, and schedule the cron job if it is.
	 */
	public function check_and_schedule_log_deletion() {
		// Check if the 'delete_logs_after_days' setting is enabled.
		$delete_logs_after_days = get_option( 'delete_logs_after_days' );

		if ( $delete_logs_after_days ) {
			// If the setting is enabled, schedule the cron job.
			if ( ! wp_next_scheduled( 'delete_logs_older_than_30_days_cron' ) ) {
				wp_schedule_event( time(), 'daily', 'delete_logs_older_than_30_days_cron' );
			}
		} else {
			// If the setting is not enabled, ensure the cron job is not scheduled.
			$this->clear_scheduled_event();
		}
	}

	/**
	 * Hook the function to delete logs older than 30 days.
	 */
	public function delete_logs_older_than_30_days() {
		// If this is triggered, but we already checked the setting, then proceed to delete logs.
		$delete_logs_after_days = get_option( 'delete_logs_after_days' );

		// Only proceed if the setting is enabled.
		if ( $delete_logs_after_days ) {
			$result = DatabaseHandler::delete_logs_after_30_days();

			if ( $result ) {
				// Failure - Add an admin notice.
				add_action(
					'admin_notices',
					function () {
						echo '<div class="error"><p>' . esc_html__( 'Failed to delete logs older than 30 days.', 'logaction' ) . '</p></div>';
					}
				);
			} else {
				// Success - Add an admin notice.
				add_action(
					'admin_notices',
					function () {
						echo '<div class="updated"><p>' . esc_html__( 'Successfully deleted logs older than 30 days.', 'logaction' ) . '</p></div>';
					}
				);
			}
		}
	}

	/**
	 * Clear the scheduled event.
	 */
	public function clear_scheduled_event() {
		// Clear the scheduled event if it's not needed.
		wp_clear_scheduled_hook( 'delete_logs_older_than_30_days_cron' );
	}
}
