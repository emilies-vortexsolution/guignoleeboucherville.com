<?php
use Roots\Sage\Assets;

// get post thumbnail img with default image if none
function vtx_get_thumbnail( $post = null, $size = 'post-thumbnail', $attr = '' ) {
  $thumbnail = false;
  $post_type = get_post_type( $post );
  if ( has_post_thumbnail( $post ) ) {
    $thumbnail = get_the_post_thumbnail( $post, $size, $attr );
  } else {

    $thumbnail_default = get_default_image_linked_to_post_type( $post_type );
    if ( ! empty( $thumbnail_default ) ) {
      $thumbnail = wp_get_attachment_image( $thumbnail_default, $size, $attr );
    } else {
      $thumbnail_default_empty = '/dist/images/defaults/default.jpg';
      $thumbnail               = '<img src="' . get_template_directory_uri() . $thumbnail_default_empty . '"' . convert_array_to_html_attr( $attr ) . ' >';

    }
  }

  return $thumbnail;
}

// get post thumbnail with default image if none
function vtx_get_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
  $thumbnail_url = false;
  $post_type     = get_post_type( $post );
  if ( has_post_thumbnail( $post ) ) {
    $thumbnail_url = get_the_post_thumbnail_url( $post, $size );
  } else {
    $thumbnail_url_default = get_default_image_linked_to_post_type( $post_type );
    if ( ! empty( $thumbnail_url_default ) ) {
      $thumbnail_url = wp_get_attachment_image_url( $thumbnail_url_default, $size );
    } else {
      $thumbnail_url_default_empty = '/dist/images/defaults/default.jpg';
      $thumbnail_url               = get_template_directory_uri() . $thumbnail_url_default_empty;

    }
  }

  return $thumbnail_url;
}

/**
 * @return Object L'objet de la page associé au post type.
 */
function get_default_image_linked_to_post_type( $post_type_slug ) {

  $link_for_post_types = get_default_image_and_post_type_links_option();

  if ( ! empty( $link_for_post_types ) ) {
    foreach ( $link_for_post_types as $link_info ) {
      if ( isset( $link_info['post_type_select'][ $post_type_slug ] ) && ! empty( $link_info['default_image'] ) ) {
        return $link_info['default_image']['ID'];
      }
    }
  }

  return false;
}


function get_default_image_and_post_type_links_option() {
  global $default_image_and_post_type_links_option;

  if ( isset( $default_image_and_post_type_links_option ) ) {
    return $default_image_and_post_type_links_option;
  }

  $default_image_and_post_type_links_option = get_field( 'default_image_by_post_type', 'option' );

  return $default_image_and_post_type_links_option;
}

/**
 * get default image by format in theme's assets
*/
function get_default_img_src( $img_format = '' ) {
  switch ( $img_format ) {
    case 'full':
          return Assets\asset_path( 'images/defaults/full.jpg' );
  }

  return Assets\asset_path( 'images/defaults/default.jpg' );
}
