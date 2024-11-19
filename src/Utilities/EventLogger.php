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

use LogAction\Controllers\LogController;
use LogAction\Events\UserEvent;
/**
 * Class that handles all events logging.
 */
class EventLogger {
	/**
	 * Private variable
	 *
	 * @var LogController
	 */
	private $log_controller;

	/**
	 * Constructor to register hooks
	 */
	public function __construct() {
		$this->log_controller = new LogController();
		$this->register_hooks();
	}
	/**
	 * Register Hooks Method
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'wp_login', array( $this, 'log_user_login' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'log_user_logout' ) );
		add_action( 'user_register', array( $this, 'log_user_registration' ), 10, 1 );
		add_action( 'wp_login_failed', array( $this, 'log_login_failed' ) );
		add_action( 'retrieve_password', array( $this, 'log_retrieve_password' ) );
		add_action( 'delete_user', array( $this, 'log_user_deletion' ), 10, 1 );

		add_action( 'delete_user', array( $this, 'log_user_deletion' ), 10, 1 );

		add_action( 'trash_post', array( $this, 'log_post_trashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'log_post_untrashed' ), 10, 1 );
		add_action( 'delete_post', array( $this, 'log_post_deleted' ), 10, 1 );
		add_action( 'save_post', array( $this, 'log_post_published' ), 10, 2 );

		add_action( 'activated_plugin', array( $this, 'log_activated_plugin' ), 10, 2 );
		add_action( 'deactivated_plugin', array( $this, 'log_deactivated_plugin' ), 10, 2 );
		add_action( 'deleted_plugin', array( $this, 'log_deleted_plugin' ), 10, 1 );
		add_action( 'switch_theme', array( $this, 'log_switch_theme' ), 10, 2 );
	}

	/**
	 * Handles logging all login activities.
	 *
	 * @param string   $user_login user login.
	 * @param \WP_User $user the WordPress user object.
	 * @return void
	 */
	public function log_user_login( string $user_login, \WP_User $user ): void {
		try {
			$event = new UserEvent(
				'login',
				(int) $user->ID,
				'User logged in successfully'
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
			wp_die( 'LogAction Plugin: Failed to log user logout info - ' . \esc_html( $e->getMessage() ), 'LogAction Error' );
		}
	}

	/**
	 * Handles logging all logout activities.
	 *
	 * @return void
	 */
	public function log_user_logout(): void {
		$user = wp_get_current_user();
		if ( $user->ID ) {
			try {
				$event = new UserEvent(
					'logout',
					(int) $user->ID,
					'User logged out successfully',
					$$user->ID
				);
				$this->log_controller->log_user_action( $event );
			} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log user login info - ' . \esc_html( $e->getMessage() ) );
			}
		}
	}
	/**
	 * Handles logging all User registration activities.
	 *
	 * @param int $user_id User Id Param.
	 * @return void
	 */
	public function log_user_registration( int $user_id ): void {
		try {
			$event = new UserEvent(
				'register',
				$user_id,
				'New user registered',
				$user_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
			wp_die( 'LogAction Plugin: Failed to log user registration info - ' . \esc_html( $e->getMessage() ) );
		}
	}
	/**
	 * Handles logging all Faileds user login activities.
	 *
	 * @param int $user_id User Id Param.
	 * @return void
	 */
	public function log_login_failed( int $user_id ): void {
		try {
			$event = new UserEvent(
				'login_failed',
				$user_id,
				'User Attempted to login but failed',
				$user_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
			wp_die( 'LogAction Plugin: Failed to failed login info - ' . \esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Handles logging all retrieved passowrd activities.
	 *
	 * @param int $user_id User Id Param.
	 * @return void
	 */
	public function log_retrieve_password( int $user_id ): void {
		try {
			$event = new UserEvent(
				'password_retrieve',
				$user_id,
				'User Requested password Retrieval',
				$user_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log retrieve password info - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Handles logging all User Deletion activities.
	 *
	 * @param int $user_id User Id Param.
	 * @return void
	 */
	public function log_user_deletion( int $user_id ): void {
		$current_user = wp_get_current_user();
		try {
			$event = new UserEvent(
				'user_deleted',
				$current_user->ID,
				$current_user->user_login . ' Deleted user with id: ' . $user_id,
				$user_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log user login info - ' . esc_html( $e->getMessage() ) );
		}
	}


	/**
	 * Log post published action.
	 *
	 * @param int $post_id The post id.
	 */
	public function log_post_published( int $post_id ): void {
		$post       = get_post( $post_id );
		$author_id  = $post->post_author;
		$post_title = $post->post_title;
		$post_type  = $post->post_type;
		$event_type = '';
		if ( null === $post_type ) {
			return; // Exit if post is not found.
		}
		if ( 'publish' !== $post->post_status || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( 'post' === $post_type ) {
			$event_type = 'post_published';
		} elseif ( 'page' === $post_type ) {
			$event_type = 'page_published';
		} else {
			$event_type = 'post_type_published';
		}
		try {
			$event = new UserEvent(
				$event_type,
				(int) $author_id,
				"Published New {$post->post_type } 
						$post_title (ID: $post_id)",
				$post_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post publication - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log post Trashed action.
	 *
	 * @param int $post_id The id of the post.
	 */
	public function log_post_trashed( int $post_id ): void {
		$post       = get_post( $post_id );
		$author_id  = $post->post_author;
		$post_title = $post->post_title;
		$post_type  = $post->post_type;
		$event_type = '';
		if ( null === $post ) {
			return; // Exit if post is not found.
		}

		if ( 'post' === $post_type ) {
			$event_type = 'post_published';
		} elseif ( 'page' === $post_type ) {
			$event_type = 'page_published';
		} else {
			$event_type = 'post_type_published';
		}
		try {
			$event = new UserEvent(
				$event_type,
				(int) $author_id,
				"Trashed {$post->post_type } ' '
						$post_title (ID: $post_id)",
				$post_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log post Trashed action.
	 *
	 * @param int $post_id The post ID.
	 * return void.
	 */
	public function log_post_untrashed( int $post_id ): void {
		$post       = get_post( $post_id );
		$author_id  = $post->post_author;
		$post_title = $post->post_title;
		$post_type  = $post->post_type;
		$event_type = '';
		if ( null === $post ) {
			return; // Exit if post is not found.
		}

		if ( 'post' === $post_type ) {
			$event_type = 'post_published';
		} elseif ( 'page' === $post_type ) {
			$event_type = 'page_published';
		} else {
			$event_type = 'post_type_published';
		}
		try {
			$event = new UserEvent(
				$event_type,
				(int) $author_id,
				"Restored {$post->post_type } ' '
						 $post_title (ID: $post_id) from trash",
				$post_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log post Trashed action.
	 *
	 * @param int $post_id The post ID.
	 */
	public function log_post_deleted( int $post_id ): void {
		$post       = get_post( $post_id );
		$author_id  = $post->post_author;
		$post_title = $post->post_title;
		$post_type  = $post->post_type;
		$event_type = '';
		if ( null === $post ) {
			return; // Exit if post is not found.
		}

		if ( 'post' === $post_type ) {
			$event_type = 'post_published';
		} elseif ( 'page' === $post_type ) {
			$event_type = 'page_published';
		} else {
			$event_type = 'post_type_published';
		}
		try {
			$event = new UserEvent(
				$event_type,
				(int) $author_id,
				"Permanently  Deleted {$post->post_type } ' '
						 $post_title (ID: $post_id) ",
				$post_id
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Method to log all comments.
	 *
	 * @param integer $comment_id The comment ID.
	 * @param string  $comment_status The comment Status.
	 *
	 * @return void
	 */
	public function log_comment_posted( int $comment_id, string $comment_status ): void {
		if ( 'approve' === $comment_status ) {
			$comment = get_comment( $comment_id );
			$event   = new UserEvent( 'comment_posted', (int) $comment->user_id, 'Comment posted on post ID ' . $comment->comment_post_ID );
			$this->log_controller->log_user_action( $event );
		}
	}



	/**
	 * Log plugin activation.
	 *
	 * @param string $plugin The plugin path.
	 * @param bool   $network_wide Whether the plugin is network-activated.
	 */
	public function log_activated_plugin( string $plugin, bool $network_wide ): void {
		try {
			$description = "Activated plugin: {$plugin}" . ( $network_wide ? ' (Network-wide)' : '' );
			$event       = new UserEvent(
				'plugin_activated',
				get_current_user_id(),
				$description
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log plugin deactivation.
	 *
	 * @param string $plugin The plugin path.
	 * @param bool   $network_wide Whether the plugin is network-deactivated.
	 */
	public function log_deactivated_plugin( string $plugin, bool $network_wide ): void {
		try {
			$description = "Deactivated plugin: {$plugin}" . ( $network_wide ? ' (Network-wide)' : '' );
			$event       = new UserEvent(
				'plugin_deactivated',
				get_current_user_id(),
				$description
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log plugin deletion.
	 *
	 * @param string $plugin The plugin path.
	 */
	public function log_deleted_plugin( string $plugin ): void {
		try {
			$description = "Deleted plugin: {$plugin}";
			$event       = new UserEvent(
				'plugin_deleted',
				get_current_user_id(),
				$description
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log theme switch.
	 *
	 * @param string    $new_theme The name of the new theme.
	 * @param \WP_Theme $old_theme The old theme object.
	 */
	public function log_switch_theme( string $new_theme, \WP_Theme $old_theme ): void {
		try {
			$old_theme_name = $old_theme->get( 'Name' );
			$description    = "Switched theme from {$old_theme_name} to {$new_theme}";
			$event          = new UserEvent(
				'theme_switched',
				get_current_user_id(),
				$description
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log post - ' . esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Log option update.
	 *
	 * @param string $option The name of the option.
	 */
	public function log_update_option( string $option ): void {
		try {
			$description = "Updated option: {$option}";
			$event       = new UserEvent(
				'option_updated',
				get_current_user_id(),
				$description
			);
			$this->log_controller->log_user_action( $event );
		} catch ( \Exception $e ) {
				wp_die( 'LogAction Plugin: Failed to log  - ' . esc_html( $e->getMessage() ) );
		}
	}
}
