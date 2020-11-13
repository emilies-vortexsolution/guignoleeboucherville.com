<?php

/**
 * Return post to include id_list
 * Return the args for the search query
 * @return array
 */
function get_posts_to_include( $exclued_posts = array() ) {

  $args = array(
    'post_type'           => get_global_category_post_types(),
    'posts_per_page'      => -1,
    'ignore_sticky_posts' => true,
    'post_status'         => 'published',
    'fields'              => 'ids',
    'meta_query'          => array(
      'relation' => 'OR',
      array(
        'key'     => '_wp_page_template',
        'compare' => 'NOT EXISTS',
      ),
      array(
        'key'     => '_wp_page_template',
        'compare' => 'NOT LIKE',
        'value'   => 'template-redirect.php',
      ),
    ),
  );

  /* If WPML is installed */
  if ( function_exists( 'icl_object_id' ) ) {
    $args['suppress_filters'] = true;
  }

  $wp_query_posts_to_include = new WP_Query( $args );
  $posts_to_include          = $wp_query_posts_to_include->posts;

  $exclued_posts = apply_filters( 'vtx_search_autocomplete_exclued_posts', $exclued_posts, $posts_to_include );

  if ( ! empty( $exclued_posts ) ) {
    foreach ( $exclued_posts as $exclued_post ) {
      if ( ! empty( $posts_to_include[ $exclued_post ] ) ) {
        unset( $posts_to_include[ $exclued_post ] );
      }
    }
  }

  return $posts_to_include;
}


/**
 * Get global category post types
 * This function return an array of cpt defined in the admin in the field `global_search_cpt_inclued`
 * @return array
 */
function get_global_category_post_types() {

  $cpt_list = get_option( 'relevanssi_index_post_types' );

  if ( ! empty( $cpt_list ) ) :
    return $cpt_list;
  else :
    return array( 'page', 'post' );
  endif;
}



/**
 * Sanitize autocomplete text
 * This function return text with the proper character avoiding fuze.js encoding errors
 * @param string $text Text to sanitize
 * @return string Sanitized string
 */
function sanitize_autocomplete_text( $text ) {

  $text = str_replace(
    array(
      '&rsquo;',
      '&#39;',
      '&#8217',
      '&#8211',
      '\'',
      ';',
      '"',
    ),
    array(
      '’',
      '’',
      '’',
      '-',
      '’',
      '',
      '’’',
    ),
    (string) $text
  );

  $text = html_entity_decode( $text );

  return $text;
}




