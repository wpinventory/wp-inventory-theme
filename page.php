<?php get_header(); ?>
	<div class="contentwrapper">
		<section class="main_content page">
			<article>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<div class="post" id="post-<?php the_ID(); ?>">
						<?php the_content( '<p class="serif">Read the rest of this page &raquo;</p>' ); ?>
					</div>
					<?php
					edit_post_link( 'Edit Page', '<div class="edit_link">', '</div>' );
					WPIMTheme::comments();
				endwhile; endif; ?>
			</article>
		</section>
	</div>
<?php get_footer(); ?>