<?php
/**
 * Plugin Name: Howler
 * Description: Let clients take screenshots, annotate visually, and send them to your Trello board — without leaving the site.
 * Version: 1.0.0
 * Author: Tom de Visser
 * Author URI: https://tomdevisser.dev/
 * Tested up to: 6.8
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * License: GNU General Public License v2.0 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: howler
 */

/**
 * Exit if accessed directly.
 */
defined( 'ABSPATH' ) or die;

/**
 * Plugin version, used for cache-busting assets.
 */
define( 'HOWLER_VERSION', '1.0.0' );

/**
 * Absolute path to the plugin directory.
 */
define( 'HOWLER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'HOWLER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bootstrap the plugin.
 */
require_once HOWLER_PLUGIN_DIR . 'includes/bootstrap.php';
