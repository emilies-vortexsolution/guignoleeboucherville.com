<?php
$args = wp_parse_args(
  $args,
  array(
    'title' => '',
  )
);

if ( '' !== $args['title'] ) : ?>
  <div class="decorated-heading">
    <?php
    echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/guy-gauche.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
    ?>
    <h2 class="decorated-heading__title"><?php echo esc_html( $args['title'] ); ?></h2>
    <?php
    echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/guy-droite.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
    ?>
  </div>
  <?php
endif;
