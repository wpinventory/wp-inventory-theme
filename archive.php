<?php
/**
 * @package Alpha Channel Group Base Theme
 * @author  Alpha Channel Group (www.alphachannelgroup.com)
 */

get_header();
global $post;
$page_heading       = get_post_meta( $post->ID, 'page_heading', TRUE );
$featured_img       = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full', FALSE, '' );
$featured_img_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium', FALSE, '' );

$default_img = ACGTheme::get_option( 'inner_default_featured' );

if ( has_post_thumbnail() ) {
	?>
    <div class="featuredwrapper"
         style="background: url('<?php echo $featured_img[0]; ?>') bottom center; background-size: cover;">
		<?php if ( $page_heading ) {
			echo '<div class="page_heading">' . $page_heading . '</div>';
		}
		?>
    </div>
	<?php
} else {
	?>
    <div class="featuredwrapper"
         style="background: url('<?php echo $default_img; ?>') bottom center; background-size: cover;">
		<?php if ( $page_heading ) {
			echo '<div class="page_heading">' . $page_heading . '</div>';
		}
		?>
    </div>
	<?php
}
?>
    <div class="contentwrapper">
        <section class="main-content blog archive">
            <article>
				<?php if ( have_posts() ) : ?>
                    <h1>
						<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
						<?php /* If this is a category archive */
						if ( is_category() ) { ?>
							<?php single_cat_title(); ?>
							<?php /* If this is a tag archive */
						} elseif ( is_tag() ) { ?>
                            Stuff in the &#8216;<?php single_tag_title(); ?>&#8217; Category
							<?php /* If this is a daily archive */
						} elseif ( is_day() ) { ?>
                            Archive for <?php the_time( 'F jS, Y' ); ?>
							<?php /* If this is a monthly archive */
						} elseif ( is_month() ) { ?>
                            Archive for <?php the_time( 'F, Y' ); ?>
							<?php /* If this is a yearly archive */
						} elseif ( is_year() ) { ?>
                            Archive for <?php the_time( 'Y' ); ?>
							<?php /* If this is an author archive */
						} elseif ( is_author() ) { ?>
                            Author Archive
							<?php /* If this is a paged archive */
						} elseif ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) { ?>
                            Blog Archives
						<?php } ?>
                    </h1>
					<?php while ( have_posts() ) : the_post(); ?>
                        <div class="postwrapper">

							<?php if ( has_post_thumbnail() ) { ?>

                                <div class="post_featured_image">
                                    <a href="<?php the_permalink() ?>" rel="bookmark"
                                       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                                </div>

							<?php } ?>
                            <div <?php post_class() ?>>
								<?php
								$permalink = get_the_permalink();
								if ( ACGTheme::get_option( 'stay_in_category' ) ) {
									$permalink = add_query_arg( 'cat_specific', get_query_var( 'cat' ), $permalink );
								}
								?>
                                <h2 id="post-<?php the_ID(); ?>" class="blog-title"><a href="<?php echo $permalink; ?>"
                                                                                       rel="bookmark"
                                                                                       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="entry entry_<?php echo $archive_content; ?>">
									<?php
									ACGTheme::the_content();
									?>
                                </div>
                            </div>
                        </div>
					<?php endwhile;
					ACGTheme::navigation( 'archive' );
				else:
					if ( is_category() ) { // If this is a category archive
						printf( "<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title( '', FALSE ) );
					} else if ( is_date() ) { // If this is a date archive
						echo( "<h2>Sorry, but there aren't any posts with this date.</h2>" );
					} else if ( is_author() ) { // If this is a category archive
						$userdata = get_userdatabylogin( get_query_var( 'author_name' ) );
						printf( "<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name );
					} else {
						echo( "<h2 class='center'>No posts found.</h2>" );
					}
					require_once( TEMPLATEPATH . '/searchform.php' );
				endif; ?>
            </article>
            <aside>
				<?php acg_get_sidebar( "blog_sidebar", "", TRUE ); ?>
            </aside>
        </section>
    </div>
<?php get_footer(); ?>