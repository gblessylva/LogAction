<?php
/**
 * The Interface for Logger.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Interfaces;

use LogAction\Events\UserEvent;

/**
 * Interface LoggerInterface
 *
 * Defines the contract for logging actions within the LogAction plugin.
 */
interface LoggerInterface {
	/**
	 * Logs an action event.
	 *
	 * @param UserEvent $event The event to be logged.
	 */
	public function log_user_action( UserEvent $event ): void;


	/**
	 * Deletes log entries older than a specified date.
	 *
	 * @param string $date The cutoff date in Y-m-d format.
	 * @return void
	 */
	public function delete_logs_by_date( string $date ): void;

	/**
	 * Deletes old logs by date.
	 *
	 * @param array $log_ids Date of selected delete.
	 */
	public function delete_logs_by_id( array $log_ids ): void;
}
