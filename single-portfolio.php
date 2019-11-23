<?php
/**
 * The Template for displaying a single portfolio post
 *
 */
get_header();
// $image_full = the_post_thumbnail_url( 'full' );
$page_heading       = get_post_meta( $post->ID, 'page_heading', TRUE );
$featured_img       = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full', FALSE, '' );
$featured_img_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium', FALSE, '' );
//var_dump($src);

if ( has_post_thumbnail() ) {
	?>
    <div class="featuredwrapper" style="background: url('<?php echo $featured_img_thumb[0]; ?>') top center;">
        <div id="featured_pixelated">
            <div class="page_heading img_container">
                <img id="top_banner_image" src="<?php echo $featured_img[0]; ?>">
                <span><?php echo $page_heading; ?></span>
            </div>
        </div>
    </div>
	<?php
} else {
	echo '';
}
?>
    <div id="contentwrapper">
        <section class="main_content blogcontent no_sidebar single portfolio portfolio-<?php echo WPIMTheme::get_option( 'portfolios_type' ); ?>">
			<?php if ( have_posts() ) {
				while ( have_posts() ) {
					the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <section class="blog_header">
                            <h2 class="entry-title"><?php the_title(); ?></h2>
                        </section>

						<?php
						$format = WPIMTheme::get_option( 'portfolios_type' );
						if ( 'gallery' == get_post_format() ) { ?>
                            <div class="post-format-content">
								<?php
								$content = get_the_content();

								preg_match( '/\[gallery(.*?)]/', $content, $matches );
								if ( empty( $matches ) ) {
									$args        = array(
										'post_type'      => 'attachment',
										'numberposts'    => - 1,
										'post_status'    => NULL,
										'post_parent'    => $post->ID,
										//'post__not_in'	=> array($thumb_id),
										'post_mime_type' => 'image',
										'orderby'        => 'menu_order',
										'order'          => 'ASC'
									);
									$attachments = get_posts( $args );
									if ( ! empty( $attachments ) ) {
										$thumbs = '';
										?>
                                        <div id="gallery-<?php the_ID(); ?>" class="gallery acg_<?php echo $format; ?>">
                                            <ul class="slides">
												<?php foreach ( $attachments as $attachment ) {
													$_post       = &get_post( $attachment->ID );
													$url         = wp_get_attachment_url( $_post->ID );
													$post_title  = esc_attr( $_post->post_title );
													$large_image = wp_get_attachment_image( $attachment->ID, 'large' );
													$thumb       = wp_get_attachment_image( $attachment->ID, 'thumbnail' );
													$caption     = get_post_field( 'post_excerpt', $attachment->ID );
													$thumbs      .= '<li class="slide-page"><a href="javascript:void(0);">' . $thumb . '</a></li>' . PHP_EOL;
													?>

                                                    <li class="item">
														<?php echo '<a href="' . $url . '" title="' . $post_title . '"></a>'; ?>
														<?php echo $large_image; ?>
														<?php if ( $caption ) {
															echo '<p class="flex-caption">' . $caption . '</p>';
														} ?>
                                                    </li>
												<?php } ?>
                                            </ul>
											<?php if ( $format == 'slideshow' ) { ?>
                                                <ul class="slide_controls">
                                                    <li class="slide-prev"><a class="prev" href="javascript:void(0);">PREV</a></li>
													<?php echo $thumbs; ?>
                                                    <li class="slide-next"><a class="next" href="javascript:void(0);">NEXT</a></li>
                                                </ul>
											<?php } ?>
                                        </div>
									<?php } ?>
								<?php } else {
									$content = apply_filters( 'the_content', get_the_content() );
									$content = str_ireplace( 'flexslider', $format, $content );
									preg_match( '/\[gallery.*ids=.(.*).\]/', get_the_content(), $ids );
									$ids     = explode( ",", $ids[1] );
									$content = str_replace( '<li>', '<li class="item">', $content );

									$start = stripos( $content, "<ul class='slides'" );
									if ( $start !== FALSE && $format == 'slideshow' ) {
										foreach ( (array) $ids as $id ) {
											$thumb  = wp_get_attachment_image( trim( $id ), 'thumbnail' );
											$thumbs .= '<li class="slide-page"><a href="javascript:void(0);">' . $thumb . '</a></li>' . PHP_EOL;
										}

										$end = stripos( $content, '</ul>', $start );
										$nav = '<ul class="slide_controls">' . PHP_EOL;
										//$nav.= '<li class="slide-prev"><a class="prev" href="javascript:void(0);">PREV</a></li>' . PHP_EOL;
										$nav .= '<li><a class="play-pause" href="javascript:void(0);">X</a></li>' . PHP_EOL;
										$nav .= $thumbs;
										//$nav.= '<li class="slide-next"><a class="next" href="javascript:void(0);">NEXT</a></li>' . PHP_EOL;
										$nav     .= '</ul>' . PHP_EOL;
										$nav     .= '<ul class="slide_control">' . PHP_EOL;
										$nav     .= '<li class="slide-prev"><a class="prev" href="javascript:void(0);">PREV</a></li>' . PHP_EOL;
										$nav     .= '<li class="slide-next"><a class="next" href="javascript:void(0);">NEXT</a></li>' . PHP_EOL;
										$nav     .= '</ul>';
										$content = substr( $content, 0, $end + 5 ) . $nav . substr( $content, $end + 5 );
									} else {
										$full_size = array();
										foreach ( (array) $ids AS $id ) {
											$full_size[] = wp_get_attachment_image( trim( $id ), 'large' );
										}
										echo '<script>var galleryImages = ' . json_encode( $full_size ) . ';</script>';
									}

									echo $content;

									define( 'GALLERY_IMAGE_WIDTH', WPIMTheme::get_option( 'portfolio_image_width' ) );
								}
								?>
                            </div>
						<?php } else { ?>
							<?php $thumbid = get_post_thumbnail_id( $post->ID );
							$img           = wp_get_attachment_image_src( $thumbid, 'full' );
							$img['title']  = get_the_title( $thumbid ); ?>
                            <a href="<?php echo $img[0]; ?>" class="thumb" title="<?php echo $img['title']; ?>">
								<?php the_post_thumbnail(); ?>
                            </a>
						<?php } ?>

                    </article><!-- #post-<?php the_ID(); ?> -->
				<?php }
			} else { ?>
                <p>Sorry, no posts matched your criteria.</p>
			<?php } ?>
        </section>
    </div>
    <script>var galleryFormat = '<?php echo $format; ?>';</script>
<?php get_footer(); ?>