<?php
declare(strict_types=1);

namespace LogAction\Events;

/**
 * Class UserEvent
 *
 * Represents user-related events in the LogAction plugin.
 */
class UserEvent {
	/**
	 * The type of the event.
	 *
	 * @var string
	 */
	private string $event_type;

	/**
	 * ID of the user associated with the event.
	 *
	 * @var int
	 */
	private int $user_id;

	/**
	 * Timestamp of when the event occurred.
	 *
	 * @var string
	 */
	private string $timestamp;

	/**
	 * Description of the event.
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * Action ID related to the event.
	 *
	 * @var int
	 */
	private int $action_id;

	/**
	 * UserEvent constructor.
	 *
	 * @param string $event_type  The type of the event.
	 * @param int    $user_id     The ID of the user associated with the event.
	 * @param string $description  A description of the event.
	 * @param int    $action_id    The action ID related to the event.
	 */
	public function __construct( string $event_type, int $user_id, string $description, ?int $action_id = 0 ) {
		$this->event_type  = $event_type;
		$this->user_id     = $user_id;
		$this->description = $description;
		$this->action_id   = $action_id;
		$this->timestamp   = current_time( 'mysql' );
	}

	/**
	 * Get the event type.
	 *
	 * @return string The type of the event.
	 */
	public function get_event_type(): string {
		return $this->event_type;
	}

	/**
	 * Get the user ID.
	 *
	 * @return int The ID of the user.
	 */
	public function get_user_id(): int {
		return $this->user_id;
	}

	/**
	 * Get the timestamp.
	 *
	 * @return string The timestamp of the event.
	 */
	public function get_time_stamp(): string {
		return $this->timestamp;
	}

	/**
	 * Get the description of the event.
	 *
	 * @return string The description of the event.
	 */
	public function get_log_description(): string {
		return $this->description;
	}

	/**
	 * Get the action ID related to the event.
	 *
	 * @return int The action ID.
	 */
	public function get_log_action_id(): int {
		return $this->action_id;
	}
}
