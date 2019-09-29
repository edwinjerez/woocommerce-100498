<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * Class add a shortcode generator button to tinymce.
 */
class Storefront_Powerpack_Pricing_Tables_Shortcode_Generator {
	/**
	 * Constructor function.
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'tinymce_button' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tinymce_script' ) );
	}

	public function tinymce_button() {
		global $typenow;

		// check user permissions
    	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
    		return;
    	}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true') {
		    add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugin' ) );
		    add_filter( 'mce_buttons', array( $this, 'tinymce_register_button' ) );
		}
	}

	public function tinymce_plugin( $plugin_array ) {
	    $plugin_array['spt_tinymce_button'] = SP_PLUGIN_URL . 'includes/customizer/pricing-tables/assets/js/pricing-table-button.min.js';
	    return $plugin_array;
	}

	public function tinymce_register_button( $buttons ) {
	   array_push( $buttons, 'spt_tinymce_button' );
	   return $buttons;
	}

	public function tinymce_script() {
		wp_enqueue_style( 'sp-pricing-tables-tinymce-style', SP_PLUGIN_URL . 'includes/customizer/pricing-tables/assets/css/admin.css', '', storefront_powerpack()->version );
	}
}

return new Storefront_Powerpack_Pricing_Tables_Shortcode_Generator();