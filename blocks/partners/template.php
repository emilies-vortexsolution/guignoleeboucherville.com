<?php
$sct_title = ! empty( get_field( 'sct_title' ) ) ? get_field( 'sct_title' ) : __( 'Our partners', 'vtx' );
$partners  = get_posts(
  array(
    'post_type' => 'partner',
  )
);
?>

<?php if ( ! empty( $partners ) ) : ?>
  <section class="block-partners theme-block alignfull">
    <div class="block-partners__background--left" role="presentation"></div>
    <div class="block-partners__background--right" role="presentation"></div>
    <div class="block-partners__inner container">
      <?php if ( ! empty( $sct_title ) ) { ?>
        <div class="block-partners__header">
          <h2 class="block-partners__title"><?php echo esc_html( $sct_title ); ?></h2>
        </div>
      <?php } ?>
      <div class="listing--partners">
        <?php
        foreach ( $partners as $partner ) :
          get_template_part(
            'templates/partner/item',
            null,
            array(
              'partner' => $partner,
            )
          );
        endforeach;
        ?>
      </div>
    </div>
  </section>
  <?php
endif;
