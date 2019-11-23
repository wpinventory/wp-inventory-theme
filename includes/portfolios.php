<?php

add_action( 'after_setup_theme', 'acg_portfolio_setup' );
add_action( 'init', 'acg_portfolio_create_type' );
add_action( 'init', 'acg_portfolio_taxonomy', 0 );
add_action( 'init', 'acg_portfolio_tags', 1 );
add_action( 'load-themes.php', 'acg_flush_rewrite_rules' );
//add_action( 'wp_enqueue_scripts', 'acg_portfolio_script' );

function acg_portfolio_setup() {
	add_theme_support( 'post-formats', array( 'gallery' ) );

	set_post_thumbnail_size( 150, 150, true ); // default square thumbnail
	add_image_size( 'horizontal', 403, 260, true ); // horizontal images
	add_image_size( 'vertical', 403, 490, true ); // vertical images
	add_image_size( 'square', 403, 403, true ); // vertical images
	add_image_size( 'fromblog', 138, 138, true ); // vertical images
	add_image_size( 'portfolio-page', 460, 300, true ); // vertical images
}

function acg_portfolio_create_type() {

    register_post_type('portfolio',
        array(
            'labels' => array(
                'name'                      => __('Portfolios', 'acg'),
                'singular_name'             => __('Portfolio', 'acg'),
                'add_new'                   => __('Add New', 'acg'),
                'add_new_item'              => __('Add Portfolio', 'acg'),
                'new_item'                  => __('Add Portfolio', 'acg'),
                'view_item'                 => __('View Portfolio', 'acg'),
                'search_items'              => __('Search Portfolio', 'acg'),
                'edit_item'                 => __('Edit Portfolio', 'acg'),
                'all_items'                 => __('All Portfolios', 'acg'),
                'not_found'                 => __('No Portfolios found', 'acg'),
                'not_found_in_trash'        => __('No Portfolios found in Trash', 'acg')
            ),
            'taxonomies'    => array('pcategory', 'ptag'),
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array( 'slug' => 'portfolio', 'with_front' => false ),
            'query_var' => true,
            'supports' => array('title','revisions','thumbnail','author','editor','post-formats'),
            'menu_position' => 5,
            'menu_icon' => get_template_directory_uri() .'/images/admin/portfolio.png',
            'has_archive' => true
        )
    );
}

function acg_portfolio_taxonomy() {
    // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' 		=> _x( 'Categories', 'taxonomy general name', 'acg'),
    'singular_name' 	=> _x( 'Category', 'taxonomy singular name', 'acg'),
    'search_items' 	=>  __( 'Search Categories', 'acg'),
    'all_items' 	=> __( 'All Categories', 'acg'),
    'parent_item' 	=> __( 'Parent Category', 'acg'),
    'parent_item_colon' => __( 'Parent Category:', 'acg'),
    'edit_item' 	=> __( 'Edit Category', 'acg'),
    'update_item' 	=> __( 'Update Category', 'acg'),
    'add_new_item' 	=> __( 'Add New Category', 'acg'),
    'new_item_name' 	=> __( 'New Category Name', 'acg'),
    'menu_name' 	=> __( 'Categories', 'acg')
  );
  
    register_taxonomy('pcategory','portfolio',array(
                'hierarchical' => true,
                'labels' => $labels,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'pcategory' )
    ));
}

function acg_portfolio_tags() {
    register_taxonomy( 'ptag', 'portfolio', array(
                'hierarchical' => false,
                'update_count_callback' => '_update_post_term_count',
                'label' => __('Tags', 'acg'),
                'query_var' => true,
                'rewrite' => array( 'slug' => 'ptags' )
    )) ;
}

function acg_flush_rewrite_rules() {
    global $pagenow, $wp_rewrite;

    if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
        $wp_rewrite->flush_rules();
}

//  Recent Posts With Featured Image
class acg_recent_portfolio_featured_image_author extends WP_Widget {
	function acg_recent_portfolio_featured_image_author() {
		parent::__construct('acg_recent_portfolio_featured_image_author', '- Recent Portfolio Super Widget', array('description'=>'Recent portfolio widget with a huge amount of flexibility to cover virtually any need for displaying recent posts.'));
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = $instance["title"];
		$image = (isset($instance["image"])) ? ($instance["image"]): '';
		$count= (isset($instance["count"])) ? ($instance["count"] * 1): 5;
		$img_loc = $instance["img_loc"];
		$title_loc = $instance["title_loc"];
		$author = (isset($instance["author"])) ? ($instance['author']): '';
		$author_intro = $instance["author_intro"];
		$date = (isset($instance["date"])) ? ($instance['date']): '';
		$date_intro = $instance["date_intro"];
		$date_format = $instance["date_format"];
		$excerpt = (isset($instance["excerpt"])) ? ($instance['excerpt']): '';
		$learn_more = (isset($instance["learn_more"])) ? ($instance['learn_more']): '';
		$category_id = (int)$instance["category_id"];
		$page_id = (int)$instance["page_id"];
		$category_link_loc = (isset($instance["category_link_loc"])) ? $instance['category_link_loc']: '';
		$category_link = (isset($instance["category_link"])) ? ($instance['category_link']): '';
		
		$category_url = ($category_id) ? get_category_link($category_id) : get_permalink($page_id);
		$category_link = ($category_link) ? '<a class="view_all_link" href="' . $category_url . '">' . $category_link . '</a>' . PHP_EOL : '';
		$widget_title = PHP_EOL . $before_title . '<span>' . $title . '</span>' . $after_title . PHP_EOL;
		
		$placeholder_image = '<img src="' . $image . ' " class="wp-post-image alignleft" title="' . $image . '" alt="' . $title . '"/>' . PHP_EOL;
		
		echo PHP_EOL . $before_widget;
		
		$catq = ($category_id) ? '&cat=' . $category_id : '';
		$wp = new WP_Query("post_type=portfolio&posts_per_page=" . $count . $catq);
		
		if ($category_link && ($category_link_loc == 'top' || $category_link_loc == 'both')) {
			echo $category_link;
		}
		
		echo ($title_loc && ($title_loc == 'top')) ?  $widget_title : '';

		if ($wp->have_posts()) {
			echo '<ul>' . "\r\n";
			while ($wp->have_posts()) {
				$wp->the_post();
				$post_title = '<a class="title" href="' . get_permalink() . '">' . get_the_title() . '</a>' . "\r\n";
				
				echo '<li class="widget_post_content">';
				// Image loc == '' when it is to be surpressed
				if ($img_loc) {
					echo '<div class="image">';
					echo '<a href="' . get_permalink() . '">';
	
					if ( ! $image == '') {
						if ($img_loc == "first") {
							echo $placeholder_image;
						}
						echo ($title && ($title_loc == 'post')) ?  $widget_title : '';
						echo $post_title;
						if ($img_loc == "second") {
							echo $placeholder_image;
						}
					} else {
						if ($img_loc == "first") {
							the_post_thumbnail(array(230, 154));
						}
						echo '</a>';
						echo '</div>';
						echo ($title && ($title_loc == 'post')) ?  "\r\n" . $before_title . $title . $after_title . "\r\n" : '';
						echo $post_title;
						if ($img_loc == "second") {
							the_post_thumbnail(array(230, 154));
						}
					}
				} else {
					echo $post_title;
				}
				
				echo '<div class="widget_entry">';

				echo ($author || $date) ? '<p class="date">' . PHP_EOL : '';
				if ($author) {
					$author_intro = ($author_intro) ? '<span class="intro">' . $author_intro . '</span>' : '';
					echo '<span class="author">' . $author_intro . get_the_author() . '</span>';
				}
				if ($date) {
					$date_intro = ($date_intro) ? '<span class="intro">' . $date_intro . '</span>' : '';
					echo '<span class="date">' . $date_intro . get_the_date($date_format) . '</span>';
				}
				echo ($author || $date) ? '</p>' . PHP_EOL : '';
				if ($excerpt) {
					the_excerpt();
				}
				if ($learn_more) {
					echo '<div><a class="learn_more" href="' . get_permalink() . '">' . $learn_more . '</a></div>';
				}
				if ($img_loc == "last") {
					the_post_thumbnail(array(203, 136));
				}

				echo '</div></li>';
			}
			echo '</ul>' . "\r\n";
		}
		
		if ($category_link && ($category_link_loc == 'bottom' || $category_link_loc == 'both')) {
			echo $category_link;
		}

		echo $after_widget . "\r\n";
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance["title"]));
		$instance['image'] = strip_tags(stripslashes($new_instance["image"]));
		$instance['count'] = strip_tags(stripslashes($new_instance["count"]));
		$instance['category_id'] = strip_tags(stripslashes($new_instance["category_id"]));
		$instance['page_id'] = strip_tags(stripslashes($new_instance["page_id"]));
		$instance['img_loc'] = strip_tags(stripslashes($new_instance["img_loc"]));
		$instance['title_loc'] = strip_tags(stripslashes($new_instance["title_loc"]));
		$instance['author'] = (isset($new_instance["author"])) ? 1 : 0;
		$instance['author_intro'] = $new_instance["author_intro"];
		$instance['date'] = (isset($new_instance["date"])) ? 1 : 0;
		$instance['date_intro'] = $new_instance["date_intro"];
		$instance['date_format'] = $new_instance["date_format"];
		$instance['excerpt'] = (isset($new_instance["excerpt"])) ? 1 : 0;
		$instance['learn_more'] = $new_instance["learn_more"];
		$instance['category_link'] = $new_instance["category_link"];
		$instance['category_link_loc'] = $new_instance["category_link_loc"];
		return $instance;
	}

	function form($instance) {
		$default = array(
			'title' 		=> 'Recent Posts', 
			'image' 		=> '', 
			'count'			=> '5', 
			'category_id'		=> '', 
			'page_id'		=> '',
			'img_loc'		=> 'second', 
			'title_loc' 		=> 'top', 
			'author'		=> '',
			'author_intro'		=> 'by',
			'date'			=> '',
			'date_intro'		=> 'posted on',
			'date_format'		=> 'm/d/Y',
			'excerpt'		=> '',
			'learn_more'		=> 'Learn More',
			'category_link'		=> 'View All',
			'category_link_loc' 	=> ''
		);
		
		$instance = wp_parse_args( (array) $instance, $default);
		$title = esc_attr( $instance['title'] );
		$image = esc_attr( $instance['image'] );
		$count = esc_attr( $instance['count'] );
		$category_id = esc_attr( $instance['category_id'] );
		$page_id = esc_attr( $instance['page_id'] );
		$learn_more = esc_attr( $instance['learn_more'] );
		$category_link = esc_attr( $instance['category_link'] );
		$category_link_loc = esc_attr( $instance['category_link_loc'] );
		$img_loc = esc_attr($instance["img_loc"]);
		$title_loc = esc_attr($instance["title_loc"]);
		$author = esc_attr( $instance["author"]);
		$author_intro = esc_attr( $instance["author_intro"]);
		$date_intro = esc_attr( $instance["date_intro"]);
		$date_format = esc_attr( $instance["date_format"]);
		
		$catlist = wp_dropdown_categories(array('echo'=> 0, 'name'=>$this->get_field_name('category_id'), 'selected'=>$category_id, 'hierarchical'=>1, 'show_option_all'=>'- All Categories -'));
		
		$locs = array(""=>" - No Image -", "first"=>"First (before post title)", "second"=>"Second (after post title)", "last"=>"Last");
		$loclist = '<select name="' . $this->get_field_name("img_loc") . '">';
		
		foreach ($locs as $val=>$loc) {
			$loclist.= '<option value="' . $val . '"';
			$loclist.= ($val == $img_loc) ? ' selected="selected"' : '';
			$loclist.= '>' . $loc . '</option>';
		}
		$loclist.= '</select>';
		
		$locs = array("top"=>"Top (before posts)", "post"=>"In Post");
		$titlelist = '<select name="' . $this->get_field_name("title_loc") . '">';
		foreach ($locs as $val=>$loc) {
			$titlelist.= '<option value="' . $val . '"';
			$titlelist.= ($val == $title_loc) ? ' selected="selected"' : '';
			$titlelist.= '>' . $loc . '</option>';
		}
		$titlelist.= '</select>';
		
		$locs = array(""=>"- None -", "top"=>"Top", "bottom"=>"Bottom", "both"=>"Both");
		$catloclist = '<select name="' . $this->get_field_name("category_link_loc") . '">';
		foreach ($locs as $val=>$loc) {
			$catloclist.= '<option value="' . $val . '"';
			$catloclist.= ($val == $category_link_loc) ? ' selected="selected"' : '';
			$catloclist.= '>' . $loc . '</option>';
		}
		$catloclist.= '</select>';
		
		$formats = array(
			'm/d/Y'			=> '08/14/2013',
			'm/d/Y \a\t g:ia'	=> '08/14/2013 at 1:25pm',
			'm/d/Y \a\t H:i'	=> '08/14/2013 at 13:25',
			'Y-m-d'			=> '2013-08-14',
			'Y-m-d \a\t g:ia'	=> '2013-08-14 at 1:25pm',
			'Y-m-d \a\t H:i'	=> '2013-08-14 at 13:25',
			'M jS, Y'		=> 'Aug 14th, 2013',
			'F jS, Y'		=> 'August 14th, 2013',
			'F jS, Y \a\t g:ia'	=> 'August 14th, 2013 at 1:25pm',
			'F jS, Y \a\t H:i'	=> 'August 14th, 2013 at 13:25',
			'D, M jS, Y'		=> 'Thu, Aug 14th, 2013',
			'D, M jS, Y \a\t g:ia'	=> 'Thu, Aug 14th, 2013 at 1:25pm',
			'D, M jS, Y \a\t H:i'	=> 'Thu, Aug 14th, 2013 at 13:25',
			'l, M jS, Y'		=> 'Thursday, Aug 14th, 2013',
			'l, M jS, Y \a\t g:ia'	=> 'Thursday, Aug 14th, 2013 at 1:25pm',
			'l, M jS, Y \a\t H:i'	=> 'Thursday, Aug 14th, 2013 at 13:25'
		);
		
		$dateformatlist = '<select style="width: 175px;font-size: 8pt;" name="' . $this->get_field_name("date_format") . '">';
		foreach ($formats as $val=>$format) {
			$dateformatlist.= '<option value="' . $val . '"';
			$dateformatlist.= ($val == $date_format) ? ' selected="selected"' : '';
			$dateformatlist.= '>' . $format . '</option>';
		}
		$dateformatlist.= '</select>';
		
		$page_id_selector = 'p-' . $this->get_field_id('page_id');
		$category_identifier = 'select[name="' . str_replace('[', '\\[', str_replace(']', '\\]', $this->get_field_name('category_id'))) . '"]';
	?>	
	<p><label for="<?php echo $this->get_field_id('title_loc'); ?>">Widget Title Location </label><?php echo $titlelist; ?></p>
	<p><label for="<?php echo $this->get_field_name("title"); ?>">Widget Title: <input type="text" class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" value="<?php echo $title; ?>" /></label></p>
	
	<legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">Recent Posts</legend>
        <p><label for="<?php echo $this->get_field_id('count'); ?>">Number of Posts: </label><input size="3" type="text" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $count; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('category'); ?>">Category: </label><?php echo $catlist; ?></p>

        <legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">View All Link</legend>
        <p id="<?php echo $page_id_selector; ?>"><label for="">View All Links To</label><?php wp_dropdown_pages('name=' . $this->get_field_name('page_id') . '&selected=' . $page_id); ?></p>
        <p><label for="<?php echo $this->get_field_id('category_link_loc'); ?>">Location: </label><?php echo $catloclist; ?></p>
        <p><label for="<?php echo $this->get_field_id('category_link'); ?>">Text: </label><input type="text" name="<?php echo $this->get_field_name('category_link'); ?>" value="<?php echo $category_link; ?>" /></p>
        
        <legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">Image</legend>
        <p><label for="<?php echo $this->get_field_id('img_loc'); ?>">Location: </label><?php echo $loclist; ?></p>
        <p><label for="<?php echo $this->get_field_name("image"); ?>">Static Image URL<br /><span style="color: #888;font-size: 8pt;">(overrides featured product image)</span>:<input type="text" class="widefat" id="<?php echo $this->get_field_id("image"); ?>" name="<?php echo $this->get_field_name("image"); ?>" value="<?php echo $image; ?>" /></label></p>
        
        <legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">Author</legend>
        <p><input class="checkbox" type="checkbox" <?php checked($instance['author'], true) ?> id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" />
                <label for="<?php echo $this->get_field_id('author'); ?>">Display Author</label></p>
	<p><label for="<?php echo $this->get_field_id('author_intro'); ?>">Author Intro Text</label><input class="widefat" type="text" name="<?php echo $this->get_field_name('author_intro'); ?>" value="<?php echo $author_intro; ?>" /></p>
        
        <legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">Date</legend>
        <p><input class="checkbox" type="checkbox" <?php checked($instance['date'], true) ?> id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" />
        <label for="<?php echo $this->get_field_id('date'); ?>">Display Date</label></p>
        <p><label for="<?php echo $this->get_field_id('date_intro'); ?>">Intro Text</label><input class="widefat" type="text" name="<?php echo $this->get_field_name('date_intro'); ?>" value="<?php echo $date_intro; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('date_format'); ?>">Format: </label><?php echo $dateformatlist; ?></p>
        
        <legend style="font-weight:bold; color: #555; border-bottom: 1px solid #555;">Excerpt</legend>
        <p><input class="checkbox" type="checkbox" <?php checked($instance['excerpt'], true) ?> id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" />
                <label for="<?php echo $this->get_field_id('excerpt'); ?>">Display Excerpt</label></p>
                
        <p><label for="<?php echo $this->get_field_id('category_link'); ?>">Link Text: </label><input type="text" name="<?php echo $this->get_field_name('learn_more'); ?>" value="<?php echo $learn_more; ?>" /></p>
        <script>
        	jQuery('<?php echo $category_identifier; ?>').change(function() {toggleACGSWPageId();});
        	jQuery(function() {
        		toggleACGSWPageId();
        	});
        	
        	function toggleACGSWPageId() {
        		var pageid = jQuery('p#<?php echo $page_id_selector; ?>');
        		if (jQuery('<?php echo $category_identifier; ?>').val() == '0') {
        			pageid.slideDown();
        		} else {
        			pageid.slideUp();
        		}
        	}
        </script>
    <?php }
}

function acg_post_thumbnail($size=NULL) {
    global $post;
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), acg_image_orientation() );
    $large = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
    $title = get_the_title( get_post_thumbnail_id($post->ID) );

    echo '<img src="' . $thumb[0] . '" title="' . $title . '" data-image_large="' .  $large[0] . '" data-image_thumb="' .  $thumb[0] . '" class="aligncenter" />';
}

function acg_portfolio_script() {
//	wp_enqueue_script('jquery');
//	wp_enqueue_script('jquery-ui-core');
//	wp_register_script('acg_portfolio_slideshow_script', TEMPLATE_URL . '/js/jquery.portfolio.js');
//	wp_enqueue_script('acg_portfolio_slideshow_script');
}

register_widget('acg_recent_portfolio_featured_image_author');