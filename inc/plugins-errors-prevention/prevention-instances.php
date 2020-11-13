<?php
/**
 * This theme plugins dependencies are neutralize here.
 * The necessary conditions of function_exists, class_exists, defined or isset goes inside.
 * If a test fails, we need to create a matching function as placeholder until an admin activates the required plugins.
 * This is needed to prevent any fatal error preventing a reach to the admin plugins list.
 */

// Add placeholders below...


/////////////////////////////////////////
// ACF
/////////////////////////////////////////
if ( ! function_exists( 'get_field' ) ) {
  vtx_add_inactive_plugin( 'acf' );

  // @codingStandardsIgnoreLine
  function get_field( ...$args ) {
    vtx_notice_use_of_inactive_plugin( __FUNCTION__ );
  }
}
