<?php

declare(strict_types=1);

namespace LogAction\Controllers;

class RenderMenuPages
{
    /**
     * Render the logs page.
     *
     * @return void
     */
    public static function renderLogsPage(): void
    {
        // Include the template for the logs page
        include plugin_dir_path(__DIR__) . '../templates/logs.php';
    }

    /**
     * Render the settings page.
     *
     * @return void
     */
    public static function renderSettingsPage(): void
    {
        // Include the template for the settings page
        include plugin_dir_path(__DIR__) . '../templates/settings.php';
    }
}
