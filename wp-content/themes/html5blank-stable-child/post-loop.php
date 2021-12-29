<?php if (have_posts()): while (have_posts()) : the_post(); ?>

	<!-- article -->
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background: url(<?php the_post_thumbnail_url( 'full-size' )?>);">

        <div class="post-loop-content">

            <!-- post title -->
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <h2><?php the_title(); ?></h2>
            </a>
            <!-- /post title -->

            <!-- post details -->
            <span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
            <span class="comments" style="float:right;"><?php if (comments_open( get_the_ID() ) ) comments_popup_link( __( 'Leave your thoughts', 'html5blank' ), __( '1 Comment', 'html5blank' ), __( '% Comments', 'html5blank' )); ?></span>
            <!-- /post details -->

            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <?php html5wp_excerpt('html5wp_index'); // Build your custom callback length in functions.php ?>
            </a>

            <?php edit_post_link(); ?>
        </div>

	</article>
	<!-- /article -->

<?php endwhile; ?>

<?php else: ?>

	<!-- article -->
	<article>
		<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>
