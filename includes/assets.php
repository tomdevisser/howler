<?php
/**
 * Assets for the Site Feedback plugin.
 * 
 * @package Site Feedback
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Register Site Feedback's frontend assets.
 *
 * @since 1.0.0
 */
function site_feedback_enqueue_assets() {
	wp_enqueue_style( 'site-feedback-style', EASY_FEEDBACK_PLUGIN_URL . 'assets/css/style.css', array(), EASY_FEEDBACK_VERSION );
	wp_enqueue_script( 'html2canvas-script', EASY_FEEDBACK_PLUGIN_URL . 'assets/js/html2canvas.js', array(), EASY_FEEDBACK_VERSION, true );
	wp_enqueue_script( 'site-feedback-script', EASY_FEEDBACK_PLUGIN_URL . 'assets/js/script.js', array( 'wp-i18n', 'html2canvas-script' ), EASY_FEEDBACK_VERSION, true );
	wp_localize_script(
		'site-feedback-script',
		'siteFeedback',
		array(
			'site_feedback_ajax_url'       => admin_url( 'admin-ajax.php' ),
			'site_feedback_feedback_nonce' => wp_create_nonce( 'site_feedback_feedback_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'site_feedback_enqueue_assets' );
