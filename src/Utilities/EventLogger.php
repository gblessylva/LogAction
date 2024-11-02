<?php

declare(strict_types=1);

namespace LogAction\Utilities;

use LogAction\Controllers\LogController;
use LogAction\Events\UserEvent;

use LogAction\Database\DatabaseHandler;

class EventLogger
{
	private LogController $logController;

	public function __construct()
	{
		$this->logController = new LogController();
		$this->registerHooks();
	}

	private function registerHooks(): void
	{
		add_action('wp_login', [$this, 'logUserLogin'], 10, 2);
		add_action('wp_logout', [$this, 'logUserLogout']);
		add_action('user_register', [$this, 'logUserRegistration'], 10, 1);
		add_action('wp_login_failed', [$this, 'logLoginFailed']);
		add_action('retrieve_password', [$this, 'logRetrievePassword']);
		add_action('delete_user', [$this, 'logUserDeletion'], 10, 1);

		add_action('trash_post', [$this, 'logPostTrashed'], 10, 1);
		add_action('untrash_post', [$this, 'logPostUntrashed'], 10, 1);
		add_action('delete_post', [$this, 'logPostDeleted'], 10, 1);
		add_action('save_post', [$this, 'logPostPublished'], 10, 2);
		
		add_action('activated_plugin', [$this, 'logActivatedPlugin'], 10, 2);
		add_action('deactivated_plugin', [$this, 'logDeactivatedPlugin'], 10, 2);
		add_action('deleted_plugin', [$this, 'logDeletedPlugin'], 10, 1);
		add_action('switch_theme', [$this, 'logSwitchTheme'], 10, 2);
		// add_action('update_option', [$this, 'logUpdateOption'], 10, 3);
		 

	}

	public function logUserLogin(string $user_login, \WP_User $user): void
	{	
		try{
			$event = new UserEvent(
				'login', 
				(int) $user->ID, 
				'User logged in successfully'
			);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
			error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
		}

		
	}

	public function logUserLogout(): void
	{
		$user = wp_get_current_user();
		if ($user->ID) {
			try{
				$event = new UserEvent(
					'logout', 
					(int) $user->ID,
					 'User logged out successfully',
					 $$user->ID
					);
				$this->logController->logAction($event);
			}catch (\Exception $e) {
					error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
			}
		}
	}

	public function logUserRegistration(int $userId): void
	{
		try{
			$event = new UserEvent(
				'register', 
				$userId, 
				'New user registered',
				$userId
			);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
		}
		
	}

	public function logLoginFailed(int $userId): void
	{
		try{
			$event = new UserEvent(
				'login_failed', 
				$userId, 
				'User Attempted to login but failed',
				$userId
			);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
		}
		
	}

	public function logRetrievePassword(int $userId): void
	{
		try{
			$event = new UserEvent(
				'password_retrieve', 
				$userId, 
				'User Requested password Retrieval',
				$userId
			);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
		}
		
	}

	public function logUserDeletion(int $userId): void
	{
		$current_user = wp_get_current_user();
		try{
			$event = new UserEvent(
				'user_deleted', 
				$current_user->ID, 
				$current_user->user_login . ' Deleted user with id: ' . $userId,
				$userId
			);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log user login info - ' . $e->getMessage());
		}
		
	}

	
	 /**
	 * Log post published action.
	 *
	 * @param int $postId
	 * @param \WP_Post $post
	 */

	//  public function logPostPublished(int $postId, \WP_Post $post): void
	 public function logPostPublished(int $postId): void
	{
		$post = get_post($postId);
		$authorId = $post->post_author;
		$postTitle = $post->post_title;
		$post_type = $post->post_type;
		$event_type = '';
		if ($post === null) {
			return; // Exit if post is not found
		}
		if ($post->post_status !== 'publish' || wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
			return;
		}
		if($post_type == 'post'){
			$event_type = 'post_published';
		}else if($post_type == 'page'){
			$event_type = 'page_published';
		}else{
			$event_type = 'post_type_published';
		}
		try{
			$event = new UserEvent(
						$event_type, 
						(int) $authorId, 
						"Published New {$post->post_type } 
						$postTitle (ID: $postId)", 
						$postId 
					);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post publication - ' . $e->getMessage());
		}
		
	}

	/**
	 * Log post Trashed action.
	 *
	 * @param int $postId
	 * @param \WP_Post $post
	 */


	public function logPostTrashed(int $postId): void
	{
		$post = get_post($postId);
		$authorId = $post->post_author;
		$postTitle = $post->post_title;
		$post_type = $post->post_type;
		$event_type = '';
		if ($post === null) {
			return; // Exit if post is not found
		}

		if($post_type == 'post'){
			$event_type = 'post_trashed';
		}else if($post_type == 'page'){
			$event_type = 'page_trashed';
		}else{
			$event_type = 'post_type_trashed';
		}
		try{
			$event = new UserEvent(
						$event_type, 
						(int) $authorId, 
						"Trashed {$post->post_type } ' '
						$postTitle (ID: $postId)", 
						$postId 
					);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		}
		
	}
	
	/**
	 * Log post Trashed action.
	 *
	 * @param int $postId
	 * @param \WP_Post $post
	 */


	 public function logPostUntrashed(int $postId): void
	 {
		 $post = get_post($postId);
		 $authorId = $post->post_author;
		 $postTitle = $post->post_title;
		 $post_type = $post->post_type;
		 $event_type = '';
		 if ($post === null) {
			 return; // Exit if post is not found
		 }
 
		 if($post_type == 'post'){
			 $event_type = 'post_untrashed';
		 }else if($post_type == 'page'){
			 $event_type = 'page_untrashed';
		 }else{
			 $event_type = 'post_type_untrashed';
		 }
		 try{
			 $event = new UserEvent(
						 $event_type, 
						 (int) $authorId, 
						 "Restored {$post->post_type } ' '
						 $postTitle (ID: $postId) from trash", 
						 $postId 
					 );
			 $this->logController->logAction($event);
		 }catch (\Exception $e) {
				 error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		 }
		 
	 }

	/**
	 * Log post Trashed action.
	 *
	 * @param int $postId
	 * @param \WP_Post $post
	 */


	 public function logPostDeleted(int $postId): void
	 {
		 $post = get_post($postId);
		 $authorId = $post->post_author;
		 $postTitle = $post->post_title;
		 $post_type = $post->post_type;
		 $event_type = '';
		 if ($post === null) {
			 return; // Exit if post is not found
		 }
 
		 if($post_type == 'post'){
			 $event_type = 'post_deleted';
		 }else if($post_type == 'page'){
			 $event_type = 'page_deleted';
		 }else{
			 $event_type = 'post_type_deleted';
		 }
		 try{
			 $event = new UserEvent(
						 $event_type, 
						 (int) $authorId, 
						 "Permanently  Deleted {$post->post_type } ' '
						 $postTitle (ID: $postId) ", 
						 $postId 
					 );
			 $this->logController->logAction($event);
		 }catch (\Exception $e) {
				 error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		 }
		 
	 }
	
	
	public function logCommentPosted(int $commentId, string $commentStatus): void
	{
		if ($commentStatus === 'approve') {
			$comment = get_comment($commentId);
			$event = new UserEvent('comment_posted', (int) $comment->user_id, 'Comment posted on post ID ' . $comment->comment_post_ID);
			$this->logController->logAction($event);
		}
	}



	/**
	 * Log plugin activation.
	 *
	 * @param string $plugin The plugin path.
	 * @param bool $network_wide Whether the plugin is network-activated.
	 */
	public function logActivatedPlugin(string $plugin, bool $network_wide): void
	{
		try{
			$description = "Activated plugin: {$plugin}" . ($network_wide ? ' (Network-wide)' : '');
			$event = new UserEvent(
				'plugin_activated', 
				get_current_user_id(), 
				$description);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		}

		
	}

	/**
	 * Log plugin deactivation.
	 *
	 * @param string $plugin The plugin path.
	 * @param bool $network_wide Whether the plugin is network-deactivated.
	 */
	public function logDeactivatedPlugin(string $plugin, bool $network_wide): void
	{	
		try{
			$description = "Deactivated plugin: {$plugin}" . ($network_wide ? ' (Network-wide)' : '');
			$event = new UserEvent(
				'plugin_deactivated', 
				get_current_user_id(), 
				$description);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		}

	}

	/**
	 * Log plugin deletion.
	 *
	 * @param string $plugin The plugin path.
	 */
	public function logDeletedPlugin(string $plugin): void
	{
		try{
			$description = "Deleted plugin: {$plugin}";
			$event = new UserEvent(
				'plugin_deleted', 
				get_current_user_id(), 
				$description);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log - ' . $e->getMessage());
		}

		
	}

	/**
	 * Log theme switch.
	 *
	 * @param string $new_theme The name of the new theme.
	 * @param \WP_Theme $old_theme The old theme object.
	 */
	public function logSwitchTheme(string $new_theme, \WP_Theme $old_theme): void
	{
		try{
			$old_theme_name = $old_theme->get('Name');
			$description = "Switched theme from {$old_theme_name} to {$new_theme}";
			$event = new UserEvent(
				'theme_switched', 
				get_current_user_id(), 
				$description);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post - ' . $e->getMessage());
		}
		
	}

	/**
	 * Log option update.
	 *
	 * @param string $option The name of the option.
	 * @param mixed $old_value The old value of the option.
	 * @param mixed $new_value The new value of the option.
	 */
	public function logUpdateOption(string $option, $old_value, $new_value): void
	{	
		try{
			$description = "Updated option: {$option}";
			$event = new UserEvent(
				'option_updated', 
				get_current_user_id(), 
				$description);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log  - ' . $e->getMessage());
		}

		
	}

}