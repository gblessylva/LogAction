<?php
/**
 * The Interface for Admin Menu.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Controllers;

use LogAction\Database\DatabaseHandler;
use LogAction\Interfaces\LoggerInterface;
use LogAction\Events\UserEvent;

/**
 * Log Controller Class to implement LoggerInterface
 */
class LogController implements LoggerInterface {
	/**
	 * Implements an action event.
	 *
	 * @param UserEvent $event The event to be logged.
	 */
	public function log_user_action( UserEvent $event ): void {
		$action      = $event->get_event_type();
		$action_id   = $event->get_log_action_id();
		$date        = $event->get_time_stamp();
		$user_id     = $event->get_user_id();
		$description = $event->get_log_description();
		if ( 'post_published' === $action && null !== $action_id ) {
			$exists = DatabaseHandler::check_if_action_exists( $action, $action_id );
			if ( $exists ) {
				return; // Exit if the log already exists.
			}
		}
		if ( 'post_published' === $action && null !== $action_id ) {
			$exists = DatabaseHandler::check_if_action_exists( $action, $action_id );
			if ( $exists ) {
				return; // Exit if the log already exists.
			}
		}
		if ( 'post_type_published' === $action && null !== $action_id ) {
			$exists = DatabaseHandler::check_if_action_exists( $action, $action_id );
			if ( $exists ) {
				return; // Exit if the log already exists.
			}
		}
		$data = array(
			'action'      => $action,
			'user_id'     => $user_id,
			'description' => $description,
			'action_id'   => $action_id,
			'date'        => $date,
		);
		DatabaseHandler::insert_log( $data );
	}
	/**
	 * Deletes old logs by date.
	 *
	 * @param string $date Date of selected delete.
	 */
	public function delete_logs_by_date( string $date ): void {
		DatabaseHandler::delete_logaction_table();
	}

	/**
	 * Deletes old logs by IDS.
	 *
	 * @param array $log_ids IDS of selected delete.
	 */
	public function delete_logs_by_id( array $log_ids ): void {
		DatabaseHandler::delete_logs_by_id( $log_ids );
	}
}
