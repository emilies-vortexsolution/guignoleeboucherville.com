<?php

add_action(
  'custom_menu_order',
  function() {
    return true;
  }
);

function apply_admin_button_style( $slug, string $menu_color = 'white', string $type = 'menu-posts' ) {

  echo '<style>';

  // @codingStandardsIgnoreLine
  echo "
    .post-types-checkboxes .post-type-$slug {
      text-shadow: 0 0 1px {$menu_color}75;
    }
    .post-types-checkboxes .post-type-$slug .dashicons-before {
      color: $menu_color;
      text-shadow: none;
    }
  ";

  switch ( $type ) {
    case 'menu-posts':
      $slug = ( 'post' === $slug ) ? '' : "-$slug";

      // @codingStandardsIgnoreLine
      echo "
        #wp-admin-bar-new$slug:not(:hover) .ab-item,
        #menu-posts$slug:not(.wp-menu-open) .wp-menu-name { color: $menu_color !important; }

        #menu-posts$slug:hover .wp-menu-image:before,
        #menu-posts$slug:hover .wp-menu-name { color: white !important; }

        #menu-posts$slug > a {
          background-color: #111;
          box-shadow: inset 2px 0 0px 0px $menu_color;
        }

        #menu-posts$slug.wp-menu-open > a,
        #menu-posts$slug > a:hover { background-color: $menu_color !important; }
        
        #menu-posts$slug:hover .wp-menu-image img,
        #menu-posts$slug.current .wp-menu-image img,
        #menu-posts$slug.wp-menu-open .wp-menu-image img {
          opacity: 1;
          -webkit-filter: saturate(0%) brightness(500%) contrast(100%);
          filter: saturate(0%) brightness(500%) contrast(100%);
        }
      ";
          break;

    case 'toplevel':
      $slug_prefixed = "#toplevel_page_$slug";

      // @codingStandardsIgnoreLine
      echo "
        #wp-admin-bar-new-$slug_prefixed:not(:hover) .ab-item,
        $slug_prefixed:not(.wp-menu-open) .wp-menu-name {
          color: $menu_color !important;
          font-weight: 700;
        }
        
        $slug_prefixed:hover .wp-menu-image:before,
        $slug_prefixed.current a.menu-top .wp-menu-name,
        $slug_prefixed:hover .wp-menu-name {
          color: white !important;
        }
        
        $slug_prefixed > a {
          background-color: #111;
          box-shadow: inset 2px 0 0px 0px $menu_color;
        }
        
        $slug_prefixed.current a.menu-top,
        $slug_prefixed.wp-menu-open > a,
        $slug_prefixed > a:hover { 
          background-color: $menu_color !important;
        }
        
        $slug_prefixed:hover .wp-menu-image img,
        $slug_prefixed.current .wp-menu-image img,
        $slug_prefixed.wp-menu-open .wp-menu-image img {
          opacity: 1;
          -webkit-filter: saturate(0%) brightness(500%) contrast(100%);
          filter: saturate(0%) brightness(500%) contrast(100%);
        }
      ";
          break;
  }

  echo '</style>';

}

/**
 * Ajouter un bouton menu avant ou après un target
 *
 * @author MADL
 *
 * @param menu:array Le array officiel du menu WP (en théorie, la fonction peut fonctionner avec n'importe quel tableau non-associatif qui contient des strings comme valeurs)
 * @param item:string Le string à chercher
 * @param target:string Le target string à positionner l'item par rapport à sa position dans le array
 *     @default 'index.php' (le dashboard)
 * @param relativePos:int À ajouter au calcul de position. 1 apporte l'item juste en dessous et 0 juste en haut.
 *     @default 1 (en dessous)
 */
function change_menu_item_position( $menu, $item, $target = 'index.php', $relative_pos = 1 ) {

  $target_pos = -1;
  $item_pos   = -1;

  $precise_target_condition = ( 'edit.php' === $target );
  $precise_item_condition   = ( 'edit.php' === $item );

  for ( $a = count( $menu ) - 1; $a >= 0; $a-- ) {
    // Trouver le target
    if ( -1 === $target_pos ) {
      if ( $precise_target_condition ) {
        if ( $menu[ $a ] === $target ) {
          $target_pos = $a;
          if ( -1 !== $item_pos && -1 !== $target_pos ) {
            break;
          }
        }
      } elseif ( false !== strpos( $menu[ $a ], (string) $target ) ) {
        $target_pos = $a;
        if ( -1 !== $item_pos && -1 !== $target_pos ) {
          break;
        }
      }
    }

    // Trouver pos pour cet item
    if ( -1 === $item_pos ) {
      if ( $precise_item_condition ) {
        if ( $menu[ $a ] === $item ) {
          $item_pos = $a;
          if ( -1 !== $item_pos && -1 !== $target_pos ) {
            break;
          }
        }
      } elseif ( false !== strpos( $menu[ $a ], $item ) ) {
        $item_pos = $a;
        if ( -1 !== $item_pos && -1 !== $target_pos ) {
          break;
        }
      }
    }
  } // end FOR menu items

  // On effectue la transformation seulement si on a trouvé la cible et que l'item existe.
  if ( -1 !== $item_pos && -1 !== $target_pos ) {
    if ( $item_pos < $target_pos ) {
      $relative_pos--;
    }

    $item_content = $menu[ $item_pos ];
    unset( $menu[ $item_pos ] );
    array_splice( $menu, $target_pos + $relative_pos, 0, $item_content );
  }

  return $menu;

}
