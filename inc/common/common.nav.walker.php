<?php
/**
 * @todo S'assurer qu'un "heading" soit accessible.
 * @todo Permettre l'ajout d'une image ou bloc HTML dans un menu.
 * @todo Permettre à "has_toggle_dropdown" d'accpter un array pour dire à quels niveaux on veut des dropdows. Même chose pour "make_child_less_last" et tout autre nouveau setting.
 * @todo Corriger bug quand on enlève le libellé pour mettre juste une icône. Menu disparait.
 */


/**
 * Cleaner walker for wp_nav_menu()
 *
 * Custom arguments for Roots_Nav_Walker when using wp_nav_menu() {
 *     @type boolean        has_toggle_dropdown  Whether to use toggleable dropdown or simple list. Default: FALSE.
 *     @type boolean|array  make_childless_last  Take all childless links of second level and put them at the end of their container.
 *                                               When using an array, use only one of it's parameter at a time. It may look like this:
 *                                               [
 *                                                 'only_level' => [0, 3], // Will only includes these levels...
 *                                                 'not_level'  => [1, 2], // ... Or every other level thant theses.
 *                                               ]
 * }
 *
 * Lexicon:
 *   * nav_structure: An array containing usefull information on each already processed item or the actual one.
 *                    Reaching for an item using `$this->nav_structure['1-2-0']` would mean:
 *                    the item that is the first child to the third child of the second first level item.
 *   * get_actual_{something}: A set of getter function aiming to get information about the actual item of the nav_structure.
 *                             $nav_structure_actual_position contain the ID of that item in the form of a string like `1-2-0`.
 */
class Roots_Nav_Walker extends Walker_Nav_Menu {

  // Parameters
  private $has_toggle_dropdown;
  private $make_childless_last;

  // Automatics
  public static $base_toggle_labels;
  private $count_of_nav_first_level_items_not_part_of_extra_col;
  private $extra_html_col_by_depth;
  private $nav_id;
  private $nav_structure;
  private $nav_structure_actual_position;
  private $nav_structure_actual_2d_snapshot;
  private $nav_structure_last_depth;
  private $subnav_id_prefix;


  public function __construct() {
    self::$base_toggle_labels = array(
      /* translators: %s: Page title */
      'open'  => esc_html_x( 'Open %s sub menu.', 'SR-Only sub menu label', 'vtx' ),
      /* translators: %s: Page title */
      'close' => esc_html_x( 'Close %s sub menu.', 'SR-Only sub menu label', 'vtx' ),
    );
  }

  private function init_nav( $args ) {
    $this->has_toggle_dropdown                                  = $args->has_toggle_dropdown;
    $this->make_childless_last                                  = $args->make_childless_last;
    $this->count_of_nav_first_level_items_not_part_of_extra_col = 0;
    $this->extra_html_col_by_depth                              = array();

    // @codingStandardsIgnoreStart
    $this->nav_id                           = "menu-{$args->menu->slug}";
    $this->nav_structure                    = array(); // [ depth => [ 'depth-x' => x ] ]
    $this->nav_structure_actual_position    = ''; // 'actual structure id. Ex.: 3-0-2'
    $this->nav_structure_actual_2d_snapshot = array(); // 'depth-x'
    $this->nav_structure_last_depth         = -1; // 'depth-x'
    $this->subnav_id_prefix                 = "{$this->nav_id}__subnav__";
    // @codingStandardsIgnoreEnd
  }

  private function get_actual_item_structure() {
    if ( ! isset( $this->nav_structure[ $this->nav_structure_actual_position ] ) ) {
      return array();
    }

    return $this->nav_structure[ $this->nav_structure_actual_position ];
  }

  private function get_actual_subnav_id() {
    return "{$this->subnav_id_prefix}{$this->nav_structure_actual_position}";
  }

  private function get_actual_structure_id_by_depth( $depth = 0 ) {
    if ( 0 > $depth ) {
      return 'none';
    }

    $id = '';
    for ( $i = 0; $i <= $depth; $i++ ) {
      $id .= "{$this->nav_structure_actual_2d_snapshot[ $i ]}-";
    }
    $id = rtrim( $id, '-' );

    return $id;
  }

  private function get_actual_parent_subnav_id() {
    $parent_structure = $this->get_actual_parent_structure();
    if ( empty( $parent_structure ) ) {
      return '';
    }

    return $parent_structure['subnav_id'];
  }

  private function get_actual_parent_structure() {
    $structure        = $this->get_actual_item_structure();
    $parent_structure = ( isset( $this->nav_structure[ $structure['parent_structure_id'] ] ) ) ? $this->nav_structure[ $structure['parent_structure_id'] ] : null;
    if ( empty( $parent_structure ) ) {
      return array();
    }

    return $parent_structure;
  }

  private function is_actual_item_childless() {
    return $this->nav_structure[ $this->nav_structure_actual_position ]['is_part_of_extra_col'];
  }

  private function count_children_not_part_of_extra_col_in_structure( $nav_structure ) {
    if ( empty( $nav_structure ) ) {
      return 0;
    }

    $count = 0;
    foreach ( $nav_structure['children'] as $id => $info ) {
      if ( ! $this->nav_structure[ $id ]['is_part_of_extra_col'] ) {
        $count++;
      }
    }

    return $count;
  }

  private function add_item_to_nav_structure( $item, $depth ) {

    if ( $depth > $this->nav_structure_last_depth || ! isset( $this->nav_structure_actual_2d_snapshot[ $depth ] ) ) {
      // If the structure actual position is in a lower depth, reset the X position. Also, do the same for non-existant structure.
      $this->nav_structure_actual_2d_snapshot[ $depth ] = 0;

    } else {
      // Here means that we are at the same or higher depth. It increment the X position
      $this->nav_structure_actual_2d_snapshot[ $depth ]++;
    }

    // Save in structure.
    $structure_id         = $this->get_actual_structure_id_by_depth( $depth );
    $parent_id            = $this->get_actual_structure_id_by_depth( $depth - 1 );
    $top_parent_id        = $this->get_actual_structure_id_by_depth( 0 );
    $is_part_of_extra_col = ! $item->is_dropdown && $this->is_item_childless_and_last_at_this_depth( $item, $depth );

    $this->nav_structure_actual_position  = $structure_id;
    $this->nav_structure[ $structure_id ] = array(
      'structure_id'         => $structure_id,
      'index'                => $this->nav_structure_actual_2d_snapshot[ $depth ],
      'depth'                => $depth,
      'parent_structure_id'  => $parent_id,
      'subnav_id'            => $this->get_actual_subnav_id(), // Only usefull if this item has children.
      'name'                 => $item->title,
      'is_part_of_extra_col' => $is_part_of_extra_col,
      'children'             => array(),
    );

    // Add to parent children count.
    if ( 'none' !== $parent_id ) {
      $this->nav_structure[ $parent_id ]['children'][ $structure_id ] = array(
        'id' => $structure_id,
      );
    } elseif ( ! $is_part_of_extra_col ) {
      $this->count_of_nav_first_level_items_not_part_of_extra_col++;
    }

    // Set var next pass
    $this->nav_structure_last_depth = $depth;
  }

  private function add_item_html_to_last_col_by_depth( $item_html, $depth ) {
    if ( ! isset( $this->extra_html_col_by_depth[ $depth ] ) ) {
      $this->extra_html_col_by_depth[ $depth ] = '';
    }

    $this->extra_html_col_by_depth[ $depth ] .= $item_html;
  }

  // @codingStandardsIgnoreLine
  private function is_item_childless_and_last_at_this_depth( $item, $depth ) {

    if ( $this->make_childless_last ) {

      if ( is_array( $this->make_childless_last ) ) {

        if ( ! empty( $this->make_childless_last['only_level'] ) ) {
          return in_array( $depth, $this->make_childless_last['only_level'], true );
        } elseif ( ! empty( $this->make_childless_last['not_level'] ) ) {
          return ! in_array( $depth, $this->make_childless_last['not_level'], true );
        }
      }

      return true;
    }

    return false;
  }

  private function add_extra_content_to_subnav_in_html( $content_to_insert, $html ) {
    $content_to_insert = array_merge(
      array(
        'ul_extra_classes' => '',
        'first_li'         => '',
      ),
      $content_to_insert
    );
    $subnav_id         = $this->get_actual_parent_subnav_id();

    $html = preg_replace(
      "/<ul id=\"{$subnav_id}\"([^>]*)class=\"([^\"]*)\"([^>]*)>/",
      "<ul id=\"{$subnav_id}\"\$1class=\"\$2 {$content_to_insert['ul_extra_classes']}\"\$3>{$content_to_insert['first_li']}",
      $html
    );

    return $html;
  }

  private function switch_actual_position_with_parent() {
    $this->nav_structure_actual_position = $this->get_actual_item_structure()['parent_structure_id'];
  }


  public function check_current( $classes ) {
    return preg_match( '/(current[-_])|active|dropdown/', $classes );
  }

  // @codingStandardsIgnoreLine
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $subnav_id = $this->get_actual_subnav_id();

    $attr_id = ( '' !== $subnav_id ) ? "id=\"$subnav_id\"" : '';

    $output .= "\n<div $attr_id class=\"dropdown__subnav-wrapper subnav-wrapper-depth-$depth\">\n<ul class=\"dropdown__subnav subnav-depth-$depth\" data-depth=\"$depth\">\n";
  }

  // @codingStandardsIgnoreLine
  function end_lvl( &$output, $depth = 0, $args = array() ) {
    $subnav_depth      = $depth + 1;
    $parent_structure  = $this->get_actual_parent_structure();
    $cols_count        = 0;
    $content_to_insert = array(
      'ul_extra_classes' => '',
      'first_li'         => '',
    );

    // End extra col if this level has one.
    if ( ! empty( $this->extra_html_col_by_depth[ $subnav_depth ] ) ) {
      $cols_count                           += $this->count_children_not_part_of_extra_col_in_structure( $parent_structure );
      $content_to_insert['ul_extra_classes'] = "prev-to-extra-col has-{$cols_count}-cols";

      // First, close normal nav...
      $output .= '</ul>';

      // ... then open extra col nav.
      $output .= "\n<ul class=\"dropdown__subnav extra-col\">";
      $output .= $this->extra_html_col_by_depth[ $subnav_depth ];

      // Reset this.
      unset( $this->extra_html_col_by_depth[ $subnav_depth ] );
    }

    $output .= "\n</ul>\n</div>\n";

    $output = $this->add_extra_content_to_subnav_in_html( $content_to_insert, $output );
  }

  // @codingStandardsIgnoreLine
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $item_html = '';
    parent::start_el( $item_html, $item, $depth, $args );

    $item_html = str_replace( '<a', "<div class=\"nav-item__link-wrapper\">\n<a class=\"nav-item__link\" ", $item_html );

    // Add toggle button
    if ( $this->has_toggle_dropdown && $item->is_dropdown ) {
      $subnav_id     = $this->get_actual_subnav_id();
      $toggle_labels = self::get_toggle_labels_for_nav_item( $item );
      $item_html     = str_replace(
        '</a>',
        "</a>\n<button type=\"button\" class=\"dropdown__toggle\" aria-label=\"{$toggle_labels[ 'open' ]}\" aria-expanded=\"false\" aria-controls=\"$subnav_id\" data-label-open=\"{$toggle_labels[ 'open' ]}\" data-label-close=\"{$toggle_labels[ 'close' ]}\"></button>\n",
        $item_html
      );
    }

    // Add icon
    if ( ! empty( $item->icon ) ) {
      $icon      = "<span class='icon icon-{$item->icon}' aria-hidden='true'></span>";
      $item_html = preg_replace( '/(<a[^>]*>)(.*)<\/a>/iU', '$1' . $icon . '<span class="next-to-icon">$2</span></a>', $item_html );
    }

    // End link wrapper
    if ( $this->has_toggle_dropdown && $item->is_dropdown ) {
      $item_html = str_replace( '</button>', "</button>\n</div>\n", $item_html );
    } else {
      $item_html = str_replace( '</a>', "</a>\n</div>\n", $item_html );
    }

    // Transform into a heading without link.
    if ( ! empty( $item->is_heading ) ) {
      $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<span class="nav-item__link">$1</span>', $item_html );
    }

    $item_html = apply_filters( 'roots/wp_nav_menu_item', $item_html );

    if ( $this->is_actual_item_childless() ) {
      // Get all this new HTML into an extra col...
      $this->add_item_html_to_last_col_by_depth( $item_html, $depth );

    } else {
      // ... otherwise proceed normally.
      $output .= $item_html;
    }
  }

  public function end_el( &$output, $item, $depth = 0, $args = null ) {
    $item_html = '';
    parent::end_el( $item_html, $item, $depth, $args );

    // If ending this el made the depth go higher (2 -> 1 is higher), change the actual position in structure.
    if ( $depth < $this->nav_structure_last_depth ) {
      $this->switch_actual_position_with_parent();
    }

    if ( $this->is_actual_item_childless() ) {
      // Get all this new HTML into an extra col...
      $this->add_item_html_to_last_col_by_depth( $item_html, $depth );

    } else {
      // ... otherwise proceed normally.
      $output .= $item_html;
    }
  }

  public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
    $element->is_dropdown = ( ( ! empty( $children_elements[ $element->ID ] ) && ( ( $depth + 1 ) < $max_depth || ( 0 === $max_depth ) ) ) );
    $element->is_heading  = ! ! get_field( 'nav_item_is_heading', $element->ID );
    $element->icon        = get_field( 'nav_item_icon', $element->ID );

    $this->add_item_to_nav_structure( $element, $depth );

    $element->subnav_id = $this->get_actual_subnav_id();

    parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
  }

  public function walk( $elements, $max_depth, ...$args ) {
    $this->init_nav( $args[0] );
    $output = parent::walk( $elements, $max_depth, ...$args );

    // If there is an extra col, add extra arguments to the parent walk and an extra UL
    if ( ! empty( $this->extra_html_col_by_depth[0] ) ) {
      $args[0]->menu_class .= ' prev-to-extra-col';
      $args[0]->menu_class .= " has-{$this->count_of_nav_first_level_items_not_part_of_extra_col}-cols";

      // First, close normal nav...
      $output .= '</ul>';
      // ... then open extra col nav.
      $output .= "\n<ul class=\"extra-col\">";
      $output .= $this->extra_html_col_by_depth[0];

      unset( $this->extra_html_col_by_depth[0] );
    }

    return $output;
  }

  public function get_toggle_labels_for_nav_item( $menu_item ) {

    return array(
      'open'  => esc_html(
        sprintf(
          self::$base_toggle_labels['open'],
          $menu_item->title
        )
      ),
      'close' => esc_html(
        sprintf(
          self::$base_toggle_labels['close'],
          $menu_item->title
        )
      ),
    );
  }
}

add_filter( 'nav_menu_item_id', '__return_null' );

/**
 * Remove the id="" on nav menu items
 * Return 'menu-slug' for nav menu classes
 */
add_filter( 'nav_menu_css_class', 'roots_nav_menu_css_class', 10, 2 );
function roots_nav_menu_css_class( $classes, $item ) {
  $classes = preg_replace( '/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes );
  $classes = preg_replace( '/^((menu|page)[-_\w+]+)+/', '', $classes );

  $classes[] = 'nav-item';
  $classes[] = $item->subnav_id;

  if ( ! empty( $item->is_dropdown ) ) {
    $classes[] = 'dropdown';
  }

  if ( ! empty( $item->is_heading ) ) {
    $classes[] = 'nav-item--heading';
  }

  if ( ! empty( $item->icon ) ) {
    $classes[] = 'nav-item--has-icon';
  }

  $classes = array_unique( $classes );

  return array_filter( $classes, 'is_element_empty' );
}

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Roots_Nav_Walker() by default
 */
add_filter( 'wp_nav_menu_args', 'roots_nav_menu_args' );
function roots_nav_menu_args( $args = '' ) {
  $roots_nav_menu_args['container'] = false;

  if ( ! $args['items_wrap'] ) {
    $roots_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
  }

  if ( ! $args['depth'] ) {
    $roots_nav_menu_args['depth'] = 3;
  }

  if ( ! $args['walker'] ) {
    $roots_nav_menu_args['walker'] = new Roots_Nav_Walker();
  }

  if ( empty( $args['has_toggle_dropdown'] ) ) {
    $roots_nav_menu_args['has_toggle_dropdown'] = false;
  }

  if ( empty( $args['make_childless_last'] ) ) {
    $roots_nav_menu_args['make_childless_last'] = false;
  }

  return array_merge( $args, $roots_nav_menu_args );
}
