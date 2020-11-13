<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );
function body_class( $classes ) {
  // Add page slug if it doesn't exist
  if ( is_single() || is_page() && ! is_front_page() ) {
    if ( ! in_array( basename( get_permalink() ), $classes, true ) ) {
      $classes[] = basename( get_permalink() );
    }
  }

  // Add class if sidebar is active
  if ( Setup\display_sidebar() ) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}

/**
 * Clean up the_excerpt()
 */
add_filter( 'excerpt_more', __NAMESPACE__ . '\\excerpt_more' );
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __( 'Continued', 'vtx' ) . '</a>';
}
