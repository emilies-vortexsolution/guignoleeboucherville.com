<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );
function setup() {

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain( 'vtx', get_template_directory() . '/lang' );
  load_theme_textdomain( 'vtx-admin', get_template_directory() . '/lang' );
  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support( 'title-tag' );

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus(
    array(
      'primary_navigation' => __( 'Primary Navigation', 'vtx' ),
      'mobile_navigation'  => __( 'Mobile Navigation', 'vtx' ),
    )
  );

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support( 'post-thumbnails' );

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ) );

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

  add_theme_support( 'woocommerce' );

  add_theme_support( 'editor-styles' );
  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style( Assets\asset_path( 'styles/editor.css' ) );
}

/**
 * Register sidebars
 */
add_action( 'widgets_init', __NAMESPACE__ . '\\widgets_init' );
function widgets_init() {
  register_sidebar(
    array(
      'name'          => __( 'Primary', 'vtx' ),
      'id'            => 'sidebar-primary',
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
    )
  );

  register_sidebar(
    array(
      'name'          => __( 'Footer', 'vtx' ),
      'id'            => 'sidebar-footer',
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
    )
  );
}

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
  static $display;

  if ( ! isset( $display ) ) {
    $display = ! in_array(
      true,
      array(
        // The sidebar will NOT be displayed if ANY of the following return true.
        // @link https://codex.wordpress.org/Conditional_Tags
        is_404(),
        is_front_page(),
        is_page_template( 'template-custom.php' ),
      ),
      true
    );
  }

  return apply_filters( 'sage/display_sidebar', $display );
}

/**
 * Theme assets
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100 );
function assets() {

  wp_enqueue_style( 'google_font', '//fonts.googleapis.com/css?family=Lato:400,700,900', false, null );

  wp_enqueue_style( 'sage-css', Assets\asset_path( 'styles/main.css' ), false, null );

  if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  if ( ! is_admin() ) {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'https://code.jquery.com/jquery-3.5.1.min.js', array(), null, true );
  }

  wp_enqueue_script( 'modernizr', Assets\asset_path( 'scripts/modernizr.js' ), array(), null, false );
  wp_enqueue_script( 'polyfills', Assets\asset_path( 'scripts/polyfills.js' ), array(), null, false );

  // fancybox
  wp_register_script( 'fancybox-js', Assets\asset_path( 'scripts/fancybox.js' ), array(), null, true );
  wp_register_style( 'fancybox-css', Assets\asset_path( 'styles/fancybox.css' ), false, null );
  
  wp_enqueue_script( 'sage-js', Assets\asset_path( 'scripts/main.js' ) . '#async', array( 'jquery', 'wp-i18n' ), null, true );

  // Here goes use AJAX, add some pre-translated strings or usefull data to the main JS
  wp_localize_script( 'sage-js', 'theme', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

  if ( function_exists( 'wp_set_script_translations' ) ) {
    wp_set_script_translations( 'sage-js', 'vtx', get_stylesheet_directory() . 'lang' );
  }

  // Removed wpml admin bar css if admin bar is not displayed
  global $wp_styles;
  if ( ! is_admin() && isset( $wp_styles->registered['wpml-tm-admin-bar'] ) ) {
    wp_dequeue_style( 'wpml-tm-admin-bar' );
  }
}

//add preconnect for jquery
add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\wp_resource_hints', 10, 2 );
function wp_resource_hints( $urls, $relation_type ) {
  if ( 'preconnect' === $relation_type ) {
    $urls[] = 'https://code.jquery.com';
    $urls   = array_unique( $urls );
  }
  return $urls;
}

// Allow to use #async or #defer while enqueuing file
add_filter( 'script_loader_tag', __NAMESPACE__ . '\\js_async_attr', 10 );
function js_async_attr( $tag ) {
  if ( false !== strpos( $tag, '#async' ) ) {
    $tag = str_replace( '#async', '', $tag );
    return str_replace( ' src', ' async src', $tag );
  }
  if ( false !== strpos( $tag, '#defer' ) ) {
    $tag = str_replace( '#defer', '', $tag );
    return str_replace( ' src', ' defer src', $tag );
  }
  return $tag;
}

/**
 * Theme assets for admin only
 */
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_assets', 100 );
function admin_assets() {
  wp_enqueue_style( 'sage-admin-css', Assets\asset_path( 'styles/admin.css' ), false, null );
  wp_enqueue_script( 'sage-admin-js', Assets\asset_path( 'scripts/admin.js' ), array( 'jquery' ), null, true );
}

/**
 * Hide template-custom from the page template dropdown
 */
add_filter( 'theme_page_templates', __NAMESPACE__ . '\\remove_page_template_from_dropdown' );
function remove_page_template_from_dropdown( $pages_templates ) {
  unset( $pages_templates['template-custom.php'] );
  return $pages_templates;
}

/**
 * Usefull to remove the default 404 favicon error
 */
add_action( 'admin_head', __NAMESPACE__ . '\\add_admin_favicon' );
add_action( 'login_head', __NAMESPACE__ . '\\add_admin_favicon' );
function add_admin_favicon() {
  include get_template_directory() . '/templates/favicon.php';
}

/**
 * Looking to see if the front-end page needs redirection.
 * The given URL is an ACF with it's own json file already in this theme.
 */
add_action( 'template_redirect', __NAMESPACE__ . '\\check_if_redirect' );
function check_if_redirect() {

  $link_url = '';

  if ( is_page_template( 'template-redirect.php' ) ) {
    $link_url = get_field( 'page_redirect_url' );
  }

  if ( '' !== $link_url ) {
    if ( wp_safe_redirect( $link_url, 301 ) ) {
      exit();
    }
  }
}

////////////////// REMOVE COMMENTS /////////////////////
/**
 * Removes from admin menu
 */
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_comment_in_admin_menu' );
function remove_comment_in_admin_menu() {
  remove_menu_page( 'edit-comments.php' );
}

/**
 * Removes from post and pages
 */
add_action( 'init', __NAMESPACE__ . '\\remove_comment_post_type_support', 100 );
function remove_comment_post_type_support() {
  remove_post_type_support( 'post', 'comments' );
  remove_post_type_support( 'page', 'comments' );
  // Add othe post types if needed
  // ...
}

/**
 * Removes from admin bar
 */
add_action( 'wp_before_admin_bar_render', __NAMESPACE__ . '\\remove_comment_on_admin_bar' );
function remove_comment_on_admin_bar() {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu( 'comments' );
}

// https://gist.github.com/eriteric/5d6ca5969a662339c4b3
add_filter( 'gform_init_scripts_footer', '__return_true' );
add_filter( 'gform_cdata_open', __NAMESPACE__ . '\\wrap_gform_cdata_open', 1 );
add_filter( 'gform_cdata_close', __NAMESPACE__ . '\\wrap_gform_cdata_close', 99 );

function wrap_gform_cdata_open( $content = '' ) {
  if ( ! do_wrap_gform_cdata() ) {
    return $content;
  }
  $content = 'document.addEventListener( "DOMContentLoaded", function() { ' . $content;
  return $content;
}

function wrap_gform_cdata_close( $content = '' ) {
  if ( ! do_wrap_gform_cdata() ) {
    return $content;
  }
  $content .= ' }, false );';
  return $content;
}

function do_wrap_gform_cdata() {
  if (
    is_admin() ||
    ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
    isset( $_POST['gform_ajax'] ) || // phpcs:ignore WordPress.Security.NonceVerification.Missing
    isset( $_GET['gf_page'] ) || // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    doing_action( 'wp_footer' ) ||
    did_action( 'wp_footer' )
  ) {
    return false;
  }
  return true;
}
