<?php
/**
 * Display search form
 *
 * @package  Eris
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'eris' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Start Searching', 'eris' ); ?>" value="" name="s" autocomplete="off">
	</label>
	<input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'eris' ); ?>" disabled="">
</form>
