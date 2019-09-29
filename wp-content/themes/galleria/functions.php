<?php
/**
 * Galleria engine room
 *
 * @package galleria
 */

 $theme              = wp_get_theme( 'galleria' );
 $galleria_version 	= $theme['Version'];

/**
 * Load the individual classes required by this theme
 */
include_once( 'inc/class-galleria.php' );
include_once( 'inc/class-galleria-customizer.php' );
include_once( 'inc/class-galleria-structure.php' );
include_once( 'inc/class-galleria-integrations.php' );

/**
 * Do not add custom code / snippets here.
 * While Child Themes are generally recommended for customisations, in this case it is not
 * wise. Modifying this file means that your changes will be lost when an automatic update
 * of this theme is performed. Instead, add your customisations to a plugin such as
 * https://github.com/woothemes/theme-customisations
 */
