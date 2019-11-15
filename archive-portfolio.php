<?php
/**
 * @package WordPress
 */

get_header(); ?>
            <div id="contentwrapper">
                <section class="pagecontent blogcontent archive">
                <?php if (have_posts()) {
                    $post = $posts[0]; ?>
                <?php } ?>
                    <?php if (have_posts()) { ?>
                        <article>
                        <div class="portfolio_wrapper">
                        <h1 class="mainheading">Portfolio</h1>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php if ( 'portfolio' == get_post_type() ) {?>
                                    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h2>
                                        <div class="portfolio-image-wrap drop-shadow lifted">
                                            <?php if ( has_post_thumbnail() ) { ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail(array(230,154)); ?>
                                                </a>
                                                    <?php } else { ?>
                                                <a href="<?php the_permalink(); ?>"><?php echo '<img src="'.get_stylesheet_directory_uri().'/images/no-portfolio-archive.gif" class="wp-post-image"/>'; ?></a>
                                            <?php } ?>
                                        </div>
                                    </div><!-- #post-<?php the_ID(); ?> -->
                                <?php } else { ?>
                                    <div <?php post_class() ?>>
                                        <small class="date"><?php the_time('m/y') ?></small>
                                        <h2 id="post-<?php the_ID(); ?>" class="blog-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                                        <div class="entry">
                                            <?php the_content() ?>
                                        </div>
                                        <p class="postmetadata">Categories: <?php the_category(', ') ?><br>
                                                        <?php the_tags('Tags: ', ', ', ''); ?></p>
                                    </div>
                                <?php } ?>
                            <?php endwhile; ?>

                            <div class="navigation">
                                <div class="alignleft"><?php next_posts_link('&larr; Older Entries') ?></div>
                                <div class="alignright"><?php previous_posts_link('Newer Entries &rarr;') ?></div>
                            </div>
                        <?php } else {

                            if ( is_category() ) { // If this is a category archive
                                printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
                            } else if ( is_date() ) { // If this is a date archive
                                echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
                            } else if ( is_author() ) { // If this is a category archive
                                $userdata = get_userdatabylogin(get_query_var('author_name'));
                                printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
                            } else {
                                echo("<h2 class='center'>No posts found.</h2>");
                            }
                            get_search_form();
                        }
                    ?>
                        </div>
                        </article>
                </section>
            </div>
<?php get_footer(); ?>
