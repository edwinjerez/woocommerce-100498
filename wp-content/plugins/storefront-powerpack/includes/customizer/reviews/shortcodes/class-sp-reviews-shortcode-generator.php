<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Class add a shortcode generator button to tinymce.
 */
class Storefront_Powerpack_Reviews_Shortcode_Generator {
	/**
	 * Constructor function.
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'sr_tinymce_button' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'sr_tinymce_script' ) );
	}

	public function sr_tinymce_button() {
		global $typenow;

		// check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true') {
			add_filter( 'mce_external_plugins', array( $this, 'sr_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'sr_tinymce_register_button' ) );
		}
	}

	public function sr_tinymce_plugin( $plugin_array ) {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$plugin_array['sr_tinymce_button'] = SP_PLUGIN_URL . 'includes/customizer/reviews/assets/js/reviews-button' . $suffix . '.js';
		return $plugin_array;
	}

	public function sr_tinymce_register_button( $buttons ) {
		array_push( $buttons, 'sr_tinymce_button' );
		return $buttons;
	}

	public function sr_tinymce_script() {
		wp_enqueue_style( 'sp-reviews-tinymce-style', SP_PLUGIN_URL . 'includes/customizer/reviews/assets/css/admin.css', storefront_powerpack()->version );
	}
}

return new Storefront_Powerpack_Reviews_Shortcode_Generator();