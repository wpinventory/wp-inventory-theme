<?php
/**
 * @package WordPress
 */
/*
 * Template Name: Home Page
 */
get_header(); ?>
    <div class="contentwrapper">
        <section class="main_content homecontent">
            <article>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="post" id="post-<?php the_ID(); ?>">
                        <?php
                        the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
                    </div>
                <?php endwhile; endif; ?>
            </article>
        </section>
    </div>
<?php get_footer(); ?>