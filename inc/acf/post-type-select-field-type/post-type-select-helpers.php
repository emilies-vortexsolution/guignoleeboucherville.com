<?php
function get_and_increment_post_type_checkbox_total_count() {
  global $post_type_select_count;
  if ( ! isset( $post_type_select_count ) ) {
    $post_type_select_count = 0;
  }

  return ++$post_type_select_count;
}

function compare_post_type_label( $a, $b ) {
  return strcmp( $a->label, $b->label );
}

/**
 * Fonction affichant l'icône d'un post type.
 * (?) Est très générale et est possiblement déjà incluse avec le thème d'où le function_exists (?)
 */
if ( ! function_exists( 'display_post_type_icon' ) ) {
  function display_post_type_icon( $post_type ) {
    if ( is_string( $post_type ) ) {
      $post_type = get_post_type_object( $post_type );
    }

    // RETURN si le post type n'existe pas
    if ( ! $post_type ) {
      return '';
    }

    $menu_icon_info = process_post_type_icon_param( $post_type );

    if ( ! empty( $menu_icon_info['src'] ) ) {

      echo wp_kses_post( "<img class='{$menu_icon_info['class']}' src='{$menu_icon_info['src']}'>" );

    } else {

      echo wp_kses_post( "<i class='dashicons-before {$menu_icon_info['class']}'></i>" );

    }

  }
}


/**
 * Tiré de «wp-admin/menu.php» Sert à vérifier si l'image est un URL ou une classe dashicon.
 * (?) Est très générale et est possiblement déjà incluse avec le thème d'où le function_exists (?)
 */
if ( ! function_exists( 'process_post_type_icon_param' ) ) {
  function process_post_type_icon_param( $post_type ) {

    $builtin_post_types = array( 'post', 'page' );
    $menu_icon          = $post_type->menu_icon;

    $icon_info = array(
      'class' => '',
      'src'   => '',
    );

    if ( empty( $menu_icon ) ) {
      $menu_icon = 'dashicons-admin-post';
    }

    if ( in_array( $post_type->name, $builtin_post_types, true ) ) {
      $icon_info['class'] = 'dashicons-admin-' . $post_type->name;

    } elseif ( is_string( $menu_icon ) ) {
      if ( 0 === strpos( $menu_icon, 'data:image/svg+xml;base64,' ) ) {
        $icon_info['src'] = $menu_icon;

      } elseif ( 0 === strpos( $menu_icon, 'dashicons-' ) ) {
        $icon_info['class'] = $menu_icon;

      } else {
        $icon_info['src'] = esc_url( $menu_icon );

      }
    }

    return $icon_info;
  }
}
