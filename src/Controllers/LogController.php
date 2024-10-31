<?php

declare(strict_types=1);

namespace LogAction\Controllers;

use LogAction\Database\DatabaseHandler;
use LogAction\Interfaces\LoggerInterface;
use LogAction\Events\UserEvent;

class LogController implements LoggerInterface
{
	public function logAction(UserEvent $event): void
	{
		$action =   $event->get_event_type();
		$actionId = $event->get_log_action_id();
		$date =     $event->get_time_stamp();
		$user_id =  $event->get_user_id();
		$description = $event->get_log_description();

		

		if ($action === 'post_published' && $actionId !== null) {
			$exists = DatabaseHandler::checkIfActionExists($action, $actionId);
			if ($exists) {
				return; // Exit if the log already exists
			}
		}

		$data = [
			'action' => $action,
			'user_id' => $user_id,
			'description' => $description,
			'action_id'   => $actionId,
			'date' => $date // Get current time in MySQL format
		];
		DatabaseHandler::insertLog($data);
	   
	}
	public function logNewAction(): void{
		global $wpdb;
		
		$tableName = DatabaseHandler::$tableName;

		$wpdb->insert(
			$tableName,
			[
				'action' => 'post_published',
				'user_id' => 1,
				'description' => 'Here is a description',
				'date' => current_time('mysql')// Get current time in MySQL format
			]
		);
	}


	public function getLogs(): array
	{
		global $wpdb;

		$tableName = DatabaseHandler::$tableName;

		return $wpdb->get_results("SELECT * FROM $tableName ORDER BY date_time DESC", ARRAY_A);
	}

	public function deleteOldLogs(string $date): void
	{
		global $wpdb;

		$tableName = DatabaseHandler::$tableName;

		$wpdb->delete($tableName, ['date_time <' => $date]);
	}


}
