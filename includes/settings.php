<?php
/**
 * Settings for Site Feedback plugin.
 *
 * @package Site Feedback
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
function site_feedback_plugin_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=site_feedback_settings' ) ) . '">' . esc_html__( 'Settings', 'site-feedback' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_site-feedback/site-feedback.php', 'site_feedback_plugin_action_links' );

/**
 * Add site-feedback settings page under the Settings menu.
 *
 * @since 1.0.0
 */
function site_feedback_add_settings_page() {
	add_options_page(
		__( 'Site Feedback Settings', 'site-feedback' ),
		__( 'Site Feedback', 'site-feedback' ),
		'manage_options',
		'site_feedback_settings',
		'site_feedback_render_settings_page'
	);
}
add_action( 'admin_menu', 'site_feedback_add_settings_page' );

/**
 * Register Site Feedback settings, sections, and fields.
 *
 * @since 1.0.0
 */
function site_feedback_register_settings() {
	register_setting(
		'site_feedback_settings_group',
		'site_feedback_settings',
		'site_feedback_sanitize_settings'
	);

	add_settings_section(
		'site_feedback_common_section',
		__( 'Common Settings', 'site-feedback' ),
		'__return_false',
		'site_feedback_settings'
	);

	add_settings_field(
		'site_feedback_custom_email',
		__( 'Custom Email Address', 'site-feedback' ),
		'site_feedback_custom_email_field_cb',
		'site_feedback_settings',
		'site_feedback_common_section'
	);

	add_settings_field(
		'site_feedback_trello_email',
		__( 'Trello Email-to-board', 'site-feedback' ),
		'site_feedback_trello_email_field_cb',
		'site_feedback_settings',
		'site_feedback_common_section'
	);

	add_settings_field(
		'site_feedback_hide_in_media_library',
		__( 'Hide screenshots in the media library', 'site-feedback' ),
		'site_feedback_hide_in_media_library_cb',
		'site_feedback_settings',
		'site_feedback_common_section'
	);
}
add_action( 'admin_init', 'site_feedback_register_settings' );

/**
 * Sanitize site-feedback settings.
 *
 * @since 1.0.0
 *
 * @param array $input Raw input.
 * @return array Sanitized input.
 */
function site_feedback_sanitize_settings( $input ) {
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
function site_feedback_trello_email_field_cb() {
	$options = get_option( 'site_feedback_settings', array() );
	$value   = isset( $options['trello_email'] ) ? $options['trello_email'] : '';

	printf(
		'<input type="email" name="site_feedback_settings[trello_email]" value="%s" class="regular-text">',
		esc_attr( $value )
	);

	echo '<p class="description">' . esc_html__( 'Paste the unique email address for your Trello board here. You can find it in Trello by opening your board, clicking the three-dot menu in the top right, then choosing “More” → “Email-to-board Settings”. Trello will generate a special email address that lets you create new cards by sending an email.', 'site-feedback' ) . '</p>';
}

/**
 * Render input for custom email address.
 *
 * @since 1.0.0
 */
function site_feedback_custom_email_field_cb() {
	$options = get_option( 'site_feedback_settings', array() );
	$value   = isset( $options['custom_email'] ) ? $options['custom_email'] : '';

	printf(
		'<input type="email" name="site_feedback_settings[custom_email]" value="%s" class="regular-text">',
		esc_attr( $value )
	);

	echo '<p class="description">' . esc_html__( 'This lets you receive feedback directly via email.', 'site-feedback' ) . '</p>';
}

/**
 * Render the settings page HTML.
 *
 * @since 1.0.0
 */
function site_feedback_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Site Feedback', 'site-feedback' ); ?></h1>
		<p>
			<?php esc_html_e( 'Welcome to Site Feedback! This plugin lets you collect visual feedback from your website and send it to Trello, email, or other destinations. Just enter your preferred address below. Got suggestions or ideas? I\'d love to hear them via the plugin reviews.', 'site-feedback' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'site_feedback_settings_group' );
			do_settings_sections( 'site_feedback_settings' );
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
function site_feedback_hide_in_media_library_cb() {
	$options = get_option( 'site_feedback_settings', array() );
	$checked = isset( $options['hide_in_media_library'] ) ? (bool) $options['hide_in_media_library'] : false;

	printf(
		'<label><input type="checkbox" name="site_feedback_settings[hide_in_media_library]" value="1" %s> %s</label>',
		checked( $checked, true, false ),
		esc_html__( 'Prevent screenshots from appearing in the Media Library.', 'site-feedback' )
	);
}
