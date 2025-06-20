<?php
/**
 * Add the frontend components for Howler.
 * 
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add the frontend components for the feedback form to the footer.
 *
 * @since 1.0.0
 */
function howler_add_frontend_components() {
	// Only load the button if the user is logged in and has the capability to manage options.
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options = get_option( 'howler_settings' );
	$trello_email = isset( $options['trello_email'] ) ? $options['trello_email'] : '';

	if ( empty( $trello_email ) ) {
		return; // Don't show the button if the email is not set.
	}
	?>
	<button id="howler-feedback-button" class="howler-button howler-floating-button">
		<?php esc_html_e( 'Feedback', 'howler' ); ?>
	</button>

	<div id="howler-feedback-notification" class="howler-feedback-notification"></div>

	<div id="howler-feedback-popup" class="howler-feedback-popup" hidden>
		<div class="feedback-popup-inner">
			<p class="popup-heading">
				<?php esc_html_e( 'Got feedback?', 'howler' ); ?>
			</p>
	
			<input type="text" name="feedback-title" id="feedback-title" placeholder="<?php esc_attr_e( 'Feedback Title', 'howler' ); ?>" />
			<textarea name="feedback" id="feedback" placeholder="<?php esc_attr_e( 'Describe what you would like differently...', 'howler' ); ?>" ></textarea>

			<div id="howler-pencil-switcher" class="pencil-switcher">
				<button class="howler-pencil-button black is-active" data-color="#000">
					<span class="screen-reader-label">
						<?php esc_html_e( 'Black', 'howler' ); ?>
					</span>
				</button>

				<button class="howler-pencil-button red" data-color="#ff0000">
					<span class="screen-reader-label">
						<?php esc_html_e( 'Red', 'howler' ); ?>
					</span>
				</button>
			</div>

			<canvas id="howler-canvas" class="howler-canvas" contenteditable="true" width="400" height="300">
				<?php esc_html_e( 'Your browser does not support the canvas element.', 'howler' ); ?>
			</canvas>
	
			<div class="howler-footer">
				<button id="howler-feedback-submit-button" class="howler-button" data-trello-email="<?php echo esc_attr( $trello_email ); ?>">
					<?php esc_html_e( 'Send to Trello', 'howler' ); ?>
				</button>
				<span id="howler-spinner" hidden>
					⏳
				</span>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'howler_add_frontend_components' );