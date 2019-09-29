<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Eris
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
	<div class="scroll-box">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div>
	<button id="closeSidebar" class="close-sidebar"><span class="screen-reader-text"><?php esc_html_e( 'close sidebar', 'eris' ); ?></span></button>
</aside><!-- #secondary -->

