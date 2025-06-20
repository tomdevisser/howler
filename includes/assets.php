<?php
/**
 * Assets for the Howler plugin.
 * 
 * @package Howler
 * @since   1.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * Register Howler's frontend assets.
 *
 * @since 1.0.0
 */
function howler_enqueue_assets() {
	wp_enqueue_style( 'howler-style', HOWLER_PLUGIN_URL . 'assets/css/style.css', array(), HOWLER_VERSION );
	wp_enqueue_script( 'html2canvas-script', HOWLER_PLUGIN_URL . 'assets/js/html2canvas.js', array(), HOWLER_VERSION, true );
	wp_enqueue_script( 'howler-script', HOWLER_PLUGIN_URL . 'assets/js/script.js', array( 'wp-i18n', 'html2canvas-script' ), HOWLER_VERSION, true );
	wp_localize_script( 'howler-script', 'howler', array( 'howler_ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'howler_enqueue_assets' );
