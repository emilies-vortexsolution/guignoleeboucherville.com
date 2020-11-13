<?php

add_action(
  'rest_api_init',
  function () {

    register_rest_route(
      'search',
      'index/(?P<lang>[a-z]+)(?:/(?P<key>[a-zA-Z0-9-]+))?',
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'get_json_index',
        'permission_callback' => '__return_true',
      )
    );

  }
);


 /**
  * Get Json Index
  *
  * @param string Lang [en/fr] or main if WPML is not installed
  * @param string Key [a-zA-Z0-9-] (optional) private key. Required to get the private index
  * @return array
  */
function get_json_index( $data ) {
  $lang = ( ! empty( (string) $data['lang'] ) ? (string) $data['lang'] : null );
  $key  = ( ! empty( $data['key'] ) ? $data['key'] : false );

  /* Prevent call of main language who does not exist if WPMl is activated */
  if ( function_exists( 'icl_object_id' ) ) {
    $autocomplete_index = autocomplete_json( false, array() );
    $autocomplete_index = ( ! empty( $autocomplete_index[ $lang ] ) ? $autocomplete_index[ $lang ] : array() );
  } else {
    $autocomplete_index = autocomplete_json( false, array() )['main'];
  }

  /* Filter the index with this key */
  $autorised_index = get_autorised_index( $autocomplete_index, $key );

  return new WP_REST_Response( $autorised_index, 200 );
}
