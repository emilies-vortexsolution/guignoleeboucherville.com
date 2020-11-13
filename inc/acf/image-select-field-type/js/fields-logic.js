//////// GLOBAL VARS ///////


//////////////// GLOBAL FUNCTIONS ///////////////


(function ($) {
  /////// SCOPE VARS //////

  
  ///////////////// SCOPE INIT FUNCTIONS ////////////////////
  function init_image_method_fields_logic( field ) {
    
    if( field && "image_select" === field.data.setting ) {
      if( field.$el.hasClass("acf-field-setting-get_image_method") ) {
        check_show_hide_logic_for_field_settings( field.$el.closest(".acf-field-settings") );
      }
    }
    
  }
  
  /////////////////  SCOPE FUNCTIONS /////////////////
  function check_show_hide_logic_for_field_settings( $field_settings ) {
   
    var $field_image_method_checked_input = $field_settings.find( '.acf-field-setting-get_image_method input:checked' );
    var value = $field_image_method_checked_input.val();
    
    var $field_folder_path = $field_settings.find( '.acf-field-setting-images_folder_path' );
    var $field_font_path = $field_settings.find( '.acf-field-setting-font_path' );
    var $field_images_list = $field_settings.find( '.acf-field-setting-images_list' );
    var $field_return_format = $field_settings.find( '.acf-field-setting-return_format' );
    console.log( value );
    
    if( 'images_folder_path' === value ) {
      $field_folder_path.show();
      $field_return_format.show();
      
      $field_images_list.hide();
      $field_font_path.hide();
      
    } else if( 'font' === value ) {
      $field_font_path.show();
      
      $field_images_list.hide();
      $field_folder_path.hide();
      $field_return_format.hide();
      
    } else {
      $field_images_list.show();
      
      $field_font_path.hide();
      $field_folder_path.hide();
      $field_return_format.hide();
    }
  }
  
  /////////////// ON READY /////////////////
  $(function () {
    
    var $field_list = $( '.acf-field-list' );

    if( $field_list.length ) {
      
      $( '.acf-field-object-image-select' ).each(function() {
        check_show_hide_logic_for_field_settings( $( this ).find( '.acf-field-settings' ) );
      });
      
      $field_list.on( 'change', '.acf-field-setting-get_image_method input', function() {
        check_show_hide_logic_for_field_settings( $( this ).closest( '.acf-field-settings' ) );
      });
    }
    
    acf.addAction( 'new_field', init_image_method_fields_logic );
    
  }); // end ready
  
})(jQuery);
