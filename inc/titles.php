<?php

namespace Roots\Sage\Titles;

/**
 * Page titles
 */
function title() {
  if ( is_home() ) {
    if ( get_option( 'page_for_posts', true ) ) {
      return get_the_title( get_option( 'page_for_posts', true ) );
    } else {
      return __( 'Latest Posts', 'vtx' );
    }
  } elseif ( is_archive() ) {
    return get_the_archive_title();
  } elseif ( is_search() ) {
    /* translators: search terms */
    return sprintf( __( 'Search Results for %s', 'vtx' ), get_search_query() );
  } elseif ( is_404() ) {
    return _x( 'Page Not Found', '404 page title', 'vtx' );
  } else {
    return get_the_title();
  }
}
