<?php

// create field
new ACF_Field_Image_Select();

class ACF_Field_Image_Select extends acf_field {

  /**
   * Private vars
   */
  private $field = null;

  /**
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
    // vars
    $this->name     = 'image_select';
    $this->label    = _x( 'Image select', 'Acf field label', 'acf' );
    $this->category = __( 'Content', 'acf' ); // Basic, Content, Choice, etc
    // $this->choice_ =
    $this->defaults = array(
      'get_image_method' => 'images_folder_path',
      'return_format'    => 'image_url',
      'allow_none'       => 1,
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

    /*
    *  acf_render_field_setting
    *
    *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
    *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
    *
    *  More than one setting can be added by copy/paste the above code.
    *  Please note that you must also have a matching $defaults value for the field name (font_size)
    */

    acf_render_field_setting(
      $field,
      array(
        'label'        => _x( 'Get image method', 'ACF Admin field creation label', 'acf' ),
        'instructions' => _x( 'An array of selected format will be returned if multiple is allowed.<br>All path are relative to the active theme folder.', 'Admin field creation instructions', 'acf' ),
        'type'         => 'radio',
        'name'         => 'get_image_method',
        'choices'      => array(
          'images_folder_path' => _x( 'Directory path', 'Radio button label', 'acf' ),
          'images_list'        => _x( 'Array of classes and/or paths', 'Radio button label', 'acf' ),
          'font'               => _x( 'Font path', 'Radio button label', 'acf' ),
        ),
        'layout'       => 'horizontal',
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label'        => _x( 'Images list', 'ACF Admin field creation label', 'acf' ),
        'instructions' => _x( 'Write each image class/URL on a different line.<br>For the URL, you can use either a full URL starting with HTTP or a path relative to the active theme.', 'Admin field creation instructions', 'acf' ),
        'type'         => 'textarea',
        'name'         => 'images_list',
        'layout'       => 'horizontal',
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label'        => _x( 'Folder path', 'ACF Admin field creation label', 'acf' ),
        'instructions' => _x( 'Start the path at the root of the active theme. Example: dist/images/img-for-acf', 'Admin field creation instructions', 'acf' ),
        'type'         => 'text',
        'name'         => 'images_folder_path',
        'layout'       => 'horizontal',
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label'        => _x( 'Font path', 'ACF Admin field creation label', 'acf' ),
        'instructions' => _x( 'Start the path at the root of the active theme. Example: dist/fonts/icomoon', 'Admin field creation instructions', 'acf' ),
        'type'         => 'text',
        'name'         => 'font_path',
        'layout'       => 'horizontal',
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label'   => _x( 'Return format', 'ACF Admin field creation label', 'acf' ),
        'type'    => 'radio',
        'name'    => 'return_format',
        'choices' => array(
          'image_url'       => _x( 'URL', 'Radio button label', 'acf' ),
          'image_path'      => _x( 'Directory path', 'Radio button label', 'acf' ),
          'image_file_name' => _x( 'File name', 'Radio button label', 'acf' ),
        ),
        'layout'  => 'horizontal',
      )
    );

    acf_render_field_setting(
      $field,
      array(
        'label' => _x( 'Allow "None" to be a choice', 'ACF Admin field creation label', 'acf' ),
        'type'  => 'true_false',
        'name'  => 'allow_none',
        'ui'    => 1,
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

    /*
    *  Review the data of $field.
    *  This will show what data is available
    */

    $field = array_merge( $this->defaults, $field );

    // vars
    $choice = '';

    $images = get_images_by_field( $field );

    if ( ! empty( $images ) ) {
      ?>
      <div class='image-select-container'>
        <ul class='image-select'>
          
          <?php
          $checked_is_found = false;

          $attr_checked_for_none = '';
          if ( empty( $field['value'] ) ) {
            $attr_checked_for_none = 'checked';
            $checked_is_found      = true;
          }

          if ( $field['allow_none'] ) {
            $field_none_id = "{$field['id']}-{$field["id"]}-none";
            ?>
            <li class='image-select__item--none'>
              <label for='<?php echo esc_attr( $field_none_id ); ?>'>
                <input name='<?php echo esc_attr( $field['name'] ); ?>' id='<?php echo esc_attr( $field_none_id ); ?>' type='radio' value='' <?php echo esc_attr( $attr_checked_for_none ); ?>>
                <?php echo esc_html_x( 'None', 'ACF None label', 'acf' ); ?> 
              </label>
            </li>
          
            <?php
          } else {
            // We reset the $checked_is_found to make sure that the first image will get chosen.
            $checked_is_found = false;
          } // end choice "none"

          foreach ( $images as $key => $image_definition ) {
            $image_id = "image-select-{$field["id"]}-$key";

            $processed_image_definition = process_image_definition( $image_definition, $key, $field );

            $attr_checked = '';
            if ( ! $checked_is_found ) {
              if ( 'checked' === $attr_checked_for_none || $processed_image_definition['value'] === $field['value'] ) {
                $attr_checked     = 'checked';
                $checked_is_found = true;
              }
            }
            ?>
            <li class='image-select__item'>
              <input name='<?php echo esc_attr( $field['name'] ); ?>' id='<?php echo esc_attr( $image_id ); ?>' type='radio' value='<?php echo esc_attr( $processed_image_definition['value'] ); ?>' <?php echo esc_attr( $attr_checked ); ?>>
              <label for='<?php echo esc_attr( $image_id ); ?>'>
                <?php echo wp_kses_post( get_html_image_processed_by_definition( $processed_image_definition ) ); ?>
              </label>
            </li>
            <?php
          }
          ?>
        </ul>
      </div>
      <?php

    }

  }


  /*
  *  format_value()
  *
  *  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
  *
  *  @type  filter
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $value (mixed) the value which was loaded from the database
  *  @param $post_id (mixed) the $post_id from which the value was loaded
  *  @param $field (array) the field array holding all the field options
  *
  *  @return  $value (mixed) the modified value
  */
  // @codingStandardsIgnoreLine
  public function format_value( $value, $post_id, $field ) {

    if ( ! $value ) {
      return '';
    }

    if ( 'images_folder_path' !== $field['get_image_method'] ) {
      return $value;
    }

    $final_value = $value;

    switch ( $field['return_format'] ) {
      case 'image_path':
            $final_value = convert_relative_path_to_full_directory_path( $value );
            break;
      case 'image_url':
            $final_value = convert_relative_path_to_url( $value );
            break;
      case 'image_file_name':
            $final_value = convert_path_to_file_name( $value );
            break;
    }

    return $final_value;

  }
}
