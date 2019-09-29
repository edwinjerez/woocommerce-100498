<?php
/**
 * Class to create a custom section title separator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The arbitrary control class
 */
class SP_Section_Title_Separator_Control extends WP_Customize_Control {

	/**
	 * The settings var
	 *
	 * @var string $settings the blog name.
	 */
	public $settings = 'blogname';

	/**
	 * The description var
	 *
	 * @var string $description the control description.
	 */
	public $description = '';

	/**
	 * Renter the control
	 *
	 * @return void
	 */
	public function render_content() {
		echo '<span class="sp-section-title-separator">' . esc_html( $this->label ) . '</span>';
	}
}
