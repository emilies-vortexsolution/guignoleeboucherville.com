<?php

/**
 * Update autocomplete index on un publish (delete indexed keyword from relevanssi index)
 */
function post_unpublished( $new_status, $old_status, $post ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  if ( 'publish' === $old_status && 'publish' !== $new_status ) {
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}relevanssi` WHERE `doc` = %d", $post->ID ) );
  }
}
add_action( 'transition_post_status', 'post_unpublished', 10, 3 );


/**
 * Update autocomplete index on post save (force partial relevanssi index and re-run autocomplete index)
 */
function update_search_transient_on_save_post( $post_id, $post ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  if ( in_array( $post->post_type, get_global_category_post_types(), true ) ) {
    $indexing[0] = relevanssi_build_index( true, false );
    if ( $indexing[0] ) {
      if ( 'publish' === $post->post_status ) {
        autocomplete_json( true );
      } else {
        autocomplete_json( true, array( $post_id ) ); //we regenerate our index and excluding this post if the status is not 'publish'
      }
    }
  }
}
add_action( 'save_post', 'update_search_transient_on_save_post', 10, 3 );


/**
 * Update autocomplete index on option page save
 */
add_action( 'acf/save_post', 'update_search_transient_on_save_option', 100 );
function update_search_transient_on_save_option() {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  $screen = get_current_screen();
  if ( 'options_page_global_search_options' === $screen->id ) {
    $indexing[0] = relevanssi_build_index( true, false );
    if ( $indexing[0] ) {
      autocomplete_json( true );
    }
  }
}


function valid_admin_status_to_index() {
  return array(); /* We remove all exception. Indexed post are for admin AND public. Because our custom indexing is working while admin, he index like an admin */
}
add_filter( 'relevanssi_valid_admin_status', 'valid_admin_status_to_index', 10 );


/**
 * Inject autocomplete template in wp_head
 */
add_action( 'wp_head', 'inject_autocomplete_template' );
function inject_autocomplete_template() {
  ?>
    <script type="text/tmpl" id="tmpl-autocomplete-item">
      <li class="search-item {{ data.Selected }}" id="search-item-{{ data.ID }}"> <a href="{{ data.URL }}" data-og-text="{{ data.Title }}">{{ data.Title }}</a> </li>
    </script>
  <?php
}
