<?php

declare(strict_types=1);

namespace LogAction\Interfaces;

use LogAction\Events\UserEvent;

/**
 * Interface LoggerInterface
 *
 * Defines the contract for logging actions within the LogAction plugin.
 */
interface LoggerInterface
{
	/**
	 * Logs an action event.
	 *
	 * @param UserEvent $event The event to be logged
	 */
	public function logAction(UserEvent $event): void;


	/**
	 * Retrieves log entries from the system.
	 *
	 * @return array An array of log entries.
	 */
	public function getLogs(): array;

	/**
	 * Deletes log entries older than a specified date.
	 *
	 * @param string $date The cutoff date in Y-m-d format.
	 * @return void
	 */
	public function deleteOldLogs(string $date): void;
}
