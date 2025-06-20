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
		'howler_custom_email',
		__( 'Custom Email Address', 'howler' ),
		'howler_custom_email_field_cb',
		'howler-settings',
		'howler_common_section'
	);

	add_settings_field(
		'howler_trello_email',
		__( 'Trello Email-to-board', 'howler' ),
		'howler_trello_email_field_cb',
		'howler-settings',
		'howler_common_section'
	);

	add_settings_field(
		'howler_hide_in_media_library',
		__( 'Hide screenshots in the media library', 'howler' ),
		'howler_hide_in_media_library_cb',
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

	/**
	 * Sanitize the custom e-mail address.
	 */
	if ( isset( $input['custom_email'] ) ) {
		$output['custom_email'] = sanitize_text_field( $input['custom_email'] );
	} else {
		$output['custom_email'] = '';
	}

	/**
	 * Sanitize hide_in_media_library checkbox.
	 */
	$output['hide_in_media_library'] = isset( $input['hide_in_media_library'] ) ? 1 : 0;

	return $output;
}

/**
 * Render input for Trello email address.
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
 * Render input for custom email address.
 *
 * @since 1.0.0
 */
function howler_custom_email_field_cb() {
	$options = get_option( 'howler_settings', array() );
	$value   = isset( $options['custom_email'] ) ? $options['custom_email'] : '';

	printf(
		'<input type="email" name="howler_settings[custom_email]" value="%s" class="regular-text">',
		esc_attr( $value )
	);

	echo '<p class="description">' . esc_html__( 'This lets you receive feedback directly via email.', 'howler' ) . '</p>';
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
			<?php esc_html_e( 'Welcome to Howler! This plugin lets you collect visual feedback from your website and send it to Trello, email, or other destinations. Just enter your preferred address below. Got suggestions or ideas? I\'d love to hear them via the plugin reviews.', 'howler' ); ?>
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

/**
 * Render checkbox to hide screenshots in the media library.
 *
 * @since 1.0.0
 */
function howler_hide_in_media_library_cb() {
	$options = get_option( 'howler_settings', array() );
	$checked = isset( $options['hide_in_media_library'] ) ? (bool) $options['hide_in_media_library'] : false;

	printf(
		'<label><input type="checkbox" name="howler_settings[hide_in_media_library]" value="1" %s> %s</label>',
		checked( $checked, true, false ),
		esc_html__( 'Prevent screenshots from appearing in the Media Library.', 'howler' )
	);
}
