<?php
/**
 * Bootstrap the Howler plugin.
 *
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Load plugin settings.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/settings.php';

/**
 * Register Howler's frontend assets.
 *
 * @since 1.0.0
 */
function howler_enqueue_assets() {
	wp_enqueue_style( 'howler-style', HOWLER_PLUGIN_URL . 'assets/css/style.css', array(), HOWLER_VERSION );
	wp_enqueue_script( 'html2canvas-script', HOWLER_PLUGIN_URL . 'assets/js/html2canvas.js', array(), HOWLER_VERSION, true );
	wp_enqueue_script( 'howler-script', HOWLER_PLUGIN_URL . 'assets/js/script.js', array( 'wp-i18n', 'html2canvas-script' ), HOWLER_VERSION, true );
	wp_localize_script( 'howler-script', 'howler', array( 'howler_ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'howler_enqueue_assets' );

/**
 * Add a floating feedback button to the frontend.
 *
 * @since 1.0.0
 */
function howler_add_floating_button() {
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
				<?php esc_html_e( 'Leave your feedback', 'howler' ); ?>
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
add_action( 'wp_footer', 'howler_add_floating_button' );

/**
 * Handle AJAX request to send feedback to Trello.
 *
 * @since 1.0.0
 */
function howler_handle_feedback_ajax() {
	$options = get_option( 'howler_settings' );
	$trello_email = isset( $options['trello_email'] ) ? $options['trello_email'] : '';
	$feedback = sanitize_textarea_field( $_POST['feedback'] ?? '' );
	$feedback_title = sanitize_text_field( $_POST['feedback_title'] ?? '' );
	$screenshot_data = $_POST['screenshot'] ?? '';

	if ( ! $feedback || ! $feedback_title || ! is_email( $trello_email ) ) {
		wp_send_json_error( 'Sorry, we couldn\'t send your feedback. We might need some ourselves.' );
	}

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$message = '<p>' . nl2br( esc_html( $feedback ) ) . '</p>';

	// Save image to media library.
	if ( $screenshot_data && str_starts_with( $screenshot_data, 'data:image/png;base64,' ) ) {
		$decoded = base64_decode( str_replace( 'data:image/png;base64,', '', $screenshot_data ) );

		// Generate a unique filename.
		$upload_dir = wp_upload_dir();
		$filename = 'howler-screenshot-' . time() . '.png';
		$filepath = $upload_dir['path'] . '/' . $filename;

		// Save the image to the file system.
		file_put_contents( $filepath, $decoded );

		// Add the image to the media library.
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/png',
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$filepath
		);

		if ( $attachment_id ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filepath );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			add_post_meta( $attachment_id, '_howler_feedback_screenshot', true );

			$image_url = wp_get_attachment_url( $attachment_id );
			$message .= '<p><strong>Screenshot:</strong><br><img src="' . esc_url( $image_url ) . '" style="max-width:100%; height:auto;" /></p>';
		}
	}

	wp_mail( $trello_email, $feedback_title, $message, $headers );
	wp_send_json_success();
}
add_action( 'wp_ajax_howler_send_feedback', 'howler_handle_feedback_ajax' );
add_action( 'wp_ajax_nopriv_howler_send_feedback', 'howler_handle_feedback_ajax' );

/**
 * Hide feedback screenshots from the media library.
 *
 * @param array $args The query arguments for attachments.
 * @return array Modified query arguments.
 */
function howler_hide_feedback_screenshots( $args ) {
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => '_howler_feedback_screenshot',
			'compare' => 'NOT EXISTS', // Show if meta doesn't exist
		),
		array(
			'key'     => '_howler_feedback_screenshot',
			'value'   => true,
			'compare' => '!=', // Hide if meta is exactly 'true'
		),
	);

	return $args;
}
add_filter( 'ajax_query_attachments_args', 'howler_hide_feedback_screenshots' );
