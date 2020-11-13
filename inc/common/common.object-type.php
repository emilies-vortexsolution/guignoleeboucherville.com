<?php
namespace Vortex\Object_Type;

/**
 * Look at certain details inside the given object to determine wich it is.
 * We are not using typed object like WP_Post or WP_Term to still get a good answer
 * even with a mutated object.
 *
 * Possibilities:
 *   - 'post' (WP_Post: page, post, custom post type)
 *   - 'term' (WP_Term)
 *   - 'post_type' (WP_Post_Type)
 *   - '' (empty if nothing was found)
 *
 * @param object $object Any WordPress official object.
 *
 * @return string
 */
function get_object_type( $object ) {
  if ( isset( $object->post_title ) ) {
    return 'post';
  } elseif ( isset( $object->term_id ) ) {
    return 'term';
  } elseif ( isset( $object->capability_type ) ) {
    return 'post_type';
  }

  return '';
}

/**
 * Get object ancestors without having to know the object type.
 *
 * @param object $object Any WordPress official object.
 *
 * @return array All object ancestors in that order: [0 => great-grandparent, 1 => grandparent, 2 => parent]
 */
function get_object_ancestors( $object ) {
  $object_type = get_object_type( $object );
  $ancestors   = array();

  if ( 'post' === $object_type ) {
    $ancestors = get_post_ancestors( $object );
  } elseif ( 'term' === $object_type ) {
    $ancestors = get_term_ancestors( $object );
  }

  return $ancestors;
}

/**
 * Get all the given term ancestors.
 *
 * @param object $term
 *
 * @return array All term ancestors in that order: [0 => great-grandparent, 1 => grandparent, 2 => parent]
 */
function get_term_ancestors( $term ) {
  $ancestors = array();

  while ( ! is_wp_error( $term ) && ! empty( $term->parent ) && ! isset( $ancestors[ $term->parent ] ) ) {
    $term                        = get_term( $term->parent, $term->taxonomy );
    $ancestors[ $term->term_id ] = $term;
  }
  $ancestors = array_reverse( $ancestors );

  return $ancestors;
}

/**
 * Get all the given post ancestors.
 * Counting as ancestors is the `page_linked_to_post_type` that might be linked to the object post type.
 *
 * @param object $post_object
 *
 * @return array All post ancestors in that order: [0 => great-grandparent, 1 => grandparent, 2 => parent]
 */
function get_post_ancestors( $post_object ) {
  $ancestors = array();

  while ( ! is_wp_error( $post_object ) && ! empty( $post_object->post_parent ) && ! isset( $ancestors[ $post_object->post_parent ] ) ) {
    $post_object                   = get_post( $post_object->post_parent );
    $ancestors[ $post_object->ID ] = $post_object;
  }
  $ancestors = array_reverse( $ancestors );

  // Add the page linked to that post type...
  $maybe_page = get_page_linked_to_post_type( $post_object->post_type );
  if ( ! empty( $maybe_page ) ) {
    // ... and it's acestors
    $ancestor_pages   = get_post_ancestors( $maybe_page );
    $ancestor_pages[] = $maybe_page;
    $ancestors        = array_merge( $ancestor_pages, $ancestors );
  } else {
    // ... otherwise, add it's archive link if there is.
    $post_type_object = get_post_type_object( $post_object->post_type );
    if ( ! empty( $post_type_object->has_archive ) ) {
      array_unshift( $ancestors, get_post_type_object( $post_object->post_type ) );
    }
  }

  return $ancestors;
}


/**
 * Get common data on any recognise WordPress object.
 * Usefull when using a loop that process different object types.
 *
 * @param object $object Any WordPress official object.
 *
 * @return array [id, title, url]
 */
function get_object_link_info( $object ) {
  $object_type = get_object_type( $object );

  if ( 'post' === $object_type ) {
    return array(
      'id'    => $object->ID,
      'title' => $object->post_title,
      'url'   => get_the_permalink( $object ),
    );
  } elseif ( 'term' === $object_type ) {
    return array(
      'id'    => $object->term_id,
      'title' => $object->name,
      'url'   => get_term_link( $object ),
    );
  } elseif ( 'post_type' === $object_type ) {
    return array(
      'id'    => 0,
      'title' => $object->label,
      'url'   => get_post_type_archive_link( $object->name ),
    );
  }
}
