<?php

// Unregister some of the default WordPress widgets
function wpim_core_unregister_widgets() {
	if ( function_exists( "unregister_widget" ) ) {
		unregister_widget( 'WP_Widget_Recent_Posts' );
	}
}

function wpim_core_remove_dashboard_widgets() {
	// Globalize the metaboxes array, this holds all the widgets for wp-admin
	global $wp_meta_boxes;
	// Remove desired widgets
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
	// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	// unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);

	// Then unset the side and primary
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
}

// Create the function to output the contents of our Dashboard Widget
function wpim_core_hotlink_widget() {
	global $wpdb, $table_prefix;
	$table = $table_prefix . "posts";

	$url = $_SERVER["SERVER_NAME"];
	echo "<p><em>Current domain: " . $url . "</em></p>";
	$query   = "SELECT ID, post_title, post_content FROM " . $table . " WHERE post_content like '%src=%' AND post_status = 'publish' ORDER BY post_title";
	$results = $wpdb->get_results( $query );
	$list    = $lasttitle = "";
	foreach ( $results as $row ) {
		$content = $row->post_content;
		$pos     = stripos( $content, 'src=' );
		if ( $pos !== FALSE ) {
			$pos     = $pos + 4;
			$delim   = substr( $content, $pos, 1 );
			$end     = stripos( $content, $delim, $pos + 2 );
			$src     = substr( $content, $pos + 1, $end - $pos - 1 );
			$fullimg = $src;
			$src     = str_replace( "http://", "", $src );
			$src     = strtolower( substr( $src, 0, strlen( $url ) ) );
			if ( $src != strtolower( $url ) ) {
				if ( $lasttitle != $row->post_title ) {
					$list .= ( $list ) ? "</p>" : '';
					$list .= '<p><a href="' . get_permalink( $row->ID ) . '">' . $row->post_title . '</a>';
				}
				$list      .= '<br />(' . $fullimg . ')';
				$lasttitle = $row->post_title;
			}
		}
	}
	echo '<p><strong>Checking status of image sources...</strong></p>';
	if ( $list ) {
		echo '<p><strong>The following posts have images that are loaded from another domain:</strong></p>';
		echo $list . '</p>';
	} else {
		echo '<p>Woohoo! Everything is healthy!</p>';
	}
}

// Create the function use in the action hook
function wpim_core_dashboard_widgets() {
	wp_add_dashboard_widget( 'wpim_dashboard_widget', 'WordPress Theme: ' . ACG_THEME_NAME, 'wpim_dashboard_widget' );
	wp_add_dashboard_widget( 'wpim_core_hotlink_widget', 'Migration Monitor', 'wpim_core_hotlink_widget' );

	// Forcing it to the top...
	// Globalize the metaboxes array, this holds all the widgets for wp-admin
	global $wp_meta_boxes;

	// Get the regular dashboard widgets array
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

	// Backup and delete our new dashbaord widget from the end of the array
	$acg_widget_backup = array(
		'wpim_dashboard_widget'    => $normal_dashboard['wpim_dashboard_widget'],
		'wpim_core_hotlink_widget' => $normal_dashboard['wpim_core_hotlink_widget']
	);
	unset( $normal_dashboard['wpim_dashboard_widget'] );
	unset( $normal_dashboard['wpim_core_hotlink_widget'] );
	// Merge the two arrays together so our widget is at the beginning
	$sorted_dashboard = array_merge( $acg_widget_backup, $normal_dashboard );
	// Save the sorted array back into the original metaboxes
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}

// Create the function to output the contents of our Dashboard Widget
function wpim_dashboard_widget() {
	// Display whatever it is you want to show
	echo '<img width="200" src="' . TEMPLATE_URL . '/images/logo.gif" alt="' . ACG_THEME_NAME . '">';
	echo '<p>Welcome to your WordPress Dashboard.</p>';
	echo '<p>This theme has the following built-in features:</p>';
	echo '<ul class="custom-features">';
	echo '<li>Display a <strong>Google Map</strong> on any page or post with a simple shorcode.  See <a href="admin.php?page=acg_admin_main">this page</a> for details.</li>';
	echo '<li><strong>Extended Editor Buttons</strong> in the Add/Edit Page and Add/Edit Post pages for additional control over your posts.</li>';
	echo '<li>The ability to have <strong>Separate Sidebar for each Page</strong>, giving you absolute control over each and every page.  When adding/editing a page, look in the <strong>lower-right</strong> area to select the sidebar to show for that page.</li>';
	echo '<li><strong>Custom Subnavigation</strong> widget to list your child pages.</li>';
	echo '<li><strong>Migration Monitor</strong> widget (elsewhere on this Dashboard) that ensures that all images are being loaded from your server rather than somewhere else.</li>';
	echo '</ul>';
}

// Remove unwanted widgets that don't play nice with this theme
add_action( 'widgets_init', 'wpim_core_unregister_widgets' );
// Hoook into the 'wp_dashboard_setup' action to remove the widgets our function
add_action( 'wp_dashboard_setup', 'wpim_core_remove_dashboard_widgets' );
// Hoook into the 'wp_dashboard_setup' action to register our other functions
add_action( 'wp_dashboard_setup', 'wpim_core_dashboard_widgets' );

function wpim_theme_admin_notices() {
	//@help:  https://wordpress.stackexchange.com/questions/152173/display-admin-notice-only-on-main-dashboard-page
	$ssl_text = 'Your site is not using SSL.  If you plan to take payments for inventory items this will have to be fixed.  Contact your hosting provider to install a valid SSL certificate for you.';
	$class = 'notice-error';
	//TODO:  Not good enough to just check SSL.  Shold check if reserve cart add on is installed AND the stripe gateway add on.
	if ( is_ssl() ) {
		$ssl_text = 'Your site is protected';
		$class = 'notice-success';
	}

	?>
    <div class="notice <?php echo $class; ?> is-dismissible">
        <p><?php _e( $ssl_text, 'sample-text-domain' ); ?></p>
    </div>
	<?php
}

add_action( 'load-index.php',
	function () {
		add_action( 'admin_notices', 'wpim_theme_admin_notices' );
	}
);