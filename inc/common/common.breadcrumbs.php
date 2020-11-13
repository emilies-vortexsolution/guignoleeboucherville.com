<?php
namespace Vortex\Breadcrumbs;

use Vortex\Object_Type;


/**
 * Retrieve all directly linked parents of object.
 * This does not use the default WordPress ancestors logics.
 *
 * @param object $object Any object from WordPress.
 *
 * @return array All breadcrumbs.
 *   - There can only be one "home" and one "current".
 *   - "current" is mandatory, not "home".
 *
 * @todo Add a way to check if a page has a template that redirects and isn't a real page to see. This make it a `span` insteand of an `a`.
 */
function get_items( $object ) {

  if ( empty( $object ) ) {
    $object = get_queried_object();
  }

  $items = array();

  ////////////////////////////////////////////////////
  // HOME
  ////////////////////////////////////////////////////
  $items['home'] = get_item_home();

  ////////////////////////////////////////////////////
  // EXCEPTIONS
  // Return right here if object is NULL.
  // It might means:
  //   - 404
  //   - Home without any configuration
  ////////////////////////////////////////////////////
  if ( null === $object ) {
    if ( is_404() ) {
      $items['current'] = array(
        'title'      => '404',
        'is_current' => true,
        'id'         => 0,
      );
    } elseif ( is_home() ) {
      $items['current'] = $items['home'];
      unset( $items['home'] );
    }

    return $items;
  }

  ////////////////////////////////////////////////////
  // ANCESTORS BY TYPE
  ////////////////////////////////////////////////////
  $ancestors = Object_Type\get_object_ancestors( $object );
  foreach ( $ancestors as $parent ) {
    $items[] = Object_Type\get_object_link_info( $parent );
  }

  ////////////////////////////////////////////////////
  // CURRENT
  ////////////////////////////////////////////////////
  if ( empty( $items['current'] ) ) {
    $items['current'] = Object_Type\get_object_link_info( $object );
  }

  return $items;
}


function get_item_home() {
  $frontpage_id = (int) get_option( 'page_on_front' );

  if ( 0 === $frontpage_id ) {
    $frontpage_id = (int) get_option( 'page_for_posts' );
  }

  if ( 0 !== $frontpage_id ) {
    return array(
      'id'    => $frontpage_id,
      'title' => get_the_title( $frontpage_id ),
      'url'   => get_home_url(),
    );
  }

  return array(
    'id'    => 0,
    'title' => esc_html_x( 'Home', 'Breadcrumb title', 'vtx' ),
    'url'   => get_home_url(),
  );
}
