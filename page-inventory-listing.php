<?php
/**
 * @package WordPress
 */
/*
 * Template Name: Inventory Listing
 */
get_header();

/**
 * TODO: [] - Move the below if checks into separate functions
 * TODO: [] - Last thing to deal with is multiple shortcodes on the listing page and only showing one item detail on the detail page (not for all instances of the listing page shortcode)
 * TODO: [] - My SSL notice I tapped into the wordpress notices hook isn't permanently dismissible - make it so #1
 */

/**
 * Have a look at the body class and see if this page load is a single view
 */
$classes           = get_body_class();
$wpim_details_page = FALSE;
if ( in_array( 'wpinventory-single', $classes ) ) {
	$wpim_details_page = TRUE;
}

/**
 * If the sidebar should be visible on the detail page or not
 */
$show_on_detail = TRUE;
if ( ! (int) get_post_meta( $post->ID, 'wpim_listing_page_sidebar_on_detail', TRUE ) ) {
	$show_on_detail = FALSE;
}

/**
 * Hide content above or below the shortcode on the details page
 */
$hide_content = FALSE;
if ( (int) get_post_meta( $post->ID, 'wpim_listing_page_hide_content_on_detail', TRUE ) ) {
	$hide_content = TRUE;
}

/**
 * Sidebar defaults
 */
$sidebar  = FALSE;
$position = get_post_meta( $post->ID, 'wpim_listing_page_sidebar_position', TRUE );
$position = ( $position ) ? $position : 'right';

/**
 * Build the appropriate CSS class based on the chosen position of the sidebar
 */
if ( 'left' === $position ) {
	$position_class = ' sbar_left';
} elseif ( 'right' === $position ) {
	$position_class = ' sbar_right';
} else {
	$position_class = '';
}

/**
 * Begin building the main wrapper classes
 */
$class = '';
if ( (int) get_post_meta( $post->ID, 'wpim_listing_page_sidebar', TRUE ) ) {
	$sidebar = TRUE;
	$class   = ' has_sidebar' . $position_class;
}

/**
 * Real quick interception if the details page is the page
 */
if ( $wpim_details_page && ! $show_on_detail ) {
	$class = '';
} elseif ( $wpim_details_page && $show_on_detail ) {
	$class = ' has_sidebar' . $position_class;
}

?>
    <div class="contentwrapper">
        <section class="main_content wpinventory_listing<?php echo $class; ?>">
			<?php if ( ( $sidebar && ! $wpim_details_page ) || ( $wpim_details_page && $show_on_detail ) ) { ?>
                <span class="sidebar_flyout">
                <span class="bar_one"></span>
                <span class="bar_two"></span>
                <span class="bar_three"></span>
            </span>
			<?php } ?>
            <article>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <div class="post" id="post-<?php the_ID(); ?>">
						<?php
						if ( $wpim_details_page && $hide_content ) {
							echo do_shortcode( '[wpinventory]' );
						} else {
							the_content( '<p class="serif">Read the rest of this page &raquo;</p>' );
						}
						?>
                    </div>
				<?php endwhile; endif; ?>
            </article>
			<?php
			if ( ( $sidebar && ! $wpim_details_page ) || ( $wpim_details_page && $show_on_detail ) ) { ?>
                <aside>
					<?php acg_get_sidebar( 'wpim_inventory', '', TRUE ); ?>
                </aside>
			<?php }
			?>
        </section>
    </div>
<?php get_footer(); ?>