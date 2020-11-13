<footer>
  <?php
  wp_link_pages(
    array(
      'before' => '<nav class="page-nav"><p>' . __( 'Pages:', 'vtx' ),
      'after'  => '</p></nav>',
    )
  );
  ?>
</footer>
