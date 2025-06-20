<?php
/**
 * All AJAX functionality for the Howler plugin.
 * 
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Handle AJAX request to send feedback to Trello.
 *
 * @since 1.0.0
 */
function howler_handle_feedback_ajax() {
	$options = get_option( 'howler_settings' );
	$trello_email = isset( $options['trello_email'] ) ? $options['trello_email'] : '';
	$custom_email = isset( $options['custom_email'] ) ? $options['custom_email'] : '';
	$feedback = sanitize_textarea_field( $_POST['feedback'] ?? '' );
	$feedback_title = sanitize_text_field( $_POST['feedback_title'] ?? '' );
	$screenshot_data = $_POST['screenshot'] ?? '';

	if ( ! $feedback || ! $feedback_title || ( ! is_email( $trello_email ) && ! is_email( $custom_email ) ) ) {
		wp_send_json_error( 'Sorry, we couldn\'t send your feedback. We might need some ourselves.' );
	}

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	$message  = '<div style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;">';
	$message .= '<p>' . nl2br( esc_html( $feedback ) ) . '</p>';

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

	$message .= '</div>';

	// If a custom email is set, send feedback there as well.
	if ( is_email( $custom_email ) ) {
		$headers[] = 'Reply-To: ' . $custom_email;
		wp_mail( $custom_email, $feedback_title, $message, $headers );
	}

	// Send feedback to Trello email.
	if ( is_email( $trello_email ) ) {
		$headers[] = 'Reply-To: ' . $trello_email;
		wp_mail( $trello_email, $feedback_title, $message, $headers );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_howler_send_feedback', 'howler_handle_feedback_ajax' );
add_action( 'wp_ajax_nopriv_howler_send_feedback', 'howler_handle_feedback_ajax' );