//////// GLOBAL VARS ///////


//////////////// GLOBAL FUNCTIONS ///////////////


(function ($) {
  /////// SCOPE VARS //////

  
  ///////////////// SCOPE INIT FUNCTIONS ////////////////////
  function init_add_nav_menu_settings_column_fixed() {
    
    var $column = $( "#menu-settings-column" );
    if( $column.length ) {
      
      var col_pos = $column.position();
      var $window = $( window );
      
      $window
        .off("scroll.sticky-col")
        .on("scroll.sticky-col", function(e) {
          var top_actual = col_pos.top - $window.scrollTop();

          if( 0 < top_actual ) {
            $column.removeClass( "is-sticky" );
          } else {
            $column.addClass( "is-sticky" );
          }
        });
    }
    
  }
  
  
  /////////////////  SCOPE FUNCTIONS /////////////////
  
  
  /////////////// ON READY /////////////////
  $(function () {
    
    init_add_nav_menu_settings_column_fixed();
    
  }); // end ready
  
})(jQuery); // end conflict sage jQuery
