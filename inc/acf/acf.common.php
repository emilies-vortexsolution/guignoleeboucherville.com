<?php

/**
 * @param array $link_info
 * @param array $extra_attr
 * @param boolean $return_only_wrapper_start
 *
 * @return string
 * @author MADL
 *
 * Permet de constuire le HTML nécessaire à partir d'un champ ACF de type Lien
 * Pour un champ de type « link » ayant le return configuré à « array »
 */
function get_html_acf_link_array( array $link_info, array $extra_attr = array(), bool $return_only_wrapper_start = false ): string {

  if ( ! isset( $link_info['url'] ) ) {
    return '';
  }

  $link_target_attr = '';

  if ( ! empty( $link_info['target'] ) ) {
    $link_target_attr = "target='{$link_info['target']}'";

    if ( '_blank' === $link_info['target'] ) {

      if ( isset( $extra_attr['rel'] ) && is_string( $extra_attr['rel'] ) ) {
        $extra_attr['rel'] .= 'noopener noreferrer';
      } else {
        $extra_attr['rel'] = 'noopener noreferrer';
      }
    }
  }

  $link_extra_attr = convert_array_to_html_attr( $extra_attr );

  $wrapper_start = "<a href='{$link_info['url']}' $link_target_attr $link_extra_attr>";

  if ( $return_only_wrapper_start ) {
    return $wrapper_start;
  }

  $link_title = ( ! empty( $link_info['title'] ) ) ? $link_info['title'] : '';

  return "{$wrapper_start}{$link_title}</a>";
}


/**
 * Turn posted acf to an associatif array with properly named keys.
 * Works with nested arrays
 *
 * @author Sariha Chabert <sariha.c@vortexsolution.com>
 *
 * @param array $data
 *
 * @return array
 */
function acf_replace_key_with_fieldnames( $data = array() ) {
    $data_array = array();

  foreach ( $data as $key => $value ) {
    if ( is_numeric( $key ) ) {
          return $data;
    }

      $field = acf_get_field( $key );
    if ( ! empty( $field['name'] ) ) {
        $data_array[ $field['name'] ] = $value;
    }

    if ( is_array( $value ) ) {
      if ( ! empty( $field['name'] ) ) {
          $data_array[ $field['name'] ] = acf_replace_key_with_fieldnames( $value );
      } else {
          $data_array[] = acf_replace_key_with_fieldnames( $value );
      }
    }
  }

  return $data_array;
}


/*
 * hide ACF MENU if not local ext
 */
add_filter( 'acf/settings/show_admin', 'vtx_acf_show_admin' );
function vtx_acf_show_admin( $show_admin ) {

  $site_url  = get_site_url();
  $url_parts = explode( '.', wp_parse_url( $site_url, PHP_URL_HOST ) );
  $tld       = end( $url_parts );

  if ( 'local' !== $tld && 'vtx' !== $tld ) {
    return false;
  }

  return $show_admin;
}


function get_field_from_default_lang( $field_name, $acf_object_type = false ) {
  global $sitepress;

  if ( ! $field_name ) {
    return '';
  }

  // On retourne directement le champs s'il n'y a pas de WPML.
  if ( ! isset( $sitepress ) ) {
    return get_field( $field_name, $acf_object_type );
  }

  add_filter(
    'acf/settings/current_language',
    function() {
      global $sitepress;
      return $sitepress->get_default_language();
    }
  );

  // Le premier essaie de retrouver un champs donne toujours null. On en déclenche un juste.
  get_field( 'dummy_not_existing_field' );

  $field_value = get_field( $field_name, $acf_object_type );

  add_filter(
    'acf/settings/current_language',
    function() {
      return apply_filters( 'wpml_current_language', null );
    }
  );

  return $field_value;
}

//add custom folder for acf-json
add_filter(
  'acf/json_directory',
  function () {
    return get_template_directory() . '/settings/acf-json';
  }
);


add_filter(
  'acf/settings/save_json',
  function () {
    return apply_filters( 'acf/json_directory', null );

  }
);

add_filter(
  'acf/settings/load_json',
  function () {
    return array(
      apply_filters( 'acf/json_directory', null ),
    );

  }
);
