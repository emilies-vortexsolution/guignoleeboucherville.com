<?php
/**
 * Search can be inited
 *  Returns true if the conditions are met correctly for the plugin to work.
 * @return boolean
 */
function search_can_be_inited() {
  $init = false;

  if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
  }

  $acf_activated        = is_plugin_active( 'advanced-custom-fields-pro/acf.php' );
  $relevanssi_activated = ( is_plugin_active( 'relevanssi-premium/relevanssi.php' ) ? true : ( is_plugin_active( 'relevanssi/relevanssi.php' ) ? true : false ) );

  if ( $relevanssi_activated && $acf_activated ) {
    $init = true;
  }

  return $init;
}


///// Logged KEY /////
define( 'LOGGED_SECRET_KEY', 'Vm9ydGV4U29sdXRpb24' );


/**
 * Get user Index Key
 *
 * @return string
 */
function get_user_index_key() {
  return LOGGED_SECRET_KEY;
}

/**
 * Is user key valid
 *
 * @return boolean
 */
function is_user_key_valid( $user_key ) {
  $is_key_valid = ( LOGGED_SECRET_KEY === $user_key );
  return apply_filters( 'vtx_search_is_user_key_valid', $is_key_valid, $user_key );
}

/**
 * Get autorised index
 *
 * @param array $autocomplete_index the index ready to be sent to the rest endpoint
 * @param string $key_valid the key
 * @return array
 */
function get_autorised_index( $autocomplete_index, $key ) {

  $key_valid = ( ! empty( $key ) ? is_user_key_valid( $key ) : false );

  $returned_index = array();
  if ( ! empty( $autocomplete_index ) ) {
    foreach ( $autocomplete_index as $index_item ) {
      if ( empty( $index_item['PrivatePage'] ) || true === $index_item['PrivatePage'] && true === $key_valid ) {
        unset( $index_item['PrivatePage'] );
        $returned_index[] = $index_item;
      }
    }
  }
  return apply_filters( 'vtx_search_get_autorised_index', $returned_index, $autocomplete_index, $key_valid );

}


/**
 * Is this page private
 *
 * @param [type] $post_id
 * @return boolean
 */
function is_this_page_private( $post_id ) {

  $is_private_post_field = ( 'public' !== get_post_status( $post_id ) );
  $return                = ( ! empty( $is_private_post_field ) ? $is_private_post_field : false );

  return apply_filters( 'vtx_search_is_this_page_private', $return, $post_id );

}




/**
 * Js search options
 *
 * @return array Options values sent to global-search.js
 */
function js_search_options() {
  $return = array(
    'autocomplete_search_version' => get_field( 'autocomplete_search_version', 'options' ),
    'language'                    => ( function_exists( 'icl_object_id' ) ? ICL_LANGUAGE_CODE : 'main' ),
    'secret_key'                  => ( is_user_logged_in() ? get_user_index_key() : false ),
    'search_label'                => esc_html_x( 'Search', 'AutocompleteSearch', 'vtx' ),
  );

  return apply_filters( 'vtx_search_js_search_options', $return );
}




/**
 * Autocomplete Json
 *  This function generate, cache and return a array of the index, ready to be sent to the endpoint.
 * @param boolean $force_update Default: False. If true, force the index update and re-cache the new version.
 * @param array $posts_id_to_exclude Default: null. If set, he will add this post ID to the exclued post list (If this function is called from a post hook)
 * @param string $Lang Default: main. return the index for a specified language
 * @param boolean $private_index Default: False. If true, return private pages.
 * @return array
 */
function autocomplete_json( $force_update = false, $posts_id_to_exclude = array() ) {

  /**
   * Relevanssi index filter
   */
  $minimum_word_occurrence = 2;
  $minimum_word_char       = 4;

  $encoded_autocomplete_search = get_transient( 'autocomplete_search' );

  if ( empty( $encoded_autocomplete_search ) || $force_update ) {

    global $wpdb;

    $relevanssi_index = $wpdb->get_results( $wpdb->prepare( "SELECT `doc` as ID, `post_title`, GROUP_CONCAT(DISTINCT `term` SEPARATOR %s) as term FROM `{$wpdb->prefix}relevanssi` LEFT JOIN `{$wpdb->prefix}posts` on `doc` = `wp_posts`.`ID` WHERE `content` >= %d && CHAR_LENGTH(`term`) >= %d  GROUP BY `doc`", ' ', $minimum_word_occurrence, $minimum_word_char ), ARRAY_A );

    $multilanguage = function_exists( 'icl_object_id' );

    $autocomplete_search_values = array();
    $posts_to_include           = get_posts_to_include( $posts_id_to_exclude );
    foreach ( $relevanssi_index as $index ) {

      if ( in_array( (int) $index['ID'], $posts_to_include, true ) ) {

          $lang                                  = ( $multilanguage ? apply_filters( 'wpml_post_language_details', null, (int) $index['ID'] )['language_code'] : 'main' );
          $autocomplete_search_values[ $lang ][] = array(
            'ID'          => (int) $index['ID'],
            'Title'       => sanitize_autocomplete_text( $index['post_title'] ),
            'Tags'        => sanitize_autocomplete_text( $index['term'] ),
            'ManualTags'  => '',
            'PrivatePage' => is_this_page_private( (int) $index['ID'] ),
          );
      }
    }

    $manual_keywords = get_manual_keywords();

    if ( ! empty( $manual_keywords ) ) {
      foreach ( $manual_keywords as $lang => $manual_keywords_by_lang ) {
        foreach ( $manual_keywords_by_lang as $manual_keyword_by_lang ) {
          $autocomplete_search_values[ $lang ][] = $manual_keyword_by_lang;
        }
      }
    }

    $autocomplete_search_values = apply_filters( 'vtx_search_autocomplete_json', $autocomplete_search_values );

    set_transient( 'autocomplete_search', wp_json_encode( $autocomplete_search_values ), 24 * HOUR_IN_SECONDS * 14 ); //2 weeks
    update_field( 'autocomplete_search_version', time(), 'options' );

  } else {
    $autocomplete_search_values = json_decode( $encoded_autocomplete_search, true );
  }

  return $autocomplete_search_values;

}


/**
 * Get manual keywords
 * This function return the manual results provided in the admin
 * @return array
 */
function get_manual_keywords() {

  if ( function_exists( 'icl_object_id' ) ) {
    $langs = apply_filters( 'wpml_active_languages', null, 'orderby=id&order=desc' );
    foreach ( $langs as $lang ) {
      do_action( 'wpml_switch_language', $lang['code'] );
      $manual_keywords[ $lang['code'] ] = get_field( 'manual_keywords', 'options' );
    }
  } else {
    $manual_keywords['main'] = get_field( 'manual_keywords', 'options' );

  }

  foreach ( $manual_keywords as $lang => $manual_keywords_by_lang ) {

    if ( ! empty( $manual_keywords_by_lang ) && is_array( $manual_keywords_by_lang ) ) {
      foreach ( $manual_keywords_by_lang as $manual_keyword_by_lang ) {
        $id               = $manual_keyword_by_lang['page_a_lier'];
        $url_a_lier       = ( ! empty( $manual_keyword_by_lang['url_a_lier'] ) ? $manual_keyword_by_lang['url_a_lier'] : '' );
        $private_keywords = ( ! empty( $manual_keyword_by_lang['private_keywords'] ) ? $manual_keyword_by_lang['private_keywords'] : false );

        $keyword_list   = array();
        $keyword_list[] = $manual_keyword_by_lang['sugestion'];

        foreach ( $manual_keyword_by_lang['keywords'] as $keyword ) {
          array_push( $keyword_list, sanitize_autocomplete_text( $keyword['keyword'] ) );
        }

        $manual_keyword_by_lang_item      = array(
          'ID'          => $id,
          'Title'       => $manual_keyword_by_lang['sugestion'],
          'Url_a_lier'  => $url_a_lier,
          'ManualTags'  => $keyword_list,
          'PrivatePage' => $private_keywords,
        );
        $manual_keywords_array[ $lang ][] = $manual_keyword_by_lang_item;
      }
    }
  }

  return $manual_keywords_array;

}


/**
 * Display the autocompelte UL
 *
 * @return void
 */
function display_autocomplete() {
  echo wp_kses_post( '<ul data-searchtext="' . _x( 'Search: ', 'autocomplete', 'vtx' ) . '" class="autocomplete search-results__group__items" style="display: none;"></ul>' );
}
