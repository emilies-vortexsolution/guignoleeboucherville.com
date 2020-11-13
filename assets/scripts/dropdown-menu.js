/**
 * Reference accessible navigation: https://adrianroselli.com/2019/06/link-disclosure-widget-navigation.html
 * 
 * @todo Rendre plus comme Vie des Arts
 */
(function ($) {
  
  // (!) These classes and selector also need to be changed in the _dropdown-menu.scss (!)
  
  var $nav_with_dropdown = $( '.nav-primary' );
  
  var dropdown_container_selector = '.dropdown';
  var link_wrapper_selector = '.nav-item__link-wrapper';
  
  var dropdown_toggle_class = 'dropdown__toggle';
  var active_class = 'opened';
  
  
  var opened_subnav = {};
  
  var unique_name_count = 0;
  
  var is_temporary_cancel = false;
  
  
  function create_unique_subnav_name() {
    unique_name_count++;
    
    return 'subnav_' + unique_name_count;
  }
  
  
  function try_closing_subnav( event ) {
    for ( var subnav_name in opened_subnav ) {
      
      if ( 'undefined' === typeof subnav_name ) {
        continue;
      }
      
      var $target = opened_subnav[ subnav_name ].find( event.target );
      if( ! $target.length ) {
        /* jshint ignore:start */
        close_subnav( subnav_name );
        /* jshint ignore:end */
        
        continue;
      }
    }
  }
  
  
  function get_opened_subnav_by_name( subnav_name ) {
    if( ! opened_subnav[ subnav_name ] ) {
      console.warn( subnav_name + ' n\'existe pas encore. (get_opened_subnav_by_name())' );
      return $();
    }
    
    return opened_subnav[ subnav_name ];
  }
  
  function get_subnav_name( $subnav ) {
    
    var subnav_name = $subnav.data( 'subnav' );
    
    if( ! subnav_name ) {
      subnav_name = create_unique_subnav_name();
      $subnav.data( 'subnav', subnav_name );
    }
    
    return subnav_name;
  }
  
  
  function set_opened_subnav( $subnav ) {
    var subnav_name = get_subnav_name( $subnav );
    
    opened_subnav[ subnav_name ] = $subnav;
  }
  
  
  function open_subnav( subnav_name ) {
    
    var $subnav = get_opened_subnav_by_name( subnav_name );
    $subnav.addClass( active_class );
    // Items are not visible for the moment and also not targetable by that fact.
    // This delay will permit a focus.
    setTimeout( function () {
      this.find( 'li a' ).first().focus();
      var $toggle = this.find( '.' + dropdown_toggle_class ).first();
      $toggle
        .attr( 'aria-expanded', true )
        .attr( 'aria-label', $toggle.data( 'label-close' ) );
        
        if ( 1 === Object.keys( opened_subnav ).length ) {
          $( document )
            .on( 'focusin.closing_subnav', try_closing_subnav )
            .on( 'click.closing_subnav', try_closing_subnav );
        }
    }.bind( $subnav ), 10 );
  }
  
  
  function close_subnav( subnav_name ) {
    
    var $subnav = get_opened_subnav_by_name( subnav_name );
    $subnav.removeClass( active_class );
    
    var $toggle = $subnav.find( '.' + dropdown_toggle_class ).first();
    $toggle
      .attr( 'aria-expanded', false )
      .attr( 'aria-label', $toggle.data( 'label-open' ) );
    
    delete opened_subnav[ subnav_name ];
    
    // Supprimer les événements si il n'y a plus de menu ouvert.
    if( ! Object.keys( opened_subnav ).length ) {
      $( document )
        .off( 'click.closing_subnav' )
        .off( 'focusin.closing_subnav' );
    }
  }
  
  
  /////////////// ON READY /////////////////
  $( function () {
    
    $nav_with_dropdown.find( '.' + dropdown_toggle_class )
      .on( 'click', function ( event ) {
        event.stopPropagation();
        
        var $dropdown_container = $( this ).closest( dropdown_container_selector );
        
        if( ! $dropdown_container.hasClass( active_class ) ) {          
          
          // On pourrait le faire tout le temps, mais en théorie on a déjà set un sub menu au close.
          // C'est donc plus performant de le faire juste ici.
          set_opened_subnav( $dropdown_container );
          
          open_subnav( get_subnav_name( $dropdown_container ) );
        } else {
          close_subnav( get_subnav_name( $dropdown_container ) );
        }
      })
      .on( 'focusin', function () {
        var $dropdown_container = $( this ).closest( dropdown_container_selector );
        if( $dropdown_container.hasClass( active_class ) ) {
          close_subnav( get_subnav_name( $dropdown_container ) );
        }
      });
  });
})(jQuery);