//////// GLOBAL VARS ///////


//////////////// GLOBAL FUNCTIONS ///////////////


(function ($) {
  /////// SCOPE VARS //////
  var registered_effects = {};
  
  /////////////////  SCOPE FUNCTIONS /////////////////
  
  /**
   * Get or create then get the item effects container.
   * In there goes all effects icon affecting the item.
   */
  function get_effects_container_in_menu_item( $menu_item ) {
    var $effects_container = $menu_item.find( '.menu-item-effects' );
    
    if ( $effects_container.length ) {
      return $effects_container;
    }
    
    $menu_item.find( '.menu-item-handle' ).append( '<span class="menu-item-effects"></span>' );
    return $menu_item.find( '.menu-item-effects' );
  }
  
  /**
   * Get or create then get the item icon container sibling to the title.
   */
  function get_title_icon_container_in_menu_item( $menu_item ) {
    var $icon_wrapper = $menu_item.find( '.menu-item-title__icon' );
    
    if ( $icon_wrapper.length ) {
      return $icon_wrapper;
    }
    
    $menu_item.find( '.menu-item-title' ).prepend( '<span class="menu-item-title__icon"></span>' );
    return $menu_item.find( '.menu-item-title__icon' );
  }
  
  function add_menu_item_effect( $menu_item, data_name ) {
    var $effects_container = get_effects_container_in_menu_item( $menu_item );
    
    if ( 'undefined' !== typeof registered_effects[ data_name ] ) {
      console.log( registered_effects[ data_name ] );
      $effects_container.append( '<span class="' + registered_effects[ data_name ] + '"></span>' );
    }
  }
  
  function remove_menu_item_effect( $menu_item, data_name ) {
    var $effects_container = get_effects_container_in_menu_item( $menu_item );

    if ( 'undefined' !== typeof registered_effects[ data_name ] ) {
      $effects_container.find( '.' + registered_effects[ data_name ] ).remove();
    }
  }
  
  /**
   * 
   * @param {Object} fields - { data_name: 'class-name' } The class name will be applied to a span inside effects container.
   */
  function register_effects_by_fields_name( fields ) {
    for ( var name in fields ) {
      registered_effects[ name ] = fields[ name ];
    }
  }
  
  function replace_menu_item_icon( $menu_item, icon ) {
    var $icon_container = get_title_icon_container_in_menu_item( $menu_item );
    var classes = $icon_container.attr( 'class' );
    
    if ( ! icon || 'none' === icon ) {
      classes = classes.replace( /icon-[A-Za-z0-9\-_]+/, '' );
      $icon_container.attr( 'class', classes );
      $icon_container.removeClass( 'icon' );
    } 
    else if ( $icon_container.hasClass( 'icon' ) ) {
      classes = classes.replace( /icon-[A-Za-z0-9\-_]+/, 'icon-' + icon );
      $icon_container.attr( 'class', classes );
    }
    else {
      $icon_container.addClass( 'icon icon-' + icon );
    }
  }
  
  function replace_menu_item_style( $menu_item, style ) {
    $menu_item.removeClass( $menu_item.data( 'last-style' ) );
    $menu_item.addClass( style );
    $menu_item.data( 'last-style', style );
  }
  
  function add_or_remove_effects_by_event( event ) {
    var $input = $( event.target );
    var $input_container = $input.closest( '[data-name]' );
    
    // If this is not a ACF input, we bail.
    if ( ! $input_container.length ) {
      return;
    }
    
    
    var $menu_item = $input_container.closest( '.menu-item' );
    var data_name = $input_container.data( 'name' );
    
    // Search for special effects...
    if ( 'nav_item_icon' === data_name ) {
      replace_menu_item_icon( $menu_item, $input.val() );
    }
    else if ( 'nav_item_style' === data_name ) {
      var selected = $input.find( 'option:selected' );
      replace_menu_item_style( $menu_item, selected.val() );
    }
    // ... but we do the normal affectation if it's not special.
    else {
      var has_effect = false;
      
      if ( $input.hasClass( 'acf-switch-input' ) ) {
        has_effect = $input.prop( 'checked' );
      }
      else if ( $input_container.hasClass( 'acf-field-image' ) ) {
        has_effect = !! $input.val();
      }
      
      if ( has_effect ) {
        add_menu_item_effect( $menu_item, data_name );
      } else {
        remove_menu_item_effect( $menu_item, data_name );
      }
    }
  }
  
  ///////////////// SCOPE INIT FUNCTIONS ////////////////////
  function init_menu_settings_item_acf_special_effects() {
    
    // List of radio buttons
    var $menu_items_with_icon = $( '#menu-to-edit .menu-item [data-name="nav_item_icon"] input:checked' );
    $menu_items_with_icon.each( function () {
      var icon = this.getAttribute( 'value' );
      if ( icon && 'none' !== icon ) {
        replace_menu_item_icon( $( this ).closest( '.menu-item' ), icon );
      }
    });
    
    // Dropdown
    var $menu_items_with_style = $( '#menu-to-edit .menu-item [data-name="nav_item_style"] option:selected' );
    $menu_items_with_style.each( function () {
      var style = this.getAttribute( 'value' );
      if ( style ) {
        replace_menu_item_style( $( this ).closest( '.menu-item' ), style );
      }
    });
  }
  
  function init_menu_settings_item_acf_switch_effects() {
    $( '#menu-to-edit .menu-item [data-name] .-on' ).each( function () {
      var data_name = $( this ).closest( '[data-name]' ).data( 'name' );
      add_menu_item_effect( $( this ).closest( '.menu-item' ), data_name );
    });
  }
  
  function init_menu_settings_item_acf_image_effects() {
    var $menu_items_with_effects = $( '#menu-to-edit .menu-item [data-name] .has-value' );
    $menu_items_with_effects.each( function () {
      var data_name = $( this ).closest( '[data-name]' ).data( 'name' );
      add_menu_item_effect( $( this ).closest( '.menu-item' ), data_name );
    });
  }
  
  
  /////////////// ON READY /////////////////
  $(function () {
    register_effects_by_fields_name({
      'nav_item_is_heading': 'is-heading',
    });
    
    init_menu_settings_item_acf_switch_effects();
    init_menu_settings_item_acf_image_effects();
    init_menu_settings_item_acf_special_effects();
    
    $( '#menu-to-edit' ).on( 'change', add_or_remove_effects_by_event );
    
  }); // end ready
  
})(jQuery); // end conflict sage jQuery
