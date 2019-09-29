<?php
/**
 * Storefront Powerpack Mega Menus Class
 *
 * @package Storefront_Powerpack
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of SP_Mega_Menus to prevent the need to use globals.
 *
 * @since 2.0.0
 * @return object SP_Mega_Menus
 */
function SP_Mega_Menus() {
	return SP_Mega_Menus::instance();
} // End SP_Mega_Menus()

SP_Mega_Menus();

/**
 * Mega Menus main class.
 */
final class SP_Mega_Menus {
	/**
	 * SP_Mega_Menus The single instance of SP_Mega_Menus.
	 * @var 	object
	 * @access  private
	 * @since 	2.0.0
	 */
	private static $_instance = null;

	/**
	 * The plugin url.
	 * @var     string
	 * @access  public
	 * @since   2.0.0
	 */
	public $version;

	/**
	 * The plugin path.
	 * @var     string
	 * @access  public
	 * @since   2.0.0
	 */
	public $plugin_url;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   2.0.0
	 */
	public $plugin_path;

	/**
	 * Setup class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->version     = storefront_powerpack()->version;
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );

		include_once( 'includes/class-smm-admin.php' );
		include_once( 'includes/class-smm-customizer.php' );
		include_once( 'includes/class-smm-frontend.php' );
	}

	/**
	 * Main SP_Mega_Menus Instance
	 *
	 * Ensures only one instance of SP_Mega_Menus is loaded or can be loaded.
	 *
	 * @since 2.0.0
	 * @static
	 * @see SP_Mega_Menus()
	 * @return Main SP_Mega_Menus instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()
}
