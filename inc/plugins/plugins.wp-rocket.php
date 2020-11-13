<?php

add_action(
  'rest_api_init',
  function () {

    register_rest_route(
      'vtx/v1',
      '/cache/clean',
      array(
        'methods'             => 'GET',
        'callback'            => 'vtx_clean_caches',
        'permission_callback' => '__return_true',
      )
    );
  }
);

function vtx_clean_caches() {
  // Clear cache.
  if ( function_exists( 'rocket_clean_domain' ) ) {
    rocket_clean_domain();
  }
}
