<?php
add_filter( 'get_partial_args/templates/sitemap/sitemap', 'control_template_data_sitemap' );
function control_template_data_sitemap( $template_data ) {
  $template_data = array_merge(
    array(
      'post_types'      => array(),
      'sitemap_section' => array(),
    ),
    $template_data
  );

  if ( ! empty( $template_data['post_types'] ) && empty( $template_data['sitemap_section'] ) ) {
    foreach ( $template_data['post_types'] as $key => $post_type ) {
      if ( is_string( $post_type ) ) {
        $post_type = array(
          'post_type' => $post_type,
        );
      }

      $post_type = array_merge(
        array(
          'post_type'     => '',
          'title'         => '',
          'apply_filters' => '',
        ),
        $post_type
      );

      $post_type_object = get_post_type_object( $post_type['post_type'] );

      // Bail if the post type does not exists.
      // Also, we remove all evidence it has even been mentioned...
      if ( empty( $post_type_object ) ) {
        unset( $template_data['post_types'][ $key ] );
        continue;
      }

      if ( empty( $post_type['title'] ) ) {
        $post_type['title'] = $post_type_object->label;
      }

      // ... otherwise, save it.
      $template_data['sitemap_section'][ $post_type['post_type'] ] = $post_type;
    }
  }

  return $template_data;
}


add_filter( 'get_partial_args/templates/sitemap/sitemap-section', 'control_sitemap_section_data' );
function control_sitemap_section_data( $template_data ) {

  $template_data = array_merge(
    array(
      'post_type'        => '',
      'title'            => '',
      'post_type_object' => null,
    ),
    $template_data
  );

  $template_data = control_sitemap_list_template_data( $template_data );

  // Remove the filter.
  // It has already been applied in `control_sitemap_list_template_data`.
  $template_data['apply_filters'] = '';

  return $template_data;
}


add_filter( 'get_partial_args/templates/sitemap/sitemap-list', 'control_sitemap_list_template_data' );
function control_sitemap_list_template_data( $template_data ) {
  $template_data = array_merge(
    array(
      'post_type'        => '',
      'orderby'          => 'title',
      'order'            => 'ASC',
      'apply_filters'    => '',
      'associated_posts' => array(),
    ),
    $template_data
  );

  if ( ! empty( $template_data['post_type'] ) && empty( $template_data['associated_posts'] ) ) {
    $query = new WP_Query(
      array(
        'post_type'      => $template_data['post_type'],
        'posts_per_page' => -1,
        'orderby'        => $template_data['orderby'],
        'order'          => $template_data['order'],
      )
    );

    if ( $query->have_posts() ) {
      $template_data['associated_posts'] = $query->posts;
    }
  }

  if ( ! empty( $template_data['apply_filters'] ) ) {
    $template_data['associated_posts'] = apply_filters( $template_data['apply_filters'], $template_data['associated_posts'] );
  }

  return $template_data;
}
