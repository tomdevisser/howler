<?php
/**
 * Add the frontend components for Site Feedback.
 * 
 * @package Site Feedback
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add the frontend components for the feedback form to the footer.
 *
 * @since 1.0.0
 */
function site_feedback_add_frontend_components() {
	// Only load the button if the user is logged in and has the capability to manage options.
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options = get_option( 'site_feedback_settings' );
	$trello_email = isset( $options['trello_email'] ) ? $options['trello_email'] : '';
	$custom_email = isset( $options['custom_email'] ) ? $options['custom_email'] : '';

	if ( empty( $trello_email ) && empty( $custom_email ) ) {
		return; // Don't show the button if the email is not set.
	}
	?>
	<button id="site-feedback-feedback-button" class="site-feedback-button site-feedback-floating-button">
		<?php esc_html_e( 'Feedback', 'site-feedback' ); ?>
	</button>

	<div id="site-feedback-feedback-notification" class="site-feedback-feedback-notification"></div>

	<div id="site-feedback-feedback-popup" class="site-feedback-feedback-popup" hidden>
		<div class="feedback-popup-inner">
			<p class="popup-heading">
				<?php esc_html_e( 'Got feedback?', 'site-feedback' ); ?>
			</p>
	
			<input type="text" name="feedback-title" id="feedback-title" placeholder="<?php esc_attr_e( 'Feedback Title', 'site-feedback' ); ?>" />
			<textarea name="feedback" id="feedback" placeholder="<?php esc_attr_e( 'Describe what you would like differently...', 'site-feedback' ); ?>" ></textarea>

			<div id="site-feedback-pencil-switcher" class="pencil-switcher">
				<button class="site-feedback-pencil-button black is-active" data-color="#000">
					<span class="screen-reader-label">
						<?php esc_html_e( 'Black', 'site-feedback' ); ?>
					</span>
				</button>

				<button class="site-feedback-pencil-button red" data-color="#ff0000">
					<span class="screen-reader-label">
						<?php esc_html_e( 'Red', 'site-feedback' ); ?>
					</span>
				</button>
			</div>

			<canvas id="site-feedback-canvas" class="site-feedback-canvas" contenteditable="true" width="400" height="300">
				<?php esc_html_e( 'Your browser does not support the canvas element.', 'site-feedback' ); ?>
			</canvas>
	
			<div class="site-feedback-footer">
				<button id="site-feedback-feedback-submit-button" class="site-feedback-button" data-trello-email="<?php echo esc_attr( $trello_email ); ?>">
					<?php esc_html_e( 'Send to Trello', 'site-feedback' ); ?>
				</button>
				<span id="site-feedback-spinner" hidden>
					⏳
				</span>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'site_feedback_add_frontend_components' );