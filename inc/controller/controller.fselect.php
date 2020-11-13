<?php
use Roots\Sage\Assets;

add_filter( 'get_partial_args/templates/form/fselect', 'control_template_data_fselect' );
function control_template_data_fselect( $template_data ) {
  $template_data = array_merge(
    array(
      'data'            => array(),
      'select_multiple' => true,
      'get_property'    => 'slug',
      'class'           => '',
      'input_name'      => add_string_unique_suffix( 'vtx_fselect_value' ),
      'placeholder'     => _x( 'Any category', 'Default choice', 'vtx' ),
      'reselect'        => true,
      'selected_values' => array(),
      'data_type'       => '',
      'prop_id'         => '',
      'prop_slug'       => 'slug',
      'prop_title'      => 'title',
    ),
    $template_data
  );

  if ( ! empty( $template_data['data'] ) && is_array( $template_data['data'] ) ) {
    $template_data['prop_id']    = '';
    $template_data['prop_slug']  = 'slug';
    $template_data['prop_title'] = 'title';

    if ( isset( $template_data['data'][0]->post_title ) ) {
      // Is WP_Post
      $template_data['data_type']  = 'post';
      $template_data['prop_id']    = 'ID';
      $template_data['prop_slug']  = 'post_name';
      $template_data['prop_title'] = 'post_title';

    } elseif ( isset( $template_data['data'][0]->term_id ) ) {
      // Is WP_Term
      $template_data['data_type']  = 'term';
      $template_data['prop_id']    = 'term_id';
      $template_data['prop_slug']  = 'slug';
      $template_data['prop_title'] = 'name';

    } elseif ( is_object( $template_data['data'][0] ) && isset( $template_data['data'][0]->title ) && isset( $template_data['data'][0]->slug ) ) {
      // Is something special
      $template_data['data_type'] = 'special';

    } elseif ( is_array( $template_data['data'][0] ) && isset( $template_data['data'][0]['title'] ) && isset( $template_data['data'][0]['slug'] ) ) {
      // Is something special, in Array form.
      $template_data['data_type'] = 'special';
    }
  }

  if ( $template_data['data_type'] ) {
    wp_enqueue_script( 'vtx-fselect-js', Assets\asset_path( 'scripts/vtx-fselect.js' ), array( 'jquery' ), '1.0.0', true );

    // get_property could be "id", "slug" or "title" wich are not always real WPObject properties.
    // Here, they are transformed into the real property depending on the data type.
    switch ( $template_data['get_property'] ) {
      case 'id':
            $template_data['get_property'] = $template_data['prop_id'];
            break;
      case 'slug':
            $template_data['get_property'] = $template_data['prop_slug'];
            break;
      case 'title':
            $template_data['get_property'] = $template_data['prop_title'];
            break;
    }

    // @codingStandardsIgnoreLine
    if ( $template_data['reselect'] && ! empty( $_REQUEST[ $template_data['input_name'] ] ) ) {
      // @codingStandardsIgnoreLine
      $template_data['selected_values'] = esc_sql( $_REQUEST[ $template_data['input_name'] ] );
      $template_data['selected_values'] = explode( ',', $selected_values );

      if ( ! $template_data['select_multiple'] ) {
        $template_data['selected_values'] = array( current( $template_data['selected_values'] ) );
      }

      $template_data['selected_values'] = array_flip( $template_data['selected_values'] );
    }
  }

  return $template_data;
}
