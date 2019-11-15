<?php
/**
 * The Template for displaying all single posts.
 */
get_header(); ?>
    <div class="contentwrapper">
        <section class="main_content blogcontent single">
            <article>
                        <div class="date post-date updated">
                            <p>&mdash; <?php the_time(get_option('date_format')); ?> &mdash;</p>
                        </div>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div <?php post_class() ?> id="post-<?php the_ID(); ?>">

                        <?php
                        $category = get_the_category($post->ID);
                        $first_category = (isset($category[0])) ? "{$category[0]->cat_name}" : "";
                        ?>
                        <div class="entry">
                            <?php
                            the_content('<p class="serif">Read the rest of this entry &raquo;</p>');
                            // echo apply_filters('the_content', '');
                            ?>
                            <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
                        </div>
                    </div>
                <?php endwhile;
                else: ?>
                    <p>Sorry, no posts matched your criteria.</p>
                <?php endif; ?>
            </article>
            <div id="post-taxonomy-attributions">
                <?php
                 if ( the_tags() != NULL) {
                     echo '<p><span class="label">TAGS: </span>' . the_tags('', ', ') . '</p>';
                 }

                if (the_category() != NULL) {
                    echo '<p><span class="label">POSTED IN: </span>' . the_category(', ') . '</p>';
                }
                ?>
            </div>
            <div class="navigation">
                <?php
                get_prev_nav_with_title('<span id="post-previous">PREVIOUS POST</span>');
                get_next_nav_with_title('<span id="post-next">NEXT POST</span>');
                ?>
            </div>
        </section>
    </div>
<?php get_footer(); ?>