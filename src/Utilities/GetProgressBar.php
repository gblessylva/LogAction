<?php
/**
 *
 * Renders the progress bar.
 *
 * @package logaction
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0
 */

namespace LogAction\Utilities;

/**
 * Add the progress loader HTML to the page.
 */
class GetProgressBar {

	/**
	 * Add the progress loader HTML to the page.
	 */
	public static function render_progress_loader() {
		?>
		<div id="progress-loader">
			<span class="loader"></span>
		</div>
		<?php
	}
}