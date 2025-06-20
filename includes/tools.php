<?php
/**
 * Tools page for Howler plugin to manage cleanup tasks.
 * 
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add a Tools submenu page for Howler cleanup tasks.
 *
 * @since 1.0.0
 */
function howler_add_tools_page() {
	add_management_page(
		__( 'Howler Cleanup', 'howler' ),
		__( 'Howler Cleanup', 'howler' ),
		'manage_options',
		'howler-cleanup',
		'howler_render_tools_page'
	);
}
add_action( 'admin_menu', 'howler_add_tools_page' );

/**
 * Render the Tools page with a delete button.
 *
 * @since 1.0.0
 */
function howler_render_tools_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$deleted_screenshots = false;

	if ( isset( $_POST['howler_delete_screenshots_nonce'] ) && wp_verify_nonce( sanitize_textarea_field( wp_unslash( $_POST['howler_delete_screenshots_nonce'] ) ), 'howler_delete_screenshots' ) ) {
		$screenshots = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key'     => '_howler_feedback_screenshot',
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
		<h1><?php esc_html_e( 'Howler Cleanup', 'howler' ); ?></h1>

		<?php
		if ( $deleted_screenshots ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Screenshots deleted from the Media Library.', 'howler' ); ?></p>
			</div>
			<?php
		}
		?>

		<form method="post">
			<h2><?php esc_html_e( 'Media Library', 'howler' ); ?></h2>
			<?php wp_nonce_field( 'howler_delete_screenshots', 'howler_delete_screenshots_nonce' ); ?>
			<p><?php esc_html_e( 'This will permanently delete all screenshots uploaded by Howler from the Media Library. This action cannot be undone.', 'howler' ); ?></p>
			<?php submit_button( __( 'Delete All Screenshots', 'howler' ), 'delete' ); ?>
		</form>
	</div>
	<?php
}