# LogAction - Activity Logs for Admin

## Description
LogAction is a powerful WordPress plugin that provides a detailed activity logging system for your website. It tracks user actions, changes, and events, allowing site administrators to monitor activity, enhance security, and improve user experience. With an intuitive interface and customizable features, LogAction makes managing your site's activity a breeze.

## Features
- Comprehensive logging of user activities.
- Filter logs by date and actions.
- Export logs to CSV format.
- Easy-to-use interface with customizable settings.

## Installation

### Automatic Installation
1. Go to your WordPress admin panel.
2. Navigate to **Plugins > Add New**.
3. Search for **LogAction**.
4. Click **Install Now** and then **Activate** the plugin.

### Manual Installation
1. Download the plugin from the [GitHub repository](https://github.com/gblessylva/LogAction).
2. Upload the plugin files to the `/wp-content/plugins/logaction/` directory.
3. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage
Once activated, LogAction adds a new menu item in the WordPress admin panel. Click on **Logs** to view all recorded activities. You can filter logs by action type, date, or user. Use the export button to download logs in CSV format for offline analysis.

## Frequently Asked Questions (FAQ)

- **Q: How do I access the logs?**
  - **A:** After activation, navigate to **LogAction > Logs** in your WordPress admin dashboard.

- **Q: How do I export the logs?**
  - **A:** Click the **Export** button on the logs page to download the logs in CSV format.

- **Q: Is there a way to clear the logs?**
  - **A:** Currently, the plugin does not support bulk deletion of logs. Please check for updates.

## Changelog

### Version 1.0.0
- Initial release with basic logging functionality and CSV export feature.

### Version 1.1.0
- Added filtering options for logs by date and action.
- Improved UI for better user experience.

## Support
For support, please visit the [LogAction Support Forum](https://gblessylva.com/logaction/support) or open an issue on our [GitHub repository](https://github.com/gblessylva/LogAction/issues).

## Supported Hooks
``add_filter( 'logaction_role_hierarchy', function ( $hierarchy ) {
    // Add a custom role "super_contributor" between "contributor" and "author".
    $hierarchy['super_contributor'] = 2.5;

    return $hierarchy;
});``
Add new Action
``add_filter( 'logaction_readable_actions', function ( $actions ) {
    $actions['file_uploaded'] = 'File Uploaded';

    return $actions;
});
``

Modify existing action.
``add_filter( 'logaction_readable_actions', function ( $actions ) {
    // Modify an existing action description.
    if ( isset( $actions['login'] ) ) {
        $actions['login'] = 'User Logged In';
    }

    // Return the modified actions array.
    return $actions;
});``


## License
This plugin is licensed under the GPL v2 or later. See the [LICENSE](https://github.com/gblessylva/LogAction/blob/main/LICENSE) file for more information.

## Author
**Sylvanus Godbless - gblessylva** - [Website](https://gblessylva.com)
