<?php
///////////////////////////////////////////////////////
//                FOR PUBLIC USAGE                   //
///////////////////////////////////////////////////////

/**
 * @param string $post_type_slug
 *
 * @return object L'objet de la page associé au post type.
 */
function get_page_linked_to_post_type( $post_type_slug ) {

  $link_for_post_types = get_page_and_post_type_links_option();

  if ( ! empty( $link_for_post_types ) ) {
    foreach ( $link_for_post_types as $link_info ) {
      if ( isset( $link_info['post_type_linked_to_page'][ $post_type_slug ] ) && ! empty( $link_info['page_linked_to_post_type'] ) ) {
        return $link_info['page_linked_to_post_type'];
      }
    }
  }

  return false;
}

/**
 * @param int|WP_Post $page L'objet ou le ID d'une page
 *
 * @return array Ex.: [ "post_type_slug" => "post_type_slug" ] ou [] si il n'y en a pas.
 */
function get_post_type_linked_to_page( $page_id ) {
  $page_id = sanitize_param_post_id( $page_id );
  if ( ! $page_id ) {
    return array();
  }
  $link_for_post_types = get_page_and_post_type_links_option();
  if ( ! empty( $link_for_post_types ) ) {
    foreach ( $link_for_post_types as $link_info ) {
      if ( ! empty( $link_info['page_linked_to_post_type'] ) && $link_info['page_linked_to_post_type']->ID === $page_id ) {
        if ( isset( $link_info['post_type_linked_to_page'] ) ) {
          return $link_info['post_type_linked_to_page'];
        }
        break;
      }
    }
  }
  return array();
}



///////////////////////////////////////////////////////
//                 PRIVATE HELPERS                   //
///////////////////////////////////////////////////////

function sanitize_url_for_page_to_post_type_rewrite_rules( $post, $language_code = '' ) {
  $url = '';
  $url = get_the_relative_uri( $post );
  $url = remove_language_from_url( $url, $language_code );
  return trim( $url, '/' );
}

function get_post_id_that_maybe_not_from_the_current_language_from_page( $page, $language_code = null ) {
  global $sitepress;

  if ( isset( $sitepress ) ) {
    return apply_filters( 'wpml_object_id', $page->ID, $page->post_type, false, $language_code );
  }

  return $page->ID;
}

function get_page_and_post_type_links_option() {
  global $page_and_post_type_links_option;

  if ( isset( $page_and_post_type_links_option ) ) {
    return $page_and_post_type_links_option;
  }

  $page_and_post_type_links_option = get_field_from_default_lang( 'page_and_post_type_links', 'option' );

  return $page_and_post_type_links_option;
}



///////////////////////////////////////////////////////
//                  ACTIONS/FILTERS                  //
///////////////////////////////////////////////////////

if ( function_exists( 'get_field' ) ) {
  add_action( 'wp_loaded', 'add_rewrite_rules_for_post_type_and_page_link', 2 );
}
function add_rewrite_rules_for_post_type_and_page_link() {

  $custom_archives = get_page_and_post_type_links_option();

  if ( ! empty( $custom_archives ) && is_array( $custom_archives ) ) {

    $site_url = get_site_url();

    if ( function_exists( 'icl_get_languages' ) ) {
      $site_languages = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
    }

    // Si WPML n'est pas installé ou n'a pas été configuré, on doit falsifier le tableau avec la constante qu'on alimente par défaut dans le thème même si WPML n'est pas actif.
    if ( empty( $site_languages ) ) {
      $lang           = ( ! empty( ICL_LANGUAGE_CODE ) ) ? ICL_LANGUAGE_CODE : THEME_DEFAULT_LANGUAGE_CODE;
      $site_languages = array( array( $lang => $lang ) );
    }

    foreach ( $custom_archives as $archive_info ) {

      if ( ! empty( $archive_info['page_linked_to_post_type'] ) && ! empty( $archive_info['post_type_linked_to_page'] ) ) {

        if ( ! is_array( $archive_info['post_type_linked_to_page'] ) ) {
          $archive_info['post_type_linked_to_page'] = array( $archive_info['post_type_linked_to_page'] );
        }

        foreach ( $archive_info['post_type_linked_to_page'] as $post_type ) {
          foreach ( $site_languages as $language_code => $language_info ) {

            $translated_post_id = get_post_id_that_maybe_not_from_the_current_language_from_page( $archive_info['page_linked_to_post_type'], $language_code );

            if ( $translated_post_id ) {

              $page                      = get_post( $translated_post_id );
              $post_type_uri             = sanitize_url_for_page_to_post_type_rewrite_rules( $page, $language_code );
              $post_type_object          = get_post_type_object( $post_type );
              $post_type_is_hierarchical = false;

              if ( ! empty( $post_type_object ) ) {
                $post_type_is_hierarchical = $post_type_object->hierarchical;

                // Add a default slug if none exists
                if ( empty( $post_type_object->rewrite ) || empty( $post_type_object->rewrite['slug'] ) ) {
                  $post_type_object->rewrite['slug'] = $post_type_uri;
                  // Or replace the placeholder if found.
                } elseif ( ! empty( $post_type_object->rewrite['slug'] ) && false !== strpos( $post_type_object->rewrite['slug'], '%page_archive_uri%' ) ) {
                  $post_type_uri = str_replace( '%page_archive_uri%', $post_type_uri, $post_type_object->rewrite['slug'] );
                }
              }

              if ( $post_type_is_hierarchical ) {
                if ( 'post' === $post_type ) {
                  add_rewrite_rule( $post_type_uri . '/(.+/)([^/]+)/?$', 'index.php?page=&name=$matches[1]$matches[2]', 'top' );
                } else {
                  add_rewrite_rule( $post_type_uri . '/(.+/)([^/]+)/?$', 'index.php?' . $post_type . '=$matches[1]$matches[2]', 'top' );
                }
              }

              if ( 'post' === $post_type ) {
                add_rewrite_rule( $post_type_uri . '/([^/]+)/?$', 'index.php?page=&name=$matches[1]', 'top' );
              } else {
                add_rewrite_rule( $post_type_uri . '/([^/]+)/?$', 'index.php?' . $post_type . '=$matches[1]', 'top' );
              }
            }
          }
        }
      }
    }
  }
}


if ( function_exists( 'get_field' ) ) {
  add_filter( 'post_link', 'replace_placeholder_in_permalink_for_post_type_linked_to_page', 10, 3 );
  add_filter( 'post_type_link', 'replace_placeholder_in_permalink_for_post_type_linked_to_page', 10, 3 );
}
function replace_placeholder_in_permalink_for_post_type_linked_to_page( $post_link, $post, $leavename ) {

  if ( $post && 'publish' === $post->post_status ) {

    $linked_page = get_page_linked_to_post_type( $post->post_type );

    // Bail if this post type is not linked to a page
    if ( ! empty( $linked_page ) ) {

      $translated_post_id = get_post_id_that_maybe_not_from_the_current_language_from_page( $linked_page );

      remove_filter( 'post_type_link', 'replace_placeholder_in_permalink_for_post_type_linked_to_page' );

      // Modifier le URI qui précède le slug du post actuel.
      if ( false !== strpos( $post_link, '%page_archive_uri%' ) ) {
        $post_link = str_replace( '%page_archive_uri%', sanitize_url_for_page_to_post_type_rewrite_rules( $translated_post_id ), $post_link );

      } else {

        // Si %page_archive_uri% ne se trouve pas à l'intérieur de l'URL, on assume qu'on doit modifier toute la partie avant le post_name.
        // Il faut remettre '%postname%' pour qu'on puisse continuer d'éditer le slug dans l'admin.
        $post_link = get_the_permalink( $translated_post_id );

        if ( $leavename ) {
          $post_link .= '%postname%/';
        } else {
          $post_type_object          = get_post_type_object( $post->post_type );
          $post_type_is_hierarchical = false;

          if ( ! empty( $post_type_object ) ) {
            $post_type_is_hierarchical = ! empty( $post_type_object->hierarchical );
          }

          if ( $post_type_is_hierarchical ) {
            $post_link .= get_page_uri( $post ) . '/';
          } else {
            $post_link .= "{$post->post_name}/";
          }
        }
      }

      add_filter( 'post_type_link', 'replace_placeholder_in_permalink_for_post_type_linked_to_page', 10, 3 );
    }
  }

  return $post_link;
}


add_action( 'save_post', 'do_flush_rewrite_if_slug_has_change', 10, 3 );
function do_flush_rewrite_if_slug_has_change( $post, $update ) {
  if ( $update && is_object( $post ) && 'page' === $post->post_type ) {
    flush_rewrite_rules();
  }
}

// Clear cache of related page to current post type beging saved / updated
add_action( 'save_post', 'clear_wp_rocket_cache', 99, 3 );
// @codingStandardsIgnoreStart
function clear_wp_rocket_cache( $post_id, $post, $update ) {
// @codingStandardsIgnoreEnd

  if ( function_exists( 'rocket_clean_post' ) ) {
    $link_for_post_types = get_page_and_post_type_links_option();
    if ( ! empty( $link_for_post_types ) ) {
      foreach ( $link_for_post_types as $link_info ) {
        if ( current( $link_info['post_type_linked_to_page'] ) === $post->post_type ) {
          rocket_clean_post( $link_info['page_linked_to_post_type']->ID );
          break;
        }
      }
    }
  }
}
