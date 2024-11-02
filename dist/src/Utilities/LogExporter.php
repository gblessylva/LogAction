<?php

declare(strict_types=1);

namespace LogAction\Utilities;

use LogAction\Database\DatabaseHandler;

class LogExporter
{
    private DatabaseHandler $databaseHandler;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler();
    }

    /**
     * Export logs as CSV.
     *
     * @return void
     */
    public function exportLogsToCSV() : void
    {
    $logs = DatabaseHandler::getLogsForExport();
        // Set headers for the CSV file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="logs_export_' . date('Y-m-d_H-i', time()) . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add column headers to the CSV
        fputcsv($output, ['ID', 'Action', 'User ID', 'Description', 'Date']);
       
        // Write each log entry    to the CSV
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id, // Assuming you have an ID field
                LogHelper::getReadableAction($log->action), // Adjust these fields based on your log structure
                $log->user_id,
                $log->description,
                LogHelper::formatdate($log->date), // Ensure this matches your log's date field
            ]);
        }

        // Close the output stream
        fclose($output);
        exit; // Terminate script to prevent any additional output
    }
}
