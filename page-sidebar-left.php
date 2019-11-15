<?php
/**
 * @package WordPress
 */
/*
 * Template Name: Sidebar Left
 */
get_header(); ?>
    <div class="contentwrapper">
        <section class="main_content homecontent sbar_left">
            <span class="sidebar_flyout">
                <span class="bar_one"></span>
                <span class="bar_two"></span>
                <span class="bar_three"></span>
            </span>
            <article>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="post" id="post-<?php the_ID(); ?>">
                        <?php
                        the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
                    </div>
                <?php endwhile; endif; ?>
            </article>
            <aside>
                <?php acg_get_sidebar('default_sidebar'); ?>
            </aside>
        </section>
    </div>
<?php get_footer(); ?>