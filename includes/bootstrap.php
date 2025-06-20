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
 * Load plugin AJAX functionality.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/ajax.php';

/**
 * Load plugin tools.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/tools.php';

/**
 * Load assets for the plugin.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/assets.php';

/**
 * Load frontend UI components.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/frontend-ui.php';

/**
 * Load media handling functionality.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/media.php';
