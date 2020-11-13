(function($) {
  
  function toggle_dropdown( event ) {    
    event.preventDefault();
    event.stopPropagation();
    
    var $toggle = $( this );
    var $li = $toggle.closest( '.dropdown' );
    var $subnav = $li.children( '.dropdown__subnav-wrapper' );
    
    if( $li.hasClass('opened') ){
      $li.removeClass('opened');
      $subnav.slideUp('fast');
    } else {
      $li.addClass('opened');
      $subnav.slideDown('fast');
    }
  }
  
  function prepare_dropdown_for_nav( $nav ) {
    
    ///// INIT SLIDER POSITION /////
    $nav.find(".active").addClass("opened");
    
    $nav.find(".dropdown").filter(":not(.opened)").each( function() {
      $(this).children('.dropdown__subnav-wrapper').slideUp(0);
    });
    
    
    ///// INIT EVENTS /////
    $nav.find(".dropdown__toggle")
      .on( 'click', toggle_dropdown );
  }
  
  function close_slidebar_menu() {
    $( 'body' ).removeClass( 'mobile-menu-open' );
    $( document )
      .off( 'click.close_slidebar' )
      .off( 'keyup.close_slidebar' );
  }
  
  function try_closing_slidebar_menu( event ) {
    
    // Keyboard
    if ( 'undefined' !== typeof event.keyCode ) {
      
      // ESCAPE
      if ( 27 === event.keyCode ) {
        $('.vtx-burger-container').focus();
        close_slidebar_menu();
      }
    }
    // Click
    else if ( ! $( event.target ).closest( '#mobile-menu-wrapper' ).length ) {
      close_slidebar_menu();
    }
  }
  
  function open_slidebar_menu() {
    $( 'body' ).addClass( 'mobile-menu-open' );
    $( document )
      .on( 'click.close_slidebar', try_closing_slidebar_menu )
      .on( 'keyup.close_slidebar', try_closing_slidebar_menu );
  }
  
  function init_menu_mobile() {
    
    var $mobile_menu = $('#mobile-menu');
    
    $('.vtx-burger-container').on('click', function( event ) {      
      event.stopPropagation();

      if ( $( 'body' ).hasClass( 'mobile-menu-open' ) ) {
        close_slidebar_menu();
      } else {
        open_slidebar_menu();
      }
    });
    
    prepare_dropdown_for_nav( $mobile_menu.find( '.nav-mobile' ) );    
  }
  
  
  $(document).ready(function(){
    
    init_menu_mobile();

  });

})(jQuery);
