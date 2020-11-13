<?php

// create field
new ACF_Post_Types_List_Revolution();

class ACF_Post_Types_List_Revolution extends acf_field {


  /*
  *  __construct
  *
  *  This function will setup the field type data
  *
  *  @type  function
  *  @date  5/03/2014
  *  @since 5.0.0
  *
  *  @param n/a
  *  @return  n/a
  */

  public function __construct() {

    $post_types = array(
      'attachment',
      'revision',
      'nav_menu_item',
      'custom_css',
      'customize_changeset',
      'oembed_cache',
      'user_request',
      'wp_block',
      'acf-field',
      'acf-field-group',
      'product_variation',
      'scheduled-action',
      'shop_order',
      'shop_order_refund',
      'shop_coupon',
      'af_form',
      'af_entry',
    );

    // vars
    $this->name     = 'post_types_list_field';
    $this->label    = _x( 'Post Types Select', 'Acf field label', 'acf' );
    $this->category = __( 'Relational', 'acf' ); // Basic, Content, Choice, etc
    $this->defaults = array(
      'allow_multiple'        => true,
      'disallowed_post_types' => $post_types,
    );

    // do not delete!
    parent::__construct();
  }


  /*
  *  render_field_settings()
  *
  *  Create extra settings for your field. These are visible when editing a field
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field (array) the $field being edited
  *  @return  n/a
  */

  public function render_field_settings( $field ) {

    acf_render_field_setting(
      $field,
      array(
        'label' => _x( 'Allow Multiple?', 'Acf field label', 'acf' ),
        'type'  => 'true_false',
        'name'  => 'allow_multiple',
        'ui'    => 1,
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label'   => _x( 'Disallowed post types', 'Acf field label', 'acf' ),
        'type'    => 'checkbox',
        'name'    => 'disallowed_post_types',
        'choices' => get_post_types(),
      )
    );

  }


  /*
  *  render_field()
  *
  *  Create the HTML interface for your field
  *
  *  @param $field (array) the $field being rendered
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field (array) the $field being edited
  *  @return  n/a
  */

  public function render_field( $field ) {

    $field = array_merge( $this->defaults, $field );

    $field_value = ( is_array( $field['value'] ) ) ? $field['value'] : array( $field['value'] );

    $post_types = get_post_types();

    if ( ! empty( $field['disallowed_post_types'] ) ) {
      foreach ( $field['disallowed_post_types'] as $disallowed_post_type ) {
        unset( $post_types[ $disallowed_post_type ] );
      }
    }

    if ( ! empty( $post_types ) ) {

      // Donner les beaux noms
      foreach ( $post_types as $key => $value ) {
        $post_types[ $value ] = get_post_type_object( $value );
      }
      usort( $post_types, 'compare_post_type_label' );
      ?>
      
      <div class='post-types-checkboxes'>
        <div class='post-types-checkboxes-inner edit_form_line'>
        
          <?php if ( $field['allow_multiple'] ) { ?>
          
            <input type='hidden' name='<?php echo esc_attr( $field['name'] ); ?>' value=''>
            <?php
            foreach ( $post_types as $post_type_object ) {

              $checked = ( in_array( $post_type_object->name, $field_value, true ) ) ? 'checked' : '';

              $id = "post-type-{$post_type_object->name}-" . get_and_increment_post_type_checkbox_total_count();
              ?>
              
              <label class='post-types-checkboxes__label post-type-<?php echo esc_attr( $post_type_object->name ); ?>' for='<?php echo esc_attr( $id ); ?>'>
                <input id='<?php echo esc_attr( $id ); ?>' name='<?php echo esc_attr( "{$field['name']}[{$post_type_object->name}]" ); ?>' type='checkbox' value="<?php echo esc_attr( $post_type_object->name ); ?>" <?php echo esc_attr( $checked ); ?>>
                <?php display_post_type_icon( $post_type_object ); ?>
                <?php echo wp_kses_post( $post_type_object->labels->singular_name ); ?>
              </label>
              
            <?php } ?>
            
          <?php } else { ?>

            <?php
            foreach ( $post_types as $post_type_object ) {

              $checked = ( in_array( $post_type_object->name, $field_value, true ) ) ? 'checked' : '';

              $id = "post-type-{$post_type_object->name}-" . get_and_increment_post_type_checkbox_total_count();
              ?>
              
              <label class='post-types-checkboxes__label post-type-<?php echo esc_attr( $post_type_object->name ); ?>' for='<?php echo esc_attr( $id ); ?>'>
                <input id='<?php echo esc_attr( $id ); ?>' name='<?php echo esc_attr( $field['name'] ); ?>' type='radio' value="<?php echo esc_attr( $post_type_object->name ); ?>" <?php echo esc_attr( $checked ); ?>>
                <?php display_post_type_icon( $post_type_object ); ?>
                <?php echo wp_kses_post( $post_type_object->labels->singular_name ); ?>
              </label>
              
            <?php } ?>
            
          <?php } ?>
          
        </div>
      </div>
      
      <?php
    } // end IF $post_types not empty
  }

  /*
  *  format_value()
  *
  *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
  *
  *  @type  filter
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $value (mixed) the value which was loaded from the database
  *  @param $post_id (mixed) the $post_id from which the value was loaded
  *  @param $field (array) the field array holding all the field options
  *
  *  @return    $value (mixed) the modified value
  */
  // @codingStandardsIgnoreLine
  public function format_value( $value, $post_id, $field ) {

    // bail early if no value
    if ( is_array( $value ) ) {

      return $value;

    }

    return array( $value => $value );

  }


}
