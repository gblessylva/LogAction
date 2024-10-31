<?php

declare(strict_types=1);

namespace LogAction\Controllers;

use LogAction\Database\DatabaseHandler;

class SettingsController
{
    private string $optionsKey = 'logaction_options'; // Option key for storing settings

    /**
     * Registers the settings for the plugin.
     *
     * @return void
     */
    public function registerSettings(): void
    {
        register_setting('logaction_settings_group', $this->optionsKey);
    }

    /**
     * Displays the settings page for the plugin.
     *
     * @return void
     */
    public function settingsPage(): void
    {
        // Include the settings page view here
        include plugin_dir_path(__FILE__) . '../views/settings.php';
    }

    /**
     * Retrieves the plugin settings.
     *
     * @return array The current settings.
     */
    public function getSettings(): array
    {
        return get_option($this->optionsKey, []);
    }

    /**
     * Updates a specific setting.
     *
     * @param string $key The setting key.
     * @param mixed $value The setting value.
     * @return void
     */
    public function updateSetting(string $key, $value): void
    {
        $settings = $this->getSettings();
        $settings[$key] = $value;
        update_option($this->optionsKey, $settings);
    }
}
