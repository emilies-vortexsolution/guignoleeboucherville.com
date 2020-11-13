/**
 * Create a "Class" containing all the custom Vortex fselect based on FacetWP.
 * 
 * @example JS - Initiating all fselect on the page. This is done by default at DOM ready.
 * 
 *  VTX_FSelect.init();
 * 
 * 
 * @example JS - Initiating a single fselect. Usefull if fselect has been dynamically added.
 * 
 *  var new_fselect = VTX_FSelect.new_fselect( $some_fselect_jquery_object );
 *  if( new_fselect ) {
 *    new_fselect.init();
 *  }
 * 
 *
 * 
 */
var VTX_FSelect = ( function ($) {
  
  var fselect = {
    
    selector: '.vtx-fselect',
    all_fselects: [],
    
    
    /**
     * 
     * @param {jQuery Object} $fselect
     * 
     * @return {boolean|Object} Returns false if the fselect has already been initiated.
     * 
     * @todo Add keyboard control for start and end of list when focus is on an option.
     * @todo Make start focus aim for the nearest checked element.
     * @todo Once focus is inside select, maybe try to act like a select when pressing a letter and jump focus.
     */
    new_fselect: function ( $fselect ) {
      
      if( $fselect.hasClass( 'is-initiated' ) ) {
        return false;
      }
      
      return {
      
        $fselect: $fselect,
        $options: $fselect.find( '.vtx-fselect__dropdown__options__input-wrapper' ),
        $search_field: $fselect.find( '.vtx-fselect__dropdown__search' ),
        $toggle: $fselect.find( '.vtx-fselect__toggle-dropdown' ),
        $input_final_value: $fselect.find( '.vtx-fselect__final-value' ),
        $active_choices_label: $fselect.find( '.vtx-fselect__active-choices__label' ),
        $active_choices_label_extra: $fselect.find( '.vtx-fselect__active-choices__label-extra' ),
        $active_choices_label_extra_sr_only: $fselect.find( '.vtx-fselect__active-choices__label-extra--sr-only' ),
        
        select_multiple: true,
        search_timeout: null,
        
        trigger_select_move_on_input: function ( $input ) {
          if( $input.length ) {
            $input.focus();
            // if( ! this.select_multiple ) {
            //   this.$options.find( 'input' ).prop( 'checked', false );
            //   $input.trigger( 'click' );
            // }
          }
        },
        
        filter_dropdown_options: function ( value ) {          
          if( value ) {
            // Tous les cacher pour afficher seulement ceux qui doivent par la suite
            this.$options.hide();

            value = VTX_FSelect.sanitize_searchable( value );

            var $results = this.$options.filter( '[data-search*="' + value + '"]' );
            if( $results.length ) {
              $results.show();
              this.$fselect.removeClass( 'no-results' );
            } else {
              this.$fselect.addClass( 'no-results' );
            }
            
          } else {
            this.$options.show();
            this.$fselect.removeClass( 'no-results' );
          }
        },
        
        open: function () {
          this.$fselect.addClass( 'opened' );
          this.$toggle.attr( 'aria-expanded', true );
          this.$search_field.focus();
          this.init_close_events();
        },
        
        close: function () {
          this.$fselect.removeClass( 'opened' );
          this.$toggle.attr( 'aria-expanded', false );
          this.delete_close_events();
        },
        
        try_closing: function ( e ) {
          // Si c'est un keypress..
          if( 'undefined' !== typeof e.keyCode ) {            
            // ESCAPE
            if( 27 === e.keyCode ) {
              e.preventDefault();
              e.stopPropagation();
              this.close();
              this.$toggle.focus();
            }
          }
          // ... sinon, pour tout autres raisons, on vérifie si le focus a quitté le fselect.
          else if( ! this.$fselect.find( e.target ).length ) {
            this.close();
          }
        },
        
        toggle: function () {
          if( 'false' === this.$toggle.attr( 'aria-expanded' ) ) {
            this.open();
          } else {
            this.close();
          }
        },
        
        change_active_choices_label: function () {
          var $choices = this.$options.find( 'input:checked' );
          
          var new_label = '';
          var new_label_extra = '';
          var new_label_extra_sr_only = '';
          var max_shown_choices = 2;
          var extra_choices_count = 0;
          
          if( $choices.length ) {
            var i = 0;
            $choices.each( function () {
              i++;
              
              // Ajouter le label à l'élément...
              if( max_shown_choices >= i ) {
                new_label += $( this ).siblings( 'label' ).text();
                
                if( i !== $choices.length && i !== max_shown_choices ) {
                  new_label = new_label.trim() + ', ';
                }
              } 
              // ... sauf si ça dépasse le nombre de caractère max.
              else {
                
                if( 0 === extra_choices_count ) {
                  new_label = new_label.trim() + '<span aria-hidden="true"> ...</span>';
                }
                extra_choices_count++;
              }
            });
            
          } else {
            new_label = this.$active_choices_label.data( 'default-label' );
          }
          
          this.$active_choices_label.html( new_label );
          
          if( extra_choices_count ) {
            new_label_extra = '+' + extra_choices_count;
            new_label_extra_sr_only = this.$active_choices_label_extra_sr_only.data( 'default-label' ).replace( /^([A-Za-z-_ \.]*)\d+([A-Za-z-_ \.]*)$/i, '$1' + extra_choices_count + '$2' );
            
            this.$active_choices_label_extra.html( new_label_extra );
            this.$active_choices_label_extra_sr_only.html( new_label_extra_sr_only );
          } else if( this.select_multiple ) {
            this.$active_choices_label_extra.html( '' );
            this.$active_choices_label_extra_sr_only.html( '' );
          }
        },
        
        init_close_events: function () {
          $( document )
            .on( 'click.vtx_fselect.close', this.try_closing.bind( this ) )
            .on( 'focusin.vtx_fselect.close', this.try_closing.bind( this ) )
            .on( 'keydown.vtx_fselect.close', this.try_closing.bind( this ) );
        },
        delete_close_events: function () {
          $( document )
            .off( 'click.vtx_fselect.close' )
            .off( 'focusin.vtx_fselect.close' )
            .off( 'keydown.vtx_fselect.close' );
        },
        
        init_open_events: function () {
          this.$toggle.on( 'click.vtx_fselect', this.toggle.bind( this ) );
          this.$toggle.on( 'keydown.vtx_fselect', function ( e ) {
            // ENTER
            if( 13 === e.keyCode ) {
              e.preventDefault();
              this.toggle();
            } 
            // ARROW DOWN
            else if( 40 === e.keyCode ) {
              e.preventDefault();
              this.open();
            }
          }.bind( this ) );
        },
        
        init_filter_events: function () {
          
          this.$search_field.on( 'keydown.vtx_select', function ( e ) {
            // Empêcher la soumission du formulaire avec ENTER si on est dans le search du fselect
            if( 13 === e.keyCode ) {
              e.preventDefault();
            }
          }.bind( this ) );
          
          this.$search_field.on( 'keyup.vtx_select', function () {
            var value = this.$search_field.val();
            
            if( this.search_timeout ) {
              clearTimeout( this.search_timeout );
            }
            
            this.search_timeout = setTimeout( function () {
              this.filter_dropdown_options( value );
            }.bind( this ), 150 );
            
          }.bind( this ) );
          
          
          this.$options.on( 'keydown.vtx_select', function ( e ) {
            var $input = $( e.target );
            
            // Empêcher la soumission du formulaire avec ENTER si on est dans le search du fselect
            if( 13 === e.keyCode ) {
              e.preventDefault();
              $input.trigger( 'click' );
            }
            // ARROW UP
            else if( 38 === e.keyCode ) {
              e.preventDefault();
              this.trigger_select_move_on_input( $input.parent().prev().find( 'input' ) );
            }
            // ARROW DOWN
            else if( 40 === e.keyCode ) {
              e.preventDefault();
              this.trigger_select_move_on_input( $input.parent().next().find( 'input' ) );
            }
            
          }.bind( this ) );
          
          
          this.$options.on( 'change.vtx_select', function( e ) {
            var $this_input = $( e.target );
            
            var new_value = $this_input.val();
            var final_value = '';
            
            if( this.select_multiple ) {
              final_value = this.$input_final_value.val();

              // Si la valeur n'existe pas.
              if( -1 !== final_value.indexOf( new_value ) ) {
                final_value = final_value.replace( ',' + new_value, '' );
                final_value = final_value.replace( new_value + ',', '' );
                final_value = final_value.replace( new_value, '' );
                
              } else {
                
                if( final_value ) {
                  final_value += ',' + new_value;
                } else {
                  final_value += new_value;
                }
              }
            } else {
              this.$options.find( 'input:checked' ).prop( 'checked', false );
              $this_input.prop( 'checked', true );
              final_value = new_value;
            }

            this.$input_final_value.val( final_value );

            this.change_active_choices_label();
            
          }.bind( this ) );
          
          /**
           * Close the dropdown on click if select_multiple is FALSE to act like a normal select.
           */
          if( ! this.select_multiple ) {
            this.$options.on( 'click.vtx_select', function( e ) {
              this.close();
            }.bind( this ) );
          }
          
          /**
           * Corriger la position du scroll du wrapper quand on change le focus.
           * (!) N'est pas tout à fait fonctionnel quand le focus tombe sur un élément plus haut (!)
           */
          this.$options.on( 'focusin.vtx_select', function( e ) {
            var $li = $( e.target ).parent();
            var $ul = $li.parent();
            var $wrapper = $ul.parent();
            
            var wrapper_height = $wrapper.height();
            var wrapper_scroll_top = $wrapper.scrollTop();
            var li_height = $li.height();
            var li_top = $li.position().top;
            var ul_top = $ul.position().top * -1;
            var wrapper_padding = li_top % li_height;

            if( wrapper_height + wrapper_scroll_top < ul_top + li_top + li_height + wrapper_padding ) {
              $wrapper.scrollTop( wrapper_scroll_top + li_height );
            } else if( wrapper_scroll_top > ul_top + li_top + wrapper_padding ) {
              $wrapper.scrollTop( Math.max( wrapper_scroll_top - li_height, 0 ) );
            }
            
          }.bind( this ) );
        },
        
        init_form_events: function () {
          var $parent_form = this.$fselect.closest( 'form' );
          if ( $parent_form.length && ! $parent_form.hasClass( 'vtx-fselect-form-initiated' ) ) {
            $parent_form
              .addClass( 'vtx-fselect-form-initiated' )
              .on( 'reset.vtx_fselect', function ( event ) {
                var $form = $( event.target );
                var $all_form_fselects = $form.find( VTX_FSelect.selector );
                
                $all_form_fselects.each( function () {
                  var $fselect = $( this );
                  var $options = $fselect.find( '.vtx-fselect__dropdown__options__input-wrapper' );
                  var $input_final_value = $fselect.find( '.vtx-fselect__final-value' );
                  
                  $options
                    .find( 'input:checked' )
                    .prop( 'checked', false );
                    
                  if ( ! $fselect.data( 'select-multiple' ) ) {
                    $options
                      .first()
                      .find( 'input' )
                      .prop( 'checked', true );
                  }
                    
                  $input_final_value.val( '' );
                });
                
                VTX_FSelect.refresh_fselects_label();
              });
          }
        },
        
        init: function () {
          this.select_multiple = this.$fselect.data( 'select-multiple' );          
          this.init_open_events();
          this.init_filter_events();
          this.init_form_events();
          this.change_active_choices_label();
          this.$fselect.addClass( 'is-initiated' );
        },
        
      };
    },
    
    refresh_fselects_label: function () {
      for ( var i = this.all_fselects.length - 1; i >= 0; i-- ) {
        this.all_fselects[ i ].change_active_choices_label();
      }
    },
    
    sanitize_searchable: function ( text ) {
      return text.replace( /[&<>"']/g, '' ).toLowerCase();
    },
    
    esc_attr: function ( text ) {
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
    
      return text.replace( /[&<>"']/g, function(m) { return map[m]; } );
    },
    
    init: function ( element_selector ) {
      if( 'undefined' !== typeof element_selector ) {
        this.selector = element_selector;
      }
      
      $( this.selector ).map( function( index, element ) {
        var new_fselect = this.new_fselect( $( element ) );
        if( new_fselect ) {
          new_fselect.init();
          this.all_fselects.push( new_fselect );
        }
      }.bind( this ) );
      
      return this.all_fselects;
    },
  };
  
  
  // Init all fselect at DOM ready.
  $( function () {
    fselect.init();
  });
  
  return fselect;
})(jQuery);
