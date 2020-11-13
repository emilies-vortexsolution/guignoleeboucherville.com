<?php
/**
 * Mandatory params:
 *  @param data {Array of WP_Post|Array of WP_Term|Array of specials}
 *    If $data contain an Array of specials, they must have those parameters:
 *      slug {String} - A Sanitized name
 *      title {String}
 *
 * Possible params:
 *  @param get_property {String} - Accepted values are "title", "slug" or "id". Cannot be "id" if $data is special. "vtx_fselect_value"
 *  @param input_name {String} - Name to retrieve the field value. Make sure each fselect has a unique one. "vtx_fselect_value_{unique number}" is the default.
 *  @param select_multiple {String} - Default: TRUE
 *  @param class {String} - Classes to add on the wrapper element.
 *  @param placeholder {String} - Placeholder for the select.
 *  @param reselect {Boolean} - Selected value from last submit will be checked at page load if true.
 *
 * Automatic params:
 *  @param selected_values {Array} - Those values to check if "reselect" is TRUE. Automatically fetch from request data.
 *  @param data_type {String} - Determined by objects inside the parameter "data".
 *  @param prop_id {String} - Object's id property wich could be "ID" for WP_Posts or "term_id" for WP_Terms.
 *  @param prop_slug {String} - Object's slug property wich could be "post_name" for WP_Posts or "slug" for WP_Terms.
 *  @param prop_title {String} - Object's title property wich could be "post_title" for WP_Posts or "name" for WP_Terms.
 */


// Cancel rendering the partial if there is no data.
if ( empty( $data ) ) {
  return;
}

$dropdown_id = add_string_unique_suffix( 'vtx-fselect-dropdown' );
?>

<div class="vtx-fselect-wrapper <?php echo esc_attr( $class ); ?>">
  <div class="vtx-fselect" data-select-multiple="<?php echo ( $select_multiple ) ? 'true' : 'false'; ?>">
  
    <fieldset class="vtx-fselect-inner">
      <legend class="sr-only">
        <?php
        if ( $select_multiple ) {
          echo esc_html_x( 'Choose one or more categories', 'Fieldset Legend', 'vtx' );
        } else {
          echo esc_html_x( 'Choose a category', 'Fieldset Legend', 'vtx' );
        }
        ?>
      </legend>
        
        <div
          role="button"
          aria-haspopup="true"
          aria-expanded="false"
          aria-controls="<?php echo esc_attr( $dropdown_id ); ?>"
          tabindex="0"
          class="vtx-fselect__active-choices vtx-fselect__toggle-dropdown">
          
          <span class="sr-only"><?php echo esc_html_x( 'Active choices:', 'SR only: Before choices enumeration', 'vtx' ); ?></span>
          <strong class="vtx-fselect__active-choices__label" data-default-label="<?php echo esc_attr( $placeholder ); ?>"><?php echo esc_html( $placeholder ); ?></strong>
          
          <span class="vtx-fselect__active-choices__label-extra-wrapper">
            <span class="vtx-fselect__active-choices__label-extra" aria-hidden="true"></span>
            <span class="vtx-fselect__active-choices__label-extra--sr-only sr-only" data-default-label="<?php echo esc_html_x( 'and 0 more.', 'SR only: Extra labels count prefix. 0 could be any number.', 'vtx' ); ?>"></span>
          </span>
          
          <span class="vtx-fselect__dropdow-icon" role="presentation"></span>
        </div>
        
        <div id="<?php echo esc_attr( $dropdown_id ); ?>" class="vtx-fselect__dropdown">
          
          <?php
          $at_load_values = '';
          foreach ( $selected_values as $value => $not_using_this ) {
            $at_load_values .= "$value,";
          }
          $at_load_values = rtrim( $at_load_values, ',' );
          ?>
          <input class="vtx-fselect__final-value" type="hidden" data-type="<?php echo esc_attr( $data_type ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $at_load_values ); ?>">
          
          
          <?php
          $input_id        = add_string_unique_suffix( 'vtx-fselect-search' );
          $options_list_id = add_string_unique_suffix( 'vtx-fselect-options' );
          ?>
          <div class="vtx-fselect__dropdown__search-wrapper">
            <label class="sr-only" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html_x( 'Search a category', 'Placeholder input', 'vtx' ); ?></label>
            <input id="<?php echo esc_html( $input_id ); ?>" class="vtx-fselect__dropdown__search" type="search" value="" placeholder="<?php echo esc_html_x( 'Search', 'Placeholder input', 'vtx' ); ?>" autocomplete="off" aria-controls="<?php echo esc_html( $options_list_id ); ?>">
          </div>

          <div class="vtx-fselect__dropdown__options-wrapper" aria-live="polite">
            <ul class="vtx-fselect__dropdown__options" id="<?php echo esc_attr( $options_list_id ); ?>">
            
              <?php
              $type_class = ( $select_multiple ) ? 'checkbox_container' : 'radio_container';

              if ( ! $select_multiple ) {

                $attr_checked = '';
                if ( empty( $selected_values ) ) {
                  $attr_checked = 'checked';
                }

                $input_id = esc_attr( add_string_unique_suffix( 'vtx-fselect-search' ) );
                ?>
                  
                <li class="vtx-fselect__dropdown__options__input-wrapper <?php echo esc_attr( $type_class ); ?>" data-search="">
                  <input id="<?php echo esc_attr( $input_id ); ?>" type="checkbox" value="" <?php echo esc_attr( $attr_checked ); ?>>
                  <label 
                    class="vtx-fselect__dropdown__options__label"
                    for="<?php echo esc_attr( $input_id ); ?>">
                      <?php echo esc_attr( $placeholder ); ?>
                  </label>
                </li>
                
                <?php
              }

              foreach ( $data as $item ) {
                $item = (object) $item;

                $input_id     = esc_attr( add_string_unique_suffix( $item->{$prop_slug} ) );
                $value        = $item->{$get_property};
                $attr_checked = '';

                if ( isset( $selected_values[ $value ] ) ) {
                  $attr_checked = 'checked';
                }
                ?>
                
                <li class="vtx-fselect__dropdown__options__input-wrapper <?php echo esc_attr( $type_class ); ?>" data-search="<?php echo esc_html( sanitize_searchable( $item->{$prop_title} ) ); ?>">
                  <input id="<?php echo esc_attr( $input_id ); ?>" type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $attr_checked ); ?>>
                  <label 
                    class="vtx-fselect__dropdown__options__label"
                    for="<?php echo esc_attr( $input_id ); ?>">
                      <?php echo wp_kses_post( $item->{$prop_title} ); ?>
                  </label>
                </li>
                
              <?php } ?>
            </ul>
            
            <p class="vtx-fselect__dropdown__options__label vtx-fselect__dropdown__options__label--no-results hide-when-no-results"><?php echo esc_html_x( 'No results found', 'Search no results', 'vtx' ); ?></p>
          </div>
          
        </div>
    </fieldset>
  </div>
</div><!-- \.collapsible-search-form__field-wrapper -->
