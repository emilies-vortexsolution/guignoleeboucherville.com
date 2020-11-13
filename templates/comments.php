<?php
if ( post_password_required() ) {
  return;
}
?>

<section id="comments" class="comments">
  <?php if ( have_comments() ) : ?>
    <h2>
    <?php
    /* translators: %s: number of comments, title of article */
    echo wp_kses_post( sprintf( _nx( '%1$s response to &ldquo;%2$s&rdquo;', '%1$s responses to &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'vtx' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' ) );
    ?>
    </h2>

    <ol class="comment-list">
      <?php
      wp_list_comments(
        array(
          'style'      => 'ol',
          'short_ping' => true,
        )
      );
      ?>
    </ol>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
      <nav>
        <ul class="pager">
          <?php if ( get_previous_comments_link() ) : ?>
            <li class="previous"><?php previous_comments_link( __( '&larr; Older comments', 'vtx' ) ); ?></li>
          <?php endif; ?>
          <?php if ( get_next_comments_link() ) : ?>
            <li class="next"><?php next_comments_link( __( 'Newer comments &rarr;', 'vtx' ) ); ?></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; // end of have_comments(); ?>

  <?php if ( ! comments_open() && get_comments_number() !== '0' && post_type_supports( get_post_type(), 'comments' ) ) : ?>
    <div class="alert alert-warning">
      <?php esc_html_e( 'Comments are closed.', 'vtx' ); ?>
    </div>
  <?php endif; ?>

  <?php comment_form(); ?>
</section>
