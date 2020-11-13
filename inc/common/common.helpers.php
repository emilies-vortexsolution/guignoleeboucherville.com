<?php
use Roots\Sage\Assets;

/**
 * Like get_template_part() put lets you pass args to the template file
 * Args are available in the template as $template_data array OR the keys as variables name's.
 * Template data can be filtered:
 *   - To filter all partials `add_filter( 'get_partial_args'. function () {...} );` can be used.
 *   - To target a specific partial, use the dynamic filter. Example for $file with "/foo/bar" as value:
 *     `add_filter( 'get_partial_args/foo/bar'. function () {...} );`
 *
 * @author Sariha Chabert <sariha.c@vortexsolution.com>
 *
 * @param $file
 * @param array $template_data
 * @param bool $output
 *
 * @return bool|false|string
 */
function get_partial( $file, $template_data = array(), $output = true ) {

  //add custom filter
  $template_data = apply_filters( 'get_partial_args', $template_data, $file );
  $template_data = apply_filters( "get_partial_args/$file", $template_data, $file );

  $template_data = wp_parse_args( $template_data );

  //custom hook.
  do_action( 'before_get_partial', $file, $template_data, $output );

  $file = get_partial_file_path( $file );
  if ( ! $file ) {
    return false;
  }

  //extrac variables so we can use them in template.
  foreach ( $template_data as $key => $value ) {
    $$key = $value;
  }

  if ( true === $output ) {
    require $file;
    $data = true;
  } else {
    ob_start();
    require $file;
    $data = ob_get_clean();
  }

  do_action( 'after_get_partial', $file, $template_data, $output );
  return $data;
}

/**
 * Advanced get_template_part().
 * Has the first two parameter of get_template_part() but uses the template data of get_partial()
 * As in get_partial(), there is way to filter template data :
 *   - `add_filter( 'get_partial_args', function () {...} );` (from get_partial())
 *   - `add_filter( "get_partial_args/{$file}", function () {...} );` (added here)
 *   - `add_filter( "get_partial_args/{$file}-{$name}", function () {...} );` (from get_partial())
 */
function get_template_part_with_data( $file, $name, $template_data = array(), $output = true ) {
  if ( get_partial_file_path( "$file-$name" ) ) {
    $template_data = apply_filters( "get_partial_args/$file", $template_data, $file );
    $partial       = get_partial( "$file-$name", $template_data, $output );

    if ( $partial ) {
      return $partial;
    }
  }

  return get_partial( $file, $template_data, $output );
}

function get_partial_file_path( $file ) {
  if ( file_exists( get_stylesheet_directory() . '/' . $file . '.php' ) ) {
    return get_stylesheet_directory() . '/' . $file . '.php';
  } elseif ( file_exists( get_template_directory() . '/' . $file . '.php' ) ) {
    return get_template_directory() . '/' . $file . '.php';
  }

  return false;
}

function get_excerpt_from_text( $text, $excerpt_word_length = 20, $excerpt_more = ' [...]' ) {
  if ( '' !== $text ) {

      $text = strip_shortcodes( $text );
      $text = apply_filters( 'the_content', $text );
      $text = str_replace( ']]&gt;', ']]&gt;', $text );

      $text = wp_trim_words( $text, $excerpt_word_length, $excerpt_more );
  }
    return apply_filters( 'wp_trim_excerpt', $text );
}

add_action( 'acf/render_field_settings', 'admin_only_field_settings' );

function admin_only_field_settings( $field ) {

    acf_render_field_setting(
      $field,
      array(
        'label'        => __( 'Admin Only ?', 'opq' ),
        'instructions' => __( 'Show this field in admin only. This field will be hidden in front-end forms.', 'opq' ),
        'name'         => 'admin_only',
        'type'         => 'true_false',
        'ui'           => 1,
      ),
      true
    );

}

add_filter( 'acf/prepare_field', 'admin_only_prepare_field' );

function admin_only_prepare_field( $field ) {

  if ( empty( $field['admin_only'] ) ) {
      return $field;
  }

  if ( ! is_admin() || wp_doing_ajax() ) {
      return false;
  }

    // return
    return $field;
}

function get_post_metas( $post_id ) {

    global $wpdb;
    $results = $wpdb->get_results(
      $wpdb->prepare( 'SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key NOT LIKE %s', $post_id, '\_%' )
    );

  if ( empty( $results ) ) {
      return array();
  }

    $metas = array();
  foreach ( $results as $result ) {
      $metas[ $result->meta_key ] = $result->meta_value;
  }

    return $metas;
}

/**
 * Merge wp_query args :)
 *
 * @author Sariha Chabert <sariha.c@vortexsolution.com>
 * @date 10/07/2018
 *
 * @param array $args
 * @param array $default
 *
 * @return array
 */
function vtx_merge_args( array &$args, array &$default ) {

    $merged = $default;
  foreach ( $args as $k => &$v ) {

    if ( is_array( $v ) && ! empty( $merged[ $k ] ) && is_array( $merged[ $k ] ) ) {

        $merged[ $k ] = vtx_merge_args( $merged[ $k ], $v );

    } elseif ( is_numeric( $k ) ) {

      if ( ! in_array( $v, $merged, true ) ) {
          $merged[] = $v;
      }
    } else {
        $merged[ $k ] = $v;
    }

      //if merged value is a sequential array we need to remove duplicates.
    if ( is_array( $merged[ $k ] ) && ! is_assoc_array( $merged[ $k ] ) ) {
        $merged[ $k ] = array_map( 'unserialize', array_unique( array_map( 'serialize', $merged[ $k ] ) ) );
    }
  }

    return $merged;
}

/**
 * Check if array is associative or sequential
 * https://stackoverflow.com/a/173479
 *
 * @param array $arr
 *
 * @return bool
 */
function is_assoc_array( array $arr ) {
  if ( array() === $arr ) {
      return false;
  }
    return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

function is_element_empty( $element ) {
  $element = trim( $element );
  return ! empty( $element );
}

/**
 * Remove the language code from a given URL.
 *
 * @param string $url
 * @param string $lang Language code (en|fr)
 *
 * @return string If WPML is inactive, the full URL will be returned.
 */
function remove_language_from_url( $url, $lang = '' ) {
  if ( ! $lang ) {
    $lang = apply_filters( 'wpml_current_language', null );
  }
  return str_replace( "/$lang/", '/', $url );
}

function page_is_default_lang() {
  return apply_filters( 'wpml_default_language', null ) === apply_filters( 'wpml_current_language', null );
}

function get_logo_url() {
  return Assets\asset_path( 'images/logo.png' );
}

/**
 * Get lastest standard (2018) of HTML surrounding link with phone number.
 *
 * @param string|int $phone_number
 * @param array      $extra_attr
 * @param string     $phone_type   Was tel or fax. Now, both do the same. Usefull only for legacy or maybe the future.
 *
 * @return string HTML
 */
function get_html_format_phone_link( $phone_number, $extra_attr = array(), $phone_type = 'tel' ) {

  // RETURN si vide
  if ( empty( $phone_number ) ) {
    return '';
  }

  unset( $extra_attr['href'] );

  $formated_number = format_phone_link( $phone_number );

  $html_attr = convert_array_to_html_attr( $extra_attr );

  return "<a href=\"$phone_type:$formated_number\" $html_attr>$phone_number</a>";
}

  /**
   * Standarise un numéro de téléphone pour qu'il soit accepter dans les attributs href.
   * (?)
   * Les standards n'était pas clair à cette époque-ci. Revérifier dès que possible.
   * Dernière modification : 2018-11-20
   * (?)
   *
   * @param string|int $phone_number
   *
   * @return string|string[]
   */
function format_phone_link( $phone_number ) {

  // Formater le code pour le pays
  $country_code     = preg_replace( '/^\+?(\d)?(\d)[- ].+$/', '$1$2', $phone_number );
  $len_country_code = strlen( $country_code );

  $ext     = preg_replace( '/^.*#(\d+)$/', '$1', $phone_number );
  $len_ext = strlen( $ext );

  // Nettoyer le numéro
  $formated_number = str_replace( array( '-', ' ', '(', ')', '.' ), '', $phone_number );

  // Si on a trouver le code du pays (donc soit, 1 ou 01 si en Amérique du nord)...
  // (?) On ne prend pas en compte la possibilité d'un pays à 3 chiffres pour son code de pays. (?)
  if ( 2 >= $len_country_code ) {
    $country_code = "+$country_code";

    // Remettre le code de pays
    $formated_number = substr_replace( $formated_number, $country_code, 0, $len_country_code );
  } else {
    // .. Autrement il n'y avait pas de code de pays définit et on doit placer celui par défaut.
    $country_code = '+1';

    // Ajouter le countryCode
    $formated_number = $country_code . $formated_number;
  }

  // Vérifier s'il y a une extension. Remplacer le # par une virgule
  if ( strlen( $phone_number ) !== $len_ext ) {

    // Remplacer le # de l'extension actuelle.
    $formated_number = str_replace( '#', ',', $formated_number );

    // Ajouter le ISUB pour plus de compatibilité
    $formated_number .= ";isub=$ext";
  }

  return $formated_number;
}

/**
 * Converts an array into a string of HTML attributes.
 *
 * @param array $array_of_attrs [ attrName => attrValue ]
 *
 * @return string
 */
function convert_array_to_html_attr( $array_of_attrs ) {
  //  RETURN si tableau vide
  if ( empty( $array_of_attrs ) ) {
    return '';
  }

  $attrs = '';
  foreach ( $array_of_attrs as $attr_name => $attr_value ) {
    // CONTINUE si le $attr_name est vide
    if ( empty( $attr_name ) ) {
      continue;
    }

    $attr_name = esc_attr( $attr_name );

    // Si la valeur de l'attribut est vide...
    if ( empty( $attr_value ) ) {
      //... Il y a plusieurs possibilité à considérer avant de savoir quoi afficher.
      switch ( $attr_name ) {

        // these attributes do not need a value to be displayed.
        case 'selected':
        case 'disabled':
        case 'required':
        case 'pubdate':
          $attrs .= "$attr_name ";
              break;

        // This attribute may have an empty attribute.
        case 'alt':
          $attrs .= "$attr_name=\"\" ";
              break;
      }
    } else {
      //... Sinon, on affiche tout bonnement l'attribut et sa valeur
      $attrs .= "$attr_name=\"" . esc_attr( $attr_value ) . '" ';
    }
  }

  return $attrs;
}

/**
 * S'assure de redonner un Int utilisable à l'intérieur des fonctions comme get_the_title(), get_field(), etc.
 * Où on s'attend des fois à recevoir Null, mais à quand même pouvoir procéder à trouver le bon titre ou la bonne meta
 * par rapport au the_post actif.
 *
 * @author Marc-André De Launière
 *
 * @param int|WP_Post|null $maybe_fake_post_id Might be all sort of stuff that can be changed into a valid ID.
 *
 * @return int
 */
function sanitize_param_post_id( $maybe_fake_post_id ) {

  if ( null === $maybe_fake_post_id || false === $maybe_fake_post_id || '' === $maybe_fake_post_id ) {
    global $post;

    if ( isset( $post ) ) {
      return $post->ID;
    }
  } elseif ( is_int( $maybe_fake_post_id ) ) {
    return $maybe_fake_post_id;

  } elseif ( is_object( $maybe_fake_post_id ) && isset( $maybe_fake_post_id->ID ) ) {
    return $maybe_fake_post_id->ID;

  } elseif ( is_string( $maybe_fake_post_id ) ) {
    return (int) $maybe_fake_post_id;

  }

  return 0;
}

function get_the_relative_uri( $post_id = null ) {
  return rtrim( wp_parse_url( get_the_permalink( sanitize_param_post_id( $post_id ) ), PHP_URL_PATH ), '/' );
}

/**
 * Simple way to make a unique an ID
 *
 * Example:
 * <code>
 * <?php
 * // Since it would be the first time it's used,
 * // $id would be equal to `input_1`.
 * // The next usage result will change to `input_2`, then `input_3`, etc.
 * $id = add_string_unique_suffix( 'input' );
 * ?>
 * <label for="<?php echo $id; ?>">For that unique input.</label>
 * <input id="<?php echo $id; ?>">
 * </code>
 *
 * @param $maybe_non_unique_id
 */
function add_string_unique_suffix( string $non_unique_string = '' ) {
  global $vtx_incrementing_id;

  if ( ! isset( $vtx_incrementing_id ) ) {
    $vtx_incrementing_id = 0;
  }

  $vtx_incrementing_id++;

  return "{$non_unique_string}_{$vtx_incrementing_id}";
}


function sanitize_searchable( $string ) {
  $char_to_remove = array( '&', '<', '>', '"', '\'' );
  $string         = str_replace( $char_to_remove, '', $string );
  return mb_strtolower( $string );
}

/**
 * As simple as `file_get_contents` without security issues.
 * Also simpler than using `wp_remote_get` if you only need the content.
 *
 * @param string $url
 *
 * @return mixed
 */
function get_file_contents_by_url( $url ) {
  return wp_remote_retrieve_body( wp_remote_get( $url ) );
}
