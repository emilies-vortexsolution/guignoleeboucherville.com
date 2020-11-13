<?php

add_action( 'current_screen', 'disable_unilangual_fields_from_option_pages', 1 );
function disable_unilangual_fields_from_option_pages() {
  global $acf_options_page;

  if ( ! class_exists( 'SitePress' ) ) {
    // Do this only if WPML is active.
    return;
  }

  // @codingStandardsIgnoreLine
  if ( isset( $_GET['page'] ) && ! empty( $acf_options_page ) && ! empty( $acf_options_page->pages ) ) {

    // @codingStandardsIgnoreLine
    if ( isset( $acf_options_page->pages[ $_GET['page'] ] ) ) {
      // @codingStandardsIgnoreLine
      $redirect_page_to_default_lang = isset( $acf_options_page->pages[ $_GET['page'] ]['redirect_to_default_lang'] ) && $acf_options_page->pages[ $_GET['page'] ]['redirect_to_default_lang'];

      if ( $redirect_page_to_default_lang ) {

        if ( ! page_is_default_lang() ) {
          $translated_page_url = site_url() . add_query_arg( 'lang', apply_filters( 'wpml_default_language', null ) );
          wp_safe_redirect( $translated_page_url, 301 );
          exit;

        } else {
          add_action( 'admin_notices', 'option_has_default_language_redirect' );

        }
      }
    }
  }
}

add_action( 'current_screen', 'check_for_option_page_with_flush_rewrite', 10 );
function check_for_option_page_with_flush_rewrite() {
  global $acf_options_page;

  // @codingStandardsIgnoreLine
  if ( isset( $_GET['page'] ) && ! empty( $acf_options_page ) && ! empty( $acf_options_page->pages ) ) {
    // @codingStandardsIgnoreLine
    if ( isset( $acf_options_page->pages[ $_GET['page'] ] ) ) {
      // @codingStandardsIgnoreLine
      $do_flush_rewrite = isset( $acf_options_page->pages[ $_GET['page'] ]['do_flush_rewrite'] ) && $acf_options_page->pages[ $_GET['page'] ]['do_flush_rewrite'];

      if ( $do_flush_rewrite ) {
        flush_rewrite_rules();
      }
    }
  }

}


function option_has_default_language_redirect() {
  add_settings_error(
    'default-language-redirect',
    'default-language-redirect',
    _x( 'This page is only editable in this language. Changing language will redirect to this page.', 'Page unilingual notice', 'vtx-admin' ),
    'notice-warning is-not-dismissible'
  );
  settings_errors( 'default-language-redirect' );
}
