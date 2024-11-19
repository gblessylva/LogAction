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
use LogAction\Utilities\OptionsHandler;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Fetch logs from the database.
global $wpdb;
$table_name = $wpdb->prefix . 'logaction_logs';
$nonce      = wp_create_nonce( 'logaction_action_nounce' );


if ( wp_verify_nonce( $nonce, 'logaction_action_nounce' ) ) {
	$month_param = isset( $_GET['m'] ) ? sanitize_text_field( wp_unslash( $_GET['m'] ) ) : '';
	// Get current order by and order parameters.
	$current_order_by = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';
	$current_order    = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DSC';
}


// Get current page and set the number of logs per page.
$current_page  = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$logs_per_page = 10;
$offset        = ( $current_page - 1 ) * $logs_per_page;

// Prepare the order clause for the SQL query.
$order_clause = LogHelper::prepare_sort( $current_order_by, $current_order );

$date_filter = DatabaseHandler::get_date_filter( $month_param );


if ( ! empty( $month_param ) ) {
	$result = DatabaseHandler::get_logs_by_month( $month_param, $order_clause, $logs_per_page, $offset );

} else {
	$result = DatabaseHandler::get_all_logs( $order_clause, $logs_per_page, $offset );
}
$logs = $result['logs'];
// Count total logs for pagination.
$total_logs  = $result['total'];
$total_pages = ceil( $total_logs / $logs_per_page );


if ( $logs && ! empty( $logs ) ) : ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Logs', 'logaction' ); ?> </h1>
		<div class="tablenav top">
			<?php
			if ( OptionsHandler::is_bulk_delete_enabled() ) {

				?>
				<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
				<select name="action" id="bulk-action-selector-top">
					
					<option value="-1">Bulk actions</option>
					<option value="trash">Permanently Delete</option>
				</select>
				<input type="submit" id="doaction" class="button action" value="Apply">
			</div>
		<?php } ?>
			<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
				<!-- Hidden fields to keep the page, orderby, and order parameters -->
				<input type="hidden" name="page" value="logaction_logs">
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $current_order_by ); ?>">
				<input type="hidden" name="order" value="<?php echo esc_attr( $current_order ); ?>">
			<div class="alignleft actions">
				<label for="filter-by-date" class="screen-reader-text">Filter by date</label>
				<select name="m" id="filter-by-date">
				<option value="0">All dates</option>
				<?php
					$months_array = LogHelper::get_months_from_logs();
				if ( $months_array ) {
					foreach ( $months_array as $value => $label ) {
						$selected = ( isset( $_GET['m'] ) && (string) $_GET['m'] === (string) $value ) ? 'selected="selected"' : '';
						echo '<option value="' . esc_attr( $value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
					}
				}
				?>
			</select>

				<?php do_action( 'logaction_add_more_filter_fields' ); ?>
				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
				<button type="button" name="clear_filters" id="clear-filters" class="button" value="Clear Filters"> 
					<a href='<?php echo esc_url( admin_url( 'admin.php?page=logaction_logs' ) ); ?>'>Reset</a> 
				</button>
				
			</div>
			</form>
			<div class="tablenav-pages one-page">
				<?php
				if ( OptionsHandler::is_log_export_enabled() ) {
					?>
					<button type="button" name="export_logs" id="export_logs" class="button" value="Export"> 
						Export 
					</button>
					<?php
				}
				?>
				<?php do_action( 'logaction_after_export_button' ); ?>
				
				<span class="displaying-num"> <?php echo esc_html( count( $logs ) ); ?> of <?php echo esc_html( $total_logs ); ?> item<?php echo esc_html( count( $logs ) === 1 ? '' : 's' ); ?> </span >
				
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped table-view-list logs">
			<thead>
				<tr>

				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox" /> </td>

					<th scope="col" id="action" class="manage-column column-action sortable <?php echo esc_attr( 'action' === $current_order_by ? $current_order : '' ); ?>">
						<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderby' => 'action',
									'order'   => $current_order === 'asc' ? 'desc' : 'asc',
									'paged'   => $current_page,
								),
								admin_url( 'admin.php?page=logaction_logs' )
							)
						);
						?>
									">
							<span><?php esc_html_e( 'Action', 'logaction' ); ?></span>
							<span class="sorting-indicator <?php echo esc_attr( 'action' === $current_order_by && 'asc' === $current_order ? 'asc' : ( 'action' === $current_order_by && 'desc' === $current_order ? 'desc' : '' ) ); ?>"></span>

						</a>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e( 'Created by', 'logaction' ); ?></span>
					</th>
					<th scope="col" id="date" class="manage-column column-date sortable <?php echo esc_attr( 'date' === $current_order_by ? $current_order : '' ); ?>" >
						<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderby' => 'date',
									'order'   => $current_order === 'asc' ? 'desc' : 'asc',
									'paged'   => $current_page,
								),
								admin_url( 'admin.php?page=logaction_logs' )
							)
						);
						?>
									">
							<?php esc_html_e( 'Date', 'logaction' ); ?>
							<span class="sorting-indicator <?php echo esc_attr( 'date' === $current_order_by && 'asc' === $current_order ? 'asc' : ( 'date' === $current_order_by && 'desc' === $current_order ? 'desc' : '' ) ); ?>">
							</span>
						</a>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e( 'Description', 'logaction' ); ?></span>
					</th>
					<th scope="col" class="manage-column sortable">
						<span><?php esc_html_e( 'Action ID', 'logaction' ); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $logs as $log ) : ?>
					<tr>
						<th scope="row" class="check-column">
							<input type="checkbox" name="logs[]" value="<?php echo esc_attr( $log->id ); ?>">
						</th>
						<td><?php echo esc_html( LogHelper::get_readable_action( $log->action ) ); ?></td>
						<td>
							<?php $user_info = LogHelper::get_user_info( $log->user_id ); ?>
							
							<div class="user-wrapper">
								<img src="<?php echo esc_url( $user_info['avatar'] ); ?>" alt='User Avatar' height="50">
								<div class="user-info">
									<span>Name: <?php echo esc_html( $user_info['full_name'] ); ?></span> <br>
									<span>Username: <?php echo esc_html( $user_info['username'] ); ?></span> <br>
									<span>Email: <?php echo esc_html( $user_info['email'] ); ?></span>
								</div>
							</div>
							
						</td>
						<td><?php echo esc_html( LogHelper::format_log_date( $log->date ) ); ?></td>
						<td><?php echo esc_html( $log->description ); ?></td>
						<td><?php echo esc_html( $log->action_id ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody> 
			
		</table>
		<div class="tablenav-pages logs-footer-nav">
		
			<?php if ( $total_pages ) : ?>
				<span class="pagination-links">
				<?php
					$pagination_base = admin_url( 'admin.php?page=logaction_logs&orderby=' . $current_order_by . '&order=' . $current_order . '&paged=%#%' );
						echo wp_kses_post(
							paginate_links(
								array(
									'base'      => $pagination_base,
									'format'    => '',
									'current'   => $current_page,
									'total'     => $total_pages,
									'prev_text' => __( '« Previous', 'logaction' ),
									'next_text' => __( 'Next »', 'logaction' ),
								)
							)
						);
				?>
				</span>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Logs', 'logaction' ); ?></h1>
		<p><?php esc_html_e( 'No logs found.', 'logaction' ); ?></p>
	</div>
<?php endif; ?>
