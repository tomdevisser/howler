<?php
/**
 * Plugin Name: Site Feedback
 * Description: Let clients take screenshots, annotate visually, and send feedback to Trello, email, or more — all without leaving your site.
 * Version: 1.0.0
 * Author: Tom de Visser
 * Author URI: https://tomdevisser.dev/
 * Tested up to: 6.8
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * License: GNU General Public License v2.0 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-feedback
 */

/**
 * Exit if accessed directly.
 */
defined( 'ABSPATH' ) or die;

/**
 * Plugin version, used for cache-busting assets.
 */
define( 'EASY_FEEDBACK_VERSION', '1.0.0' );

/**
 * Absolute path to the plugin directory.
 */
define( 'EASY_FEEDBACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'EASY_FEEDBACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bootstrap the plugin.
 */
require_once EASY_FEEDBACK_PLUGIN_DIR . 'includes/bootstrap.php';
