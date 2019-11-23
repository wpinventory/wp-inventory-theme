<?php
/**
 * @package Alpha Channel Group Base Theme
 * @author Alpha Channel Group (www.alphachannelgroup.com)
 */

get_header(); ?>
    <div class="contentwrapper">
        <section class="main-content blog archive">
            <article>
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="postwrapper">

                            <?php if (has_post_thumbnail()) { ?>

                                <div class="post_featured_image">
                                    <a href="<?php the_permalink() ?>" rel="bookmark"
                                       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                                </div>

                            <?php } ?>
                            <div <?php post_class() ?>>
                                <?php
                                $permalink = get_the_permalink();
                                if (WPIMTheme::get_option('stay_in_category')) {
                                    $permalink = add_query_arg('cat_specific', get_query_var('cat'), $permalink);
                                }
                                ?>
                                <h2 id="post-<?php the_ID(); ?>" class="blog-title"><a href="<?php echo $permalink; ?>"
                                                                                       rel="bookmark"
                                                                                       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="entry entry_<?php echo $archive_content; ?>">
                                    <?php
                                    WPIMTheme::the_content();
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                    WPIMTheme::navigation('archive');
                else:
                    if (is_category()) { // If this is a category archive
                        printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('', false));
                    } else if (is_date()) { // If this is a date archive
                        echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
                    } else if (is_author()) { // If this is a category archive
                        $userdata = get_userdatabylogin(get_query_var('author_name'));
                        printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
                    } else {
                        echo("<h2 class='center'>No posts found.</h2>");
                    }
                    require_once(TEMPLATEPATH . '/searchform.php');
                endif; ?>
            </article>
            <aside>
                <?php acg_get_sidebar("blog_sidebar", "", TRUE); ?>
            </aside>
        </section>
    </div>
<?php get_footer(); ?>