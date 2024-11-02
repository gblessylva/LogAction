<?php

declare(strict_types=1);

namespace LogAction\Hooks;

use LogAction\Utilities\LogExporter;

class LoadAdminScripts
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_ajax_export_logs', [$this, 'exportLogs']); // Register AJAX action
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @return void
     */
    public function enqueueScripts(): void
    {
        // Enqueue the main admin style
        wp_enqueue_style('logaction-admin-style', plugin_dir_url(__DIR__) . '../assets/css/admin-style.css');

        // Enqueue the main admin script
        wp_enqueue_script('logaction-admin-script', plugin_dir_url(__DIR__) . '../assets/js/admin-script.js', ['jquery'], null, true);
        
        // Enqueue the exporter script
        wp_enqueue_script('logaction-exporter', plugin_dir_url(__DIR__) . '../assets/js/log-exporter.js', ['jquery'], null, true);
        
        // Localize script to pass AJAX URL
        wp_localize_script('logaction-exporter', 'logaction_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
    
    /**
     * Handle the export logs AJAX request.
     * SHould move this to another controller??
     *
     * @return void
     */
    public function exportLogs(): void
    {
       
        $exporter = new LogExporter();
        $exporter->exportLogsToCSV();

        // Exit to prevent WordPress from returning a 404 response
        exit;
    }
}
