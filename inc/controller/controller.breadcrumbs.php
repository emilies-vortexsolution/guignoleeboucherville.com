<?php
use Vortex\Breadcrumbs;


/**
 * Get all items necessary for the breadcrumbs and apply logics.
 */
add_filter( 'get_partial_args/templates/breadcrumbs/breadcrumbs', 'control_template_data_breadcrumbs' );
function control_template_data_breadcrumbs( $template_data ) {
  $template_data = array_merge(
    array(
      'object'           => null,
      'show_home'        => true,
      'current_has_link' => false,
      'is_global'        => true,
      'items'            => array(),
    ),
    $template_data
  );

  if ( null === $template_data['object'] ) {
    $template_data['object'] = get_queried_object();
  }

  // Execute all the logics to get breadcrumbs if no items are given already.
  if ( empty( $template_data['items'] ) ) {
    $template_data['items'] = Vortex\Breadcrumbs\get_items( $template_data['object'] );
  }

  // Remove home from items if conditions meet.
  //  - `show_home` is true.
  //  - Has more thant one item.
  //  - There is a `home` to unset.
  if ( ! $template_data['show_home'] && 1 < count( $template_data['items'] ) && isset( $template_data['items']['home'] ) ) {
    unset( $template_data['items']['home'] );
  }

  if ( ! empty( $template_data['items']['current'] ) ) {
    $template_data['items']['current']['is_current'] = true;

    if ( ! $template_data['current_has_link'] ) {
      $template_data['items']['current']['url'] = '';
    }

    // Remove `home` if `current` has the same ID.
    if ( isset( $template_data['items']['home'] ) && $template_data['items']['home']['id'] === $template_data['items']['current']['id'] ) {
      unset( $template_data['items']['home'] );
    }
  }

  return $template_data;
}



/**
 * Logics for a crumb
 */
add_filter( 'get_partial_args/templates/breadcrumbs/breadcrumbs-item', 'control_breadcrumbs_item' );
function control_breadcrumbs_item( $template_data ) {
  $template_data = array_merge(
    array(
      'title'                    => '',
      'url'                      => '',
      'position'                 => 0,
      'title_wrapper_tag'        => 'span',
      'title_wrapper_link_attrs' => '',
      'is_current'               => false,
    ),
    $template_data
  );

  // Bail if no title
  if ( empty( $template_data['title'] ) ) {
    return $template_data;
  }

  $attrs = array();

  if ( ! empty( $template_data['url'] ) ) {
    $template_data['title_wrapper_tag'] = 'a';
    $attrs['href']                      = $template_data['url'];
  }

  if ( $template_data['is_current'] ) {
    $attrs['aria-current'] = 'page';
  }

  $template_data['title_wrapper_link_attrs'] = convert_array_to_html_attr( $attrs );

  return $template_data;
}
