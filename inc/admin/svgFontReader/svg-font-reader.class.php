<?php
/**
 * Original class as been edited to add more information to the returned Array
 * and make compatible with the theme.
 *
 * @link https://github.com/blacksunshineCoding/svgFontReader
 *
 * @todo Test on beta due to file_get_contents
 */
class SVG_Font_Reader {

  public function list_glyphs( $svg_file ) {
    $all_glyphs = $this->get_glyphs( $svg_file );
    return implode( ',', $all_glyphs );
  }

  public function get_glyphs( $svg_file ) {
    $svg_content = get_file_contents_by_url( $svg_file );

    if ( empty( $svg_content ) ) {
      return '';
    }

    $xml_init  = simplexml_load_string( $svg_content );
    $svg_json  = wp_json_encode( $xml_init );
    $svg_array = json_decode( $svg_json, true );

    $svg_glyphs       = array();
    $svg_glyphs_clear = array();

    if ( is_array( $svg_array ) ) {
      $svg_glyphs = $svg_array['defs']['font']['glyph'];

      if ( count( $svg_glyphs ) > 0 ) {

        foreach ( $svg_glyphs as $glyph_id => $glyph ) {
          if ( empty( $glyph['@attributes']['glyph-name'] ) ) {
            continue;
          }

          if ( isset( $glyph['@attributes']['glyph-name'] ) ) {
            $glyph_id = $glyph['@attributes']['glyph-name'];
          }

          if ( isset( $glyph['@attributes']['unicode'] ) ) {
            $svg_glyphs_clear[ $glyph_id ] = $glyph['@attributes']['unicode'];
          }
        }
      }
    }

    return $svg_glyphs_clear;
  }
}
