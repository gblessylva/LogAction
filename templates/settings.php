<?php
/**
 * Adds the LogAction settings submenu.
 *
 * @package logaction
 */

namespace LogAction\Utilities;

use LogAction\Utilities\GetProgressBar;

GetProgressBar::render_progress_loader();
?>

<div class = 'wrap'>
	<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="warningModalLabel">Confirm Log Deletion</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete all logs? This action is irreversible.
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="button" id="confirm-delete-logs" class="btn btn-danger">Delete Logs</button>
				</div>
			</div>
		</div>
	</div>
	<h1> <?php echo esc_html__( 'LogAction Settings', 'logaction' ); ?></h1>
	<p><?php echo esc_html__( 'Configure the settings for the LogAction plugin. Use the options below to manage logs effectively.', 'logaction' ); ?></p>
	<form method="post" action="options.php" class="logaction-settings-form">
		<?php
			settings_fields( 'logaction_settings' );
			do_settings_sections( 'logaction_settings' );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="enable_log_deletion"><?php echo esc_html__( 'Enable Bulk log deletion', 'logaction' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="enable_log_deletion" name="enable_log_deletion" value="1" <?php checked( get_option( 'enable_log_deletion' ), 1 ); ?>>
				</td>
			</tr>
			

			<tr>
				<th scope="row">
					<label for="delete_logs_after_days"><?php echo esc_html__( 'Delete Logs after 30 days', 'logaction' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="delete_logs_after_days" name="delete_logs_after_days" value="1" <?php checked( get_option( 'delete_logs_after_days' ), 1 ); ?>>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="enable_logs_export"><?php echo esc_html__( 'Enable Logs Export', 'logaction' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="enable_logs_export" name="enable_logs_export" value="1" <?php checked( get_option( 'enable_logs_export' ), 1 ); ?>>
				</td>
			</tr>
	
		</table>
		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save Changes', 'logaction' ); ?>">
		</p>
	</form>
	<tr class="danger-zone">
				<th scope="row">
					<label for="empty-logs"><?php echo esc_html__( 'Empty all logs on the database', 'logaction' ); ?></label>
					<p><?php echo esc_html__( 'This will delete all logs on the site. Proceed with caution.', 'logaction' ); ?></p>
				</th>
				<td>
					<?php wp_nonce_field( 'delete_logs_nonce' ); ?>
					<button type="button" class="button button-danger" id="empty-logs">
						<?php echo esc_html__( 'Delete All Logs Now', 'logaction' ); ?>
					</button>
				</td>
			</tr>

</div>