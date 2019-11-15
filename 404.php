<?php
/**
 * @package WordPress
 */
// NOTE: This page serves as the "404 Page Template"
get_header(); ?>
    <div class="contentwrapper">
        <section class="main-content page 404">
            <article>
                <h1>We are very sorry but...</h1>
                <p>You have found a page that has been moved or no longer exists. Please use the navigation menu above
                    or the search option below.<br><br><cite>-Thank you!</cite></p>
                <?php include(TEMPLATEPATH . '/searchform.php'); ?>
            </article>
        </section>
    </div>
    <div class="latestblogwrapper">
        <?php acg_get_sidebar("latest_blog", "", TRUE); ?>
    </div>
<?php get_footer(); ?>