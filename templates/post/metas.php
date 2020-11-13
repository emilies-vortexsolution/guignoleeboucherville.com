<time class="updated" datetime="<?php echo esc_html( get_post_time( 'c', true ) ); ?>"><?php the_date(); ?></time>
<p class="byline author vcard"><?php echo esc_html_x( 'By', 'Author vcard prefix', 'vtx' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author" class="fn"><?php the_author(); ?></a></p>
