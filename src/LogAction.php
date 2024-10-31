<?php

declare(strict_types=1);

namespace LogAction;

use Exception;
use LogAction\Database\DatabaseHandler;
use LogAction\Utilities\EventLogger;

/**
 * Class LogAction
 *
 * The main plugin class for the LogAction - Activity Log Plugin.
 */
class LogAction
{
    private static ?LogAction $instance = null;

    // Private constructor to prevent instantiation
    private function __construct()
    {
        // Initialization code here if needed
    }

    /**
     * Returns the singleton instance of the LogAction class.
     *a
     * @return LogAction
     */
    public static function getInstance(): LogAction
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Activates the plugin, called on activation.
     *
     * @return void
     * @throws Exception If the table creation fails.
     */
    public static function activate(): void
    {
        DatabaseHandler::createTable();
        // Code to run on activation
        // e.g., create database tables, set default options
    }

    /**
     * Deactivates the plugin, called on deactivation.
     *
     * @return void
     */
    public static function deactivate(): void
    {
        DatabaseHandler::deleteTable();
        // Code to run on deactivation
        // e.g., cleanup, remove temporary options
    }

    /**
     * Initializes the plugin's functionalities.
     *
     * @return void
     */
    public function init(): void
    {
        // Code to initialize the plugin's functionalities
        // e.g., register hooks, setup event logging
        // add_action('init', [$this, 'setupHooks']);
        new EventLogger();
    }

    /**
     * Sets up hooks for event logging.
     *
     * @return void
     */
    public function setupHooks(): void
    {
        // Example: Log a message when a post is published
        // add_action('publish_post', [$this, 'logPostPublished']);
    }

    
}
