<?php
/**
 * Plugin Name:     WP Book
 * Plugin URI:      www.futurebridge.com
 * Description:     WP Book Plugin for custom post type
 * Author:          Futurebridge
 * Author URI:      www.futurebridge.com
 * Text Domain:     wp-book
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package Wp_Book
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPBOOK_PLUGIN_FILE' ) ) {
	define( 'WPBOOK_PLUGIN_FILE', __FILE__ );
}

// Include the main class.
if ( ! class_exists( 'WPBook', false ) ) {
	include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-wpbook.php';
}

/**
 * Returns the main instance of WPBook Plugin.
 *
 * @since  2.1
 * @return Main Instance
 */
function wp_book() {
	return WPBook::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpbook-plugin'] = wp_book();
