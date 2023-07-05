<?php
/**
 * WP Book Plugin setup
 *
 * @package wp-book
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WPBook Plugin Class.
 *
 * @class WPBook
 */
class WPBook {

	/**
	 * The single instance of the class.
	 *
	 * @var   WPBook
	 * @since 2.1
	 */
	protected static $instance = null;
	/**
	 * Main WPBook Instance.
	 *
	 * Ensures only one instance of WPBook is loaded or can be loaded.
	 *
	 * @since  1.0
	 * @static
	 * @return WPBook - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks() {
		register_activation_hook( WPBOOK_PLUGIN_FILE, array( $this, 'activation_hook' ) );
		register_deactivation_hook( WPBOOK_PLUGIN_FILE, array( $this, 'deactivation_hook' ) );
	}

	/**
	 * Plugin activation hook
	 */
	public function activation_hook() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$collate         = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		$table_name      = $wpdb->prefix . 'book_meta';
		$sql             = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}book_meta (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			book_id bigint(20) NOT NULL,
			author_name varchar(50) DEFAULT NULL,
			price float NOT NULL DEFAULT '0',
			publisher varchar(50) DEFAULT NULL,
			year year(4) DEFAULT NULL,
			edition varchar(50) DEFAULT NULL,
			url varchar(100) DEFAULT NULL			
			) $collate;";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}book_meta'" ) !== $table_name ) {
			include_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}


	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-wpbook-custom-post-type.php';
		include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-wpbook-dashboard-widget.php';
		include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-wpbook-shortcode.php';
		include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-wpbook-widget.php';
		include_once dirname( WPBOOK_PLUGIN_FILE ) . '/includes/class-book-settings-submenu.php';
	}

	/**
	 * Plugin URL
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WPBOOK_PLUGIN_FILE ) );
	}

	/**
	 * Plugin deactivation hook
	 */
	public function deactivation_hook() {

	}
}
