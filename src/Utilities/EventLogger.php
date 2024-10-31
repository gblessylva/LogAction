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
		// add_action('publish_post', [$this, 'logPostPublished'], 10, 2);
		// add_action('publish_post', [$this, 'logPostPublished'], 20);

		add_action('save_post', [$this, 'logPostPublished'], 10, 2);

		// add_action('comment_post', [$this, 'logCommentPosted'], 10, 2);
	}

	public function logUserLogin(string $user_login, \WP_User $user): void
	{
		$event = new UserEvent('login', (int) $user->ID, 'User logged in successfully');
		$this->logController->logAction($event);
	}

	public function logUserLogout(): void
	{
		$user = wp_get_current_user();
		if ($user->ID) {
			$event = new UserEvent('logout', (int) $user->ID, 'User logged out successfully');
			$this->logController->logAction($event);
		}
	}

	public function logUserRegistration(int $userId): void
	{
		$event = new UserEvent('register', $userId, 'New user registered');
		$this->logController->logAction($event);
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
		if ($post === null) {
			return; // Exit if post is not found
		}
		if ($post->post_status !== 'publish' || wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
			return;
		}
		try{
			$event = new UserEvent(
						'post_published', 
						(int) $authorId, 
						"Published post: 
						$postTitle (ID: $postId)", 
						$postId 
					);
			$this->logController->logAction($event);
		}catch (\Exception $e) {
				error_log('LogAction Plugin: Failed to log post publication - ' . $e->getMessage());
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

}
