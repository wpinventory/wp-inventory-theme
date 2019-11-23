<?php
/**
 * @package Alpha Channel Group Base Theme
 * @author Alpha Channel Group (www.alphachannelgroup.com)
 */
/*
 * Template Name: Blog
 */

$blog_title = WPIMTheme::get_option( 'blog_page_title' );
if ( $blog_title ) {
	$blog_title = '<h1 class="blogtitle">' . $blog_title . '</h1>';
}

get_header(); ?>

    <div class="contentwrapper blog_archive archive">
        <section class="site_center main_content blog">
			<?php echo $blog_title; ?>
            <article class="blog">
				<?php
				global $more;
				$more = 0;
				$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
				query_posts( 'post_type=post&paged=' . $paged );
				while ( have_posts() ) : the_post(); ?>

                    <div class="postwrapper">

						<?php if ( has_post_thumbnail() ) { ?>

                            <div class="post_featured_image">
                                <a href="<?php the_permalink() ?>" rel="bookmark"
                                   title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail( array(480, 320) ); ?></a>
                            </div>

						<?php } ?>

                        <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
                            <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark"
                                                       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="postmeta">
                                <p><em>Posted on</em> <time datetime="<?php the_time('HH:MM:SS') ?>" pubdate><?php the_time('l, F jS, Y') ?></time> by <a href="https://plus.google.com/u/0/104905181784587333262/?rel=author" target="_blank"><?php the_author(); ?></a></p>
                            </div>
                        </div>
                    </div>

				<?php endwhile;
				WPIMTheme::navigation( 'blog' );
				?>
            </article>
            <aside>
                <?php acg_get_sidebar('blog_sidebar', '', TRUE); ?>
            </aside>
        </section>
    </div>
<?php get_footer(); ?>