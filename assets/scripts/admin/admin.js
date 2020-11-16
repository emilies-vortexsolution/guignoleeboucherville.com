//////// GLOBAL VARS ////////



//////// GLOBAL FUNCTIONS ////////



( function ($) {
  //////// SCOPE VARS ////////

  
  
  //////// SCOPE FUNCTIONS ////////

  
  
  //////// SCOPE INIT FUNCTIONS ////////  

  
  
  //////// ON READY ////////
  $( function () {

    wp.domReady(function () {

      // Remove Gutenberg core button style and create new ones
      if( 'undefined' !== typeof wp.blocks ) {
        wp.domReady( function() {
          wp.blocks.unregisterBlockStyle( 'core/image', 'circle-mask' );
  
          // Add style for paragraphs
          wp.blocks.registerBlockStyle( 'core/heading', [
            {
              name: 'default',
              label: 'Défaut',
              isDefault: true,
            },
            {
              name: 'decorated',
              label: 'Décoré',
            },
          ]);
  
        } );
      }
    });
    
  }); // end ready
  
})(jQuery); // end conflict sage jQuery
