<?php
/**
 * Functionality for handling media attachments in the Site Feedback plugin.
 * 
 * @package Site Feedback
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Hide feedback screenshots from the media library.
 *
 * @param array $args The query arguments for attachments.
 * @return array Modified query arguments.
 */
function site_feedback_hide_feedback_screenshots( $args ) {
	// Only modify the query if the 'hide_in_media_library' setting is enabled.
	$options = get_option( 'site_feedback_settings' );
	if ( ! isset( $options['hide_in_media_library'] ) || ! $options['hide_in_media_library'] ) {
		return $args;
	}

	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => '_site_feedback_feedback_screenshot',
			'compare' => 'NOT EXISTS', // Show if meta doesn't exist
		),
		array(
			'key'     => '_site_feedback_feedback_screenshot',
			'value'   => true,
			'compare' => '!=', // Hide if meta is exactly 'true'
		),
	);

	return $args;
}
add_filter( 'ajax_query_attachments_args', 'site_feedback_hide_feedback_screenshots' );
