<?php
$sct_title   = get_field( 'info_columns_title' );
$left_blocks = get_field( 'info_columns_left_blocks' );
$right_col   = get_field( 'info_columns_right_content' );
?>
<section class="block-info-columns alignfull">
  <div class="container block-info-columns__inner">
    <?php
    get_template_part(
      'templates/decorated-heading',
      null,
      array(
        'title' => $sct_title,
      )
    );
    ?>
    <div class="info-columns__columns">
      <?php if ( ! empty( $left_blocks ) ) { ?>
        <div class="info-columns__col--left">
          <?php foreach ( $left_blocks as $left_block ) : ?>
            <div class="info-columns__block">
              <div class="info-columns__block__col--icon">
                <?php
                if ( ! empty( $left_block['info_block_icon'] ) && 'none' !== $left_block['info_block_icon'] ) {
                  echo get_file_contents_by_url( $left_block['info_block_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput
                }
                ?>
              </div>
              <div class="info-columns__block__col--main">
                <strong class="info-columns__block__title"><?php echo esc_html( $left_block['info_block_title'] ); ?></strong>
                <div class="info-columns__block__content wysiwyg">
                  <?php echo wp_kses_post( $left_block['info_block_content'] ); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php } ?>
      <?php if ( ! empty( $right_col ) ) { ?>
        <div class="info-columns__col--right wysiwyg">
          <?php echo wp_kses_post( $right_col ); ?>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
