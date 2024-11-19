<?php
/**
 * Adds the LogAction settings submenu.
 *
 * @package logaction
 */

?>
<div class = 'wrap'>
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
					<label for="only_admin_delete_logs"><?php echo esc_html__( 'Only Admins Can Delete Logs', 'logaction' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="only_admin_delete_logs" name="only_admin_delete_logs" value="1" <?php checked( get_option( 'only_admin_delete_logs' ), 1 ); ?>>
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
			<tr>
				<th scope="row">
					<label for="restrict_log_access"><?php echo esc_html__( 'Restrict access to logs.', 'logaction' ); ?></label>
					<span><?php echo esc_html__( 'None Admin Users will be able to view only their logs, but not other users logs.', 'logaction' ); ?></span>
				</th>
				<td>
					<input type="checkbox" id="restrict_log_access" name="restrict_log_access" value="1" <?php checked( get_option( 'restrict_log_access' ), 1 ); ?>>
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