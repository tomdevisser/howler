<?php
/**
 * Settings for Howler plugin.
 *
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Add settings link to the Plugins page.
 *
 * @since 1.0.0
 *
 * @param array $links Existing plugin action links.
 * @return array Modified plugin action links.
 */
function howler_plugin_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=howler-settings' ) ) . '">' . esc_html__( 'Settings', 'howler' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_howler/howler.php', 'howler_plugin_action_links' );

/**
 * Add howler settings page under the Settings menu.
 *
 * @since 1.0.0
 */
function howler_add_settings_page() {
	add_options_page(
		__( 'Howler Settings', 'howler' ),
		__( 'Howler', 'howler' ),
		'manage_options',
		'howler-settings',
		'howler_render_settings_page'
	);
}
add_action( 'admin_menu', 'howler_add_settings_page' );

/**
 * Register Howler settings, sections, and fields.
 *
 * @since 1.0.0
 */
function howler_register_settings() {
	register_setting(
		'howler_settings_group',
		'howler_settings',
		'howler_sanitize_settings'
	);

	add_settings_section(
		'howler_common_section',
		__( 'Common Settings', 'howler' ),
		'__return_false',
		'howler-settings'
	);

	add_settings_field(
		'howler_trello_email',
		__( 'Trello e-mail address', 'howler' ),
		'howler_trello_email_field_cb',
		'howler-settings',
		'howler_common_section'
	);
}
add_action( 'admin_init', 'howler_register_settings' );

/**
 * Sanitize howler settings.
 *
 * @since 1.0.0
 *
 * @param array $input Raw input.
 * @return array Sanitized input.
 */
function howler_sanitize_settings( $input ) {
	$output = array();

	/**
	 * Sanitize the Trello e-mail address.
	 */
	if ( isset( $input['trello_email'] ) ) {
		$output['trello_email'] = sanitize_text_field( $input['trello_email'] );
	} else {
		$output['trello_email'] = '';
	}

	return $output;
}

/**
 * Render input for excluded meta keys.
 *
 * @since 1.0.0
 */
function howler_trello_email_field_cb() {
	$options = get_option( 'howler_settings', array() );
	$value   = isset( $options['trello_email'] ) ? $options['trello_email'] : '';

	printf(
		'<input type="email" name="howler_settings[trello_email]" value="%s" class="regular-text">',
		esc_attr( $value )
	);

	echo '<p class="description">' . esc_html__( 'Paste the unique email address for your Trello board here. You can find it in Trello by opening your board, clicking the three-dot menu in the top right, then choosing “More” → “Email-to-board Settings”. Trello will generate a special email address that lets you create new cards by sending an email.', 'howler' ) . '</p>';
}

/**
 * Render the settings page HTML.
 *
 * @since 1.0.0
 */
function howler_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Howler', 'howler' ); ?></h1>
		<p>
			<?php esc_html_e( 'Welcome to Howler! This plugin lets you send visual feedback to Trello — just enter your board\'s email address below. Got suggestions or ideas? I\'d love to hear them via the plugin reviews.', 'howler' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'howler_settings_group' );
			do_settings_sections( 'howler-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
