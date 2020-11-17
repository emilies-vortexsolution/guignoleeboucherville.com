<footer class="site-footer">
  <div class="container">
    <?php 
    $rights_html = _x( 'All rights reserved.', 'copyrights footer', 'vtx' );
    if ( get_privacy_policy_url() ) {
      $rights_html = '<a href="' . get_privacy_policy_url() . '">' . _x( 'All rights reserved.', 'copyrights footer', 'vtx' ) . '</a>';
    }
    ?>
    <span class="site-footer__copyrights">&copy; <?php echo esc_html( gmdate( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. ' . wp_kses_post( $rights_html ); ?></span>
    <span class="site-footer__bottom__agency"><a href="https://www.vortexsolution.com/" target="_blank"> <?php echo esc_html_x( 'Web agency', 'copyrights footer', 'vtx' ); ?></a> <a href="https://www.vortexsolution.com/" target="_blank">Vortex Solution</a></span>
  </div>
</footer>
