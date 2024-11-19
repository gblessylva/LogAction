<?php
/**
 * Class top loadd all scripts.
 *
 * @package logaction.
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

declare(strict_types=1);

namespace LogAction\Hooks;

use LogAction\Database\DatabaseHandler;
use LogAction\Utilities\Configs;
use LogAction\Utilities\LogExporter;
use PSpell\Config;

/**
 * Load script class.
 */
class LoadAdminScripts {
	/**
	 * Constructor method to Register AJAX action.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_logaction_scripts' ) );
		add_action( 'wp_ajax_export_logs', array( $this, 'export_logs' ) );
		add_action( 'wp_ajax_delete_selected_logs', array( $this, 'delete_selected_logs' ) );
		add_action( 'wp_ajax_delete_all_logs', array( $this, 'delete_all_logs' ) );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_logaction_scripts(): void {
		// Enqueue the main admin style.
		wp_enqueue_style(
			'logaction-admin-style',
			Configs::$asset_url . '/css/admin-style.css',
			null,
			Configs::VERSION,
		);

		// Enqueue the main admin script.
		wp_enqueue_script(
			'logaction-admin-script',
			Configs::$asset_url . '/js/admin-script.js',
			array( 'jquery' ),
			Configs::VERSION,
			true
		);
		// Enqueue the exporter script.
		wp_enqueue_script(
			'logaction-exporter',
			Configs::$asset_url . '/js/log-exporter.js',
			array( 'jquery' ),
			Configs::VERSION,
			true
		);
		// Localize script to pass AJAX URL.
		wp_localize_script(
			'logaction-exporter',
			'logaction_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'delete_logs_nonce' ),
			),
		);
		// Bootstrap CSS (for modal and other components).
		wp_enqueue_style(
			'bootstrap-css',
			'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
			array(),
			Configs::VERSION
		);

		// Bootstrap JS (includes modal functionality).
		wp_enqueue_script(
			'bootstrap-js',
			'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js',
			array( 'jquery' ),
			Configs::VERSION,
			true
		);

		// jQuery (WordPress includes jQuery by default).
		wp_enqueue_script( 'jquery' );
	}
	/**
	 * Handle the export logs AJAX request.
	 * SHould move this to another controller??
	 *
	 * @return void
	 */
	public function export_logs(): void {
		$exporter = new LogExporter();
		$exporter->export_logs_to_csv();
		// Exit to prevent WordPress from returning a 404 response.
		exit;
	}

	/**
	 * Handle the delete logs AJAX request.
	 *
	 * @return void
	 */
	public function delete_selected_logs(): void {
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( \wp_unslash( $_POST['_wpnonce'] ) ), 'delete_logs_nonce' )
		) {
			wp_send_json_error( 'Invalid nonce', 403 );
		}

		$logs = isset( $_POST['logs'] ) ? array_map( 'intval', $_POST['logs'] ) : array();
		if ( ! empty( $logs ) ) {
			DatabaseHandler::delete_logs_by_id( $logs );
			// Construct the redirect URL.
			$redirect_url = add_query_arg(
				array(
					'page'    => 'logaction_logs',
					'orderby' => 'date',
					'order'   => 'ASC',
				),
				admin_url( 'admin.php' )
			);

			// Send success response with the redirect URL.
			wp_send_json_success( $redirect_url );
		} else {
			wp_send_json_error( 'No logs selected.' );
		}

		wp_die();
	}

	/**
	 * Deletes all Logs in the DB using AJAX request.
	 *
	 * @return void
	 */
	public function delete_all_logs(): void {
		check_ajax_referer( 'delete_logs_nonce', '_wpnonce' );

		$result = DatabaseHandler::delete_all_logs();

		if ( false === $result ) {
			wp_send_json_error( 'Failed to delete logs.' );
		} else {
			wp_send_json_success( "Deleted {$result} logs." );
		}
	}
}
