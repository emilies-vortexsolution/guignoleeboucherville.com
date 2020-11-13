<?php
if ( have_rows( 'site_social_media', 'option' ) ) {
  ?>
  <div class='social-media-container'>
    <?php
    while ( have_rows( 'site_social_media', 'option' ) ) {
      the_row();

      $social_media_names = get_sub_field( 'social_media' );
      $url                = get_sub_field( 'social_media_url' );
      ?>
      <a href='<?php echo esc_url( $url ); ?>' class='social-media-button <?php echo esc_html( $social_media_names['value'] ); ?> icon-container icon-<?php echo esc_html( $social_media_names['value'] ); ?>' target='_blank' title='<?php /* translators: social media name */ echo esc_html( sprintf( _x( 'See our %s page', "Social media link's title attribute", 'vtx' ), $social_media_names['label'] ) ); ?>'>
        <span class='sr-only'><?php echo esc_html( $social_media_names['label'] ); ?></span>
      </a>
      <?php
    }
    ?>
  </div>
  <?php
}
