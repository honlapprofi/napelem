<?php get_header(); ?>

    <main role="main">
        <!-- section -->
        <section class="latest-posts">

            <div class="latest-title">
                <h1><?php single_post_title(); ?></h1>
            </div>

            <div class="post-grid">
                <?php get_template_part('post-loop'); ?>
            </div>

            <div class="post-pagination">
                <?php get_template_part('pagination'); ?>
            </div>

        </section>
        <!-- /section -->
    </main>

<?php /*get_sidebar();*/ ?>

<?php get_footer(); ?>
