<?php
$sct_title = ! empty( get_field( 'sct_title' ) ) ? get_field( 'sct_title' ) : __( 'Our partners', 'vtx' );
$partners  = get_posts(
  array(
    'post_type'   => 'partner',
    'numberposts' => -1,
    'order'       => 'ASC',
  )
);
?>

<?php if ( ! empty( $partners ) ) : ?>
  <section class="block-partners theme-block alignfull">
    <div class="block-partners__background--left" role="presentation"></div>
    <div class="block-partners__background--right" role="presentation"></div>
    <div class="block-partners__inner container">
      <?php
      get_template_part(
        'templates/decorated-heading',
        null,
        array(
          'title' => $sct_title,
        )
      );
      ?>
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
