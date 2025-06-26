<?php
/**
 * Tools page for Site Feedback plugin to manage cleanup tasks.
 * 
 * @package Site Feedback
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add a Tools submenu page for Site Feedback cleanup tasks.
 *
 * @since 1.0.0
 */
function site_feedback_add_tools_page() {
	add_management_page(
		__( 'Site Feedback Cleanup', 'site-feedback' ),
		__( 'Site Feedback Cleanup', 'site-feedback' ),
		'manage_options',
		'site-feedback-cleanup',
		'site_feedback_render_tools_page'
	);
}
add_action( 'admin_menu', 'site_feedback_add_tools_page' );

/**
 * Render the Tools page with a delete button.
 *
 * @since 1.0.0
 */
function site_feedback_render_tools_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$deleted_screenshots = false;

	if ( isset( $_POST['site_feedback_delete_screenshots_nonce'] ) && wp_verify_nonce( sanitize_textarea_field( wp_unslash( $_POST['site_feedback_delete_screenshots_nonce'] ) ), 'site_feedback_delete_screenshots' ) ) {
		$screenshots = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key'     => '_site_feedback_feedback_screenshot',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		foreach ( $screenshots as $screenshot ) {
			wp_delete_attachment( $screenshot->ID, true );
		}

		$deleted_screenshots = true;
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Site Feedback Cleanup', 'site-feedback' ); ?></h1>

		<?php
		if ( $deleted_screenshots ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Screenshots deleted from the Media Library.', 'site-feedback' ); ?></p>
			</div>
			<?php
		}
		?>

		<form method="post">
			<h2><?php esc_html_e( 'Media Library', 'site-feedback' ); ?></h2>
			<?php wp_nonce_field( 'site_feedback_delete_screenshots', 'site_feedback_delete_screenshots_nonce' ); ?>
			<p><?php esc_html_e( 'This will permanently delete all screenshots uploaded by Site Feedback from the Media Library. This action cannot be undone.', 'site-feedback' ); ?></p>
			<?php submit_button( __( 'Delete All Screenshots', 'site-feedback' ), 'delete' ); ?>
		</form>
	</div>
	<?php
}