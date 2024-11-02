<?php
/**
 * Logs Template
 *
 * This template displays all the logs in a table format, allowing users to sort
 * the logs by action or date.
 *
 * @package LogAction
 */
use LogAction\Database\DatabaseHandler;
use LogAction\Utilities\LogHelper;

defined('ABSPATH') || exit; // Exit if accessed directly

// Fetch logs from the database
global $wpdb;
$table_name = $wpdb->prefix . 'logaction_logs'; // Adjust to your actual table name

// Get current order by and order parameters
$current_order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$current_order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DSC';

// Get current page and set the number of logs per page
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$logs_per_page = 10; // Change this to set how many logs per page
$offset = ($current_page - 1) * $logs_per_page;

// Prepare the order clause for the SQL query
$order_clause = LogHelper::prepareSort($current_order_by, $current_order);
$date_filter = DatabaseHandler::getDateFilter(isset($_GET['m']) ? $_GET['m'] : '');
$month_param = isset($_GET['m']) ? $_GET['m'] : '';



if( !empty($month_param)  ){
	$result = DatabaseHandler::getLogsByMonth($month_param, $order_clause, $logs_per_page, $offset);

}else{
	$result = DatabaseHandler::getAllLogs($order_clause, $logs_per_page, $offset);
}
$logs = $result['logs'];
// Count total logs for pagination
$total_logs = $result['total'];
$total_pages = ceil($total_logs / $logs_per_page);

// Check if there are any logs
if ($logs && !empty($logs)) : ?>
	<div class="wrap">
		<h1><?php esc_html_e('Logs', 'logaction'); ?></h1>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
				<select name="action" id="bulk-action-selector-top">
					
					<option value="-1">Bulk actions</option>
					<option value="trash">Move to Trash</option>
				</select>
				<input type="submit" id="doaction" class="button action" value="Apply">
			</div>
			<form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
				<!-- Hidden fields to keep the page, orderby, and order parameters -->
				<input type="hidden" name="page" value="logaction_logs">
				<input type="hidden" name="orderby" value="<?php echo esc_attr($current_order_by); ?>">
				<input type="hidden" name="order" value="<?php echo esc_attr($current_order); ?>">

			<div class="alignleft actions">
				<label for="filter-by-date" class="screen-reader-text">Filter by date</label>
				<select name="m" id="filter-by-date">
				<option value="0">All dates</option>
				<?php
					$months_array = LogHelper::get_months_from_logs();
					if ($months_array) {
						foreach ($months_array as $value => $label) {
							$selected = (isset($_GET['m']) && (string)$_GET['m'] === (string)$value) ? 'selected="selected"' : '';
							echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
						}
					}
				?>
			</select>

				<?php do_action('logaction_add_more_filter_fields');  ?>
				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
					<button type="button" name="clear_filters" id="clear-filters" class="button" value="Clear Filters"> <a href='<?php echo admin_url('admin.php?page=logaction_logs'); ?>'>Reset</a> </button>
			</div>
			</form>
			<div class="tablenav-pages one-page">
				<span class="displaying-num"><?php echo count($logs); ?> of <?php echo $total_logs ?> item<?php echo count($logs) === 1 ? '' : 's'; ?></span>
				
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped table-view-list logs">
			<thead>
				<tr>
					<th></th>
					<th scope="col" id="action" class="manage-column column-action sortable <?php echo esc_attr($current_order_by === 'action' ? $current_order : ''); ?>">
						<a href="<?php echo esc_url(add_query_arg(['orderby' => 'action', 'order' => $current_order === 'asc' ? 'desc' : 'asc', 'paged' => $current_page], admin_url('admin.php?page=logaction_logs'))); ?>">
							<span><?php esc_html_e('Action', 'logaction'); ?></span>
							<span class="sorting-indicator <?php echo esc_attr($current_order_by === 'action' && $current_order === 'asc' ? 'asc' : ($current_order_by === 'action' && $current_order === 'desc' ? 'desc' : '')); ?>">
							</span>
						</a>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e('Created by', 'logaction'); ?></span>
					</th>
					<th scope="col" id="date" class="manage-column column-date sortable <?php echo esc_attr($current_order_by === 'date' ? $current_order : ''); ?>">
						<a href="<?php echo esc_url(add_query_arg(['orderby' => 'date', 'order' => $current_order === 'asc' ? 'desc' : 'asc', 'paged' => $current_page], admin_url('admin.php?page=logaction_logs'))); ?>">
							<?php esc_html_e('Date', 'logaction'); ?>
							<span class="sorting-indicator <?php echo esc_attr($current_order_by === 'date' && $current_order === 'asc' ? 'asc' : ($current_order_by === 'date' && $current_order === 'desc' ? 'desc' : '')); ?>">
							</span>
						</a>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e('Description', 'logaction'); ?></span>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e('Action ID', 'logaction'); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($logs as $log) : ?>
					<tr>
						<th scope="row" class="check-column">
							<input type="checkbox" value="<?php echo esc_attr($log->id); ?>">
						</th>
						<td><?php echo esc_html(LogHelper::getReadableAction($log->action)); ?></td>
						<td>
							<?php $userInfo = LogHelper::getUserInfo($log->user_id); ?>
							<span>Name: <?php echo esc_html($userInfo['full_name']); ?></span> <br>
							<span>Username: <?php echo esc_html($userInfo['username']); ?></span> <br>
							<span>Email: <?php echo esc_html($userInfo['email']); ?></span>
						</td>
						<td><?php echo esc_html(LogHelper::formatdate($log->date)); ?></td>
						<td><?php echo esc_html($log->description); ?></td>
						<td><?php echo esc_html($log->action_id); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="tablenav-pages">

		
			<?php if ($total_pages > 1) : ?>
				<span class="pagination-links">
				<?php
					$pagination_base = admin_url('admin.php?page=logaction_logs&orderby=' . $current_order_by . '&order=' . $current_order . '&paged=%#%');
					echo paginate_links([
					'base' => $pagination_base,
								'format' => '',
								'current' => $current_page,
								'total' => $total_pages,
								'prev_text' => __('« Previous', 'logaction'),
								'next_text' => __('Next »', 'logaction'),
							]);
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	<div class="wrap">
		<h1><?php esc_html_e('Logs', 'logaction'); ?></h1>
		<p><?php esc_html_e('No logs found.', 'logaction'); ?></p>
	</div>
<?php endif; ?>
