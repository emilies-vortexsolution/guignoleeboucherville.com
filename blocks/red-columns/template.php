<?php if ( have_rows( 'columns_group' ) ) : ?>
  <section class="block-red-columns alignfull">
    <div class="red-columns__columns">
      <?php
      while ( have_rows( 'columns_group' ) ) :
        the_row();
        ?>
        <div class="red-columns__column">
          <?php if ( '' !== get_sub_field( 'column_title' ) ) { ?>
            <h3 class="red-columns__column__title"><?php echo esc_html( get_sub_field( 'column_title' ) ); ?></h3>
          <?php } ?>
          <div class="wysiwyg">
            <?php echo wp_kses_post( get_sub_field( 'column_content' ) ); ?>
          </div>  
        </div>
      <?php endwhile; ?>
    </div>
  </section>
<?php endif;
