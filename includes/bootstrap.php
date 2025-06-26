<?php
/**
 * Bootstrap the Site Feedback plugin.
 *
 * @package Site Feedback
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Load plugin settings.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/settings.php';

/**
 * Load plugin AJAX functionality.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/ajax.php';

/**
 * Load plugin tools.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/tools.php';

/**
 * Load assets for the plugin.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/assets.php';

/**
 * Load frontend UI components.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/frontend-ui.php';

/**
 * Load media handling functionality.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/media.php';
