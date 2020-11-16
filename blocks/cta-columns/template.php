<?php if ( have_rows( 'cta_columns' ) ) : ?>
  <section class="block-cta-columns alignfull">
    <div class="block-cta-columns__background--left" role="presentation">
      <?php
      echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/flocon-BG.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
      ?>
    </div>
    <div class="block-cta-columns__background--right" role="presentation">
      <?php
      echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/flocon-BG.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
      ?>
    </div>
    <div class="block-cta-columns__inner container">
      <div class="cta-columns__columns">
        <?php
        while ( have_rows( 'cta_columns' ) ) :
          the_row();
          $cta_image = get_sub_field( 'cta_image' );
          ?>
          <div class="cta-columns__cta">
            <div class="cta-columns__cta__background" role="presentation">
              <?php
              echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/flocon-BG.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
              ?>
            </div>
            <div class="cta-columns__cta__inner">
              <div class="cta-columns__cta__col--image">
                <?php
                if ( ! empty( $cta_image['ID'] ) ) {
                  echo wp_kses_post( wp_get_attachment_image( $cta_image['ID'], 'medium' ) );
                }
                ?>
              </div>
              <div class="cta-columns__cta__col--main">
                <?php if ( ! empty( get_sub_field( 'cta_title' ) ) ) { ?>
                  <h2 class="cta-columns__cta__title heading-size-3"><?php echo esc_html( get_sub_field( 'cta_title' ) ); ?></h2>
                <?php } ?>
                <div class="wysiwyg"><?php echo wp_kses_post( get_sub_field( 'cta_content' ) ); ?></div>
              </div>
            </div>
          </div>
          <?php
        endwhile;
        ?>
      </div>
    </div>
  </section>
  <?php
endif;
