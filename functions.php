<?php
/**
 * @package WP Inventory Theme
 * @author  WP Inventory Manager (www.wpinventory.com)
 */

// Define this for use in a few places
define( 'ACG_THEME_NAME', "WP Inventory Manager Theme" );
// Theme version defined in ACGTheme class below

// Require the relevant module files
require_once "ssi/sidebars.php";
require_once "ssi/shortcodes.php";
require_once "ssi/widgets.php";
require_once "ssi/menus.php";
require_once "ssi/listing-page/db.class.php";

/**
 * Core theme class.
 * Designed to be extensible with select hooks and actions.
 */
class ACGTheme {

	const THEME_VERSION = '2.2.0';

	private static $initialized = FALSE;
	public static $allowed_group = 'manage_options';

	private static $stay_in_cat;
	private static $dequeued = array();

	const MENU_SLUG = 'acg_admin_menu';
	const SETTINGS = 'acg_options';
	const SETTINGS_GROUP = 'acg_options_group';

	private static $theme_options = array(
		'use_testimonials' => 'testimonials.php',
		'use_portfolios'   => 'portfolios.php'
	);

	public static function initialize() {
		if ( self::$initialized ) {
			return;
		}


		self::add_theme_options();
		self::add_theme_support();
		self::add_filters();
		self::add_actions();
	}

	private static function add_theme_support() {
		// Enable RSS feeds
		add_theme_support( 'automatic-feed-links' );

		// Enable featured images
		add_theme_support( 'post-thumbnails' );

		// Enable the gallery post format
		// See http://codex.wordpress.org/Post_Formats
		add_theme_support( 'post-formats', array( 'gallery' ) );

		// Enable excerpts for pages
		add_post_type_support( 'page', 'excerpt' );
	}

	private static function add_theme_options() {
		foreach ( self::$theme_options AS $opt => $include ) {
			if ( self::get_option( $opt ) ) {
				require_once 'ssi/' . $include;
			}
		}
	}

	/**
	 * Loop through any actions we want to hook into and hook'em
	 */
	private static function add_actions() {
		$actions = array(
			'init',
			'wp_head',
			'admin_init',
			'admin_menu',
			'admin_head',
			'login_head',
			'add_meta_boxes',
			'wp_enqueue_scripts',
			'wp_print_styles',
			'wp_print_scripts',
			'wp_footer'
		);

		foreach ( $actions AS $action ) {
			if ( method_exists( __CLASS__, $action ) ) {
				add_action( $action, array( __CLASS__, $action ) );
			}
		}
	}

	/**
	 * Add any filters we want to utilize.
	 */
	private static function add_filters() {
		//  Take out WP version from <head>
		add_filter( 'the_generator', array( __CLASS__, 'remove_version_info' ) );

		// Enable custom logo on login page
		add_filter( 'login_headerurl', array( __CLASS__, 'loginpage_custom_link' ) );
		add_filter( 'login_headertext', array( __CLASS__, 'change_title_on_logo' ) );

		// Add more buttons to the visual editor
		add_filter( "mce_buttons", array( __CLASS__, 'enable_more_buttons' ) );

		// Hijack the excerpt so we can use settings
		add_filter( 'the_excerpt', array( __CLASS__, 'the_excerpt' ), 0 );
		add_filter( 'excerpt_length', array( __CLASS__, 'excerpt_length' ), 999 );

		// Support the "stay in category" functionality when navigating blog posts
		add_filter( 'get_previous_post_join', array( __CLASS__, 'get_previous_post_join_filter' ) );
		add_filter( 'get_next_post_join', array( __CLASS__, 'get_next_post_join_filter' ) );
		add_filter( 'previous_post_link', array( __CLASS__, 'previous_post_link_filter' ) );
		add_filter( 'next_post_link', array( __CLASS__, 'next_post_link_filter' ) );
		add_filter( 'get_the_excerpt', 'shortcode_unautop' );
		add_filter( 'get_the_excerpt', 'do_shortcode' );
	}


	public static function init() {
		define( 'TEMPLATE_URL', get_bloginfo( "template_url" ) );
		define( 'SITE_URL', get_bloginfo( "url" ) );
		define( 'SITE_NAME', get_bloginfo( "name" ) );

		if ( ! is_admin() ) {
			wp_enqueue_script( 'jquery-theme-common', TEMPLATE_URL . '/js/theme.common.js', array( 'jquery' ), self::THEME_VERSION );
		}
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );


		wp_enqueue_script( 'jquery-theme-bxslider', TEMPLATE_URL . '/js/jquery.bxslider.min.js', array( 'jquery' ), self::THEME_VERSION );
		wp_enqueue_style( 'acg-bxslider', TEMPLATE_URL . '/css/jquery.bxslider.css' );

		// Only register here.  Gets printed in footer only when needed.
		wp_register_script( 'acg_masonry_script', TEMPLATE_URL . '/js/masonry.js' );

//		if ( self::get_option( 'font_awesome' ) ) {
		wp_enqueue_style( 'acg-font-awesome', TEMPLATE_URL . '/fontawesome/css/fontawesome-all.min.css' );
//		}
	}

	public static function wp_head() {
		self::google_font();

		self::styles_in_head();
	}

	public static function admin_init() {
		register_setting( self::SETTINGS_GROUP, self::SETTINGS );
	}

	public static function admin_menu() {
		add_menu_page( ACG_THEME_NAME, ACG_THEME_NAME, self::$allowed_group, self::MENU_SLUG, array(
			__CLASS__,
			'admin_settings'
		), 'dashicons-admin-generic', 61 );
		do_action( 'acg_admin_submenu' );
	}

	public static function admin_head() {
		self::google_font();
		echo '<link rel="stylesheet" type="text/css" href="' . TEMPLATE_URL . '/css/style-admin.css" />' . "\r\n";
	}

	public static function wp_enqueue_scripts() {
		if ( self::get_option( 'scripts_to_footer' ) ) {
			self::move_scripts_to_footer();
		}
	}

	public static function styles_in_head() {
		$rendered = FALSE;
		if ( self::get_option( 'styles_in_head' ) ) {
			$stylesheet = get_stylesheet_directory() . '/style.php';
			if ( file_exists( $stylesheet ) ) {
				$styles = file_get_contents( $stylesheet );
				echo '<!-- Stylesheet contents loaded for performance based on Performance Settings in theme dashboard -->' . PHP_EOL;
				if ( stripos( $styles, "*/" ) !== FALSE && stripos( $styles, "*/" ) < 500 ) {
					$styles = substr( $styles, stripos( $styles, "*/" ) + 2 );
				}
				echo "<style>" . str_ireplace( "\n", " ", $styles ) . "</style>";
				$rendered = TRUE;
			}
		}

		if ( ! $rendered ) {
			echo '<link type="text/css" rel="stylesheet" href="' . get_stylesheet_uri() . '" />' . PHP_EOL;
		}
	}

	/**
	 * Performance function.
	 * Checks a setting, then dequeues any styles to prevent them from being output in the <head>.
	 */
	public static function wp_print_styles() {
		if ( ! self::get_option( 'dequeue_styles' ) ) {
			return;
		}
		if ( ! is_user_logged_in() && ! is_admin() ) {
			$wp_styles = wp_styles();
			foreach ( $wp_styles->queue AS $handle ) {
				echo '<!-- stylesheed "' . $handle . '" dequeued for performance based on Performance Settings in theme dashboard -->' . PHP_EOL;
				wp_dequeue_style( $handle );
				self::$dequeued[] = $handle;
			}
		} else if ( is_admin() ) {
			echo '<!-- No styles dequeued because viewing admin dashboard. -->' . PHP_EOL;
		} else {
			echo '<!-- No styles dequeued because user is logged in. -->' . PHP_EOL;
		}
	}

	public static function wp_print_scripts() {
		// If the option is set to move to footer, then re-enqueue styles that were dequeued for the <head>
		if ( self::get_option( 'dequeue_styles' ) == 'footer' ) {
			foreach ( self::$dequeued AS $handle ) {
				wp_enqueue_style( $handle );
			}
		}
	}

	/**
	 * Performance function.
	 * Removes scripts from the <head> and puts them in the footer.
	 */
	public static function move_scripts_to_footer() {
		remove_action( 'wp_head', 'wp_print_scripts' );
		remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
		remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );

		add_action( 'wp_footer', 'wp_print_scripts', 5 );
		add_action( 'wp_footer', 'wp_enqueue_scripts', 5 );
		add_action( 'wp_footer', 'wp_print_head_scripts', 5 );
	}

	public static function wp_footer() {
		// ready for stuff
	}

	/**
	 * Provides a rich text editor for excerpts
	 */
	public static function add_meta_boxes() {
		if ( ! post_type_supports( $GLOBALS['post']->post_type, 'excerpt' ) ) {
			return;
		}

		remove_meta_box(
			'postexcerpt' // ID
			, ''            // Screen, empty to support all post types
			, 'normal'      // Context
		);

		add_meta_box(
			'postexcerpt2'     // Reusing just 'postexcerpt' doesn't work.
			, __( 'Excerpt' )    // Title
			, array( __CLASS__, 'show_post_excerpt_editor' ) // Display function
			, NULL              // Screen, we use all screens with meta boxes.
			, 'normal'          // Context
			, 'core'            // Priority
		);
	}

	/**
	 * Displays the rich editor for the excerpt
	 *
	 * @param $post
	 */
	public static function show_post_excerpt_editor( $post ) { ?>
        <label class="screen-reader-text" for="excerpt"><?php
			_e( 'Excerpt' )
			?></label>
		<?php
		// We use the default name, 'excerpt', so we don’t have to care about
		// saving, other filters etc.
		wp_editor(
			self::excerpt_editor_unescape( $post->post_excerpt ),
			'excerpt',
			array(
				'textarea_rows' => 15
			,
				'media_buttons' => FALSE
			,
				'teeny'         => TRUE
			,
				'tinymce'       => TRUE
			)
		);
	}

	/**
	 * The excerpt is escaped usually. This breaks the HTML editor.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function excerpt_editor_unescape( $str ) {
		return str_replace(
			array( '&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;', '&amp;nbsp;' )
			, array( '<', '>', '"', '&', ' ', ' ' )
			, $str
		);
	}

	public static function enable_more_buttons( $buttons ) {
		array_push( $buttons, "backcolor", "anchor", "hr", "fontselect", "sub", "sup" );

		return $buttons;
	}

	// *** Site options - footer, social media, etc.
	public static function admin_settings() {

		$home_h1 = $footer_info = $footer_left = $blog_page_title = '';

		$options = get_option( self::SETTINGS );
		if ( $options ) {
			extract( $options );
		}

		$pageslist = wp_dropdown_pages( array(
			'selected' => ( isset( $options['site_option_page'] ) ) ? $options['site_option_page'] : '',
			'name'     => self::SETTINGS . '[site_option_page]',
			'echo'     => FALSE
		) );

		$font_awesome_checked = ( isset( $options['font_awesome'] ) && $options['font_awesome'] ) ? ' checked' : '';
		$google_fonts         = ( isset( $options['google_fonts'] ) ) ? $options['google_fonts'] : '';

		$opts = array(
			''        => 'Select...',
			'off'     => 'Do Not Load',
			'head'    => 'Load - Always in &lt;head&gt;',
			'default' => 'Load - Honor other performance settings'
		);

		$scripts_to_footer_checked = ( isset( $options['scripts_to_footer'] ) && $options['scripts_to_footer'] ) ? ' checked' : '';
		$styles_in_head_checked    = ( isset( $options['styles_in_head'] ) && $options['styles_in_head'] ) ? ' checked' : '';
		$dequeue_styles            = ( isset( $options['dequeue_styles'] ) ) ? $options['dequeue_styles'] : '';

		$comments_enabled = ( isset( $options['comments_enabled'] ) ) ? $options['comments_enabled'] : '';

		$blog_content       = ( isset( $options['blog_content'] ) ) ? $options['blog_content'] : '';
		$archive_content    = ( isset( $options['archive_content'] ) ) ? $options['archive_content'] : '';
		$social_media_label = ( isset( $options['social_media_label'] ) ) ? $options['social_media_label'] : '';

		$blog_excerpt_length    = ( isset( $options['blog_excerpt_length'] ) ) ? (int) $options['blog_excerpt_length'] : 55;
		$archive_excerpt_length = ( isset( $options['archive_excerpt_length'] ) ) ? (int) $options['archive_excerpt_length'] : 55;

		$blog_pagination    = ( isset( $options['blog_pagination'] ) && $options['blog_pagination'] ) ? ' checked' : '';
		$archive_pagination = ( isset( $options['archive_pagination'] ) && $options['archive_pagination'] ) ? ' checked' : '';

		$blog_read_more    = ( isset( $options['blog_read_more'] ) ) ? $options['blog_read_more'] : '';
		$archive_read_more = ( isset( $options['archive_read_more'] ) ) ? $options['archive_read_more'] : '';

		$stay_in_category = ( isset( $options['stay_in_category'] ) && $options['stay_in_category'] ) ? ' checked' : '';

		$use_portfolios        = ( isset( $options['use_portfolios'] ) && $options['use_portfolios'] ) ? ' checked' : '';
		$portfolios_type       = ( isset( $options['portfolios_type'] ) ) ? $options['portfolios_type'] : '';
		$portfolio_image_width = ( isset( $options['portfolio_image_width'] ) ) ? $options['portfolio_image_width'] : '236';

		$gallery_type        = ( isset( $options['gallery_type'] ) ) ? $options['gallery_type'] : '';
		$gallery_image_width = ( isset( $options['gallery_image_width'] ) ) ? $options['gallery_image_width'] : '236';
		$gallery_gutter      = ( isset( $options['gallery_gutter'] ) ) ? $options['gallery_gutter'] : '10';

		$use_testimonials = ( isset( $options['use_testimonials'] ) && $options['use_testimonials'] ) ? ' checked' : '';

		$placeholder_text   = ( isset( $options['placeholder_text'] ) ) ? $options['placeholder_text'] : 'Search';
		$search_button_text = ( isset( $options['search_button_text'] ) ) ? $options['search_button_text'] : 'Search &raquo;';


		$footer_social_label    = ( isset( $options['footer_social_label'] ) ) ? $options['footer_social_label'] : '';
		$fb_icon                = ( isset( $options['footer_fb_icon'] ) ) ? $options['footer_fb_icon'] : '';
		$fb_link                = ( isset( $options['footer_fb_link'] ) ) ? $options['footer_fb_link'] : '';
		$twitter_icon           = ( isset( $options['footer_twitter_icon'] ) ) ? $options['footer_twitter_icon'] : '';
		$twitter_link           = ( isset( $options['footer_twitter_link'] ) ) ? $options['footer_twitter_link'] : '';
		$linked_icon            = ( isset( $options['footer_linked_icon'] ) ) ? $options['footer_linked_icon'] : '';
		$linked_link            = ( isset( $options['footer_linked_link'] ) ) ? $options['footer_linked_link'] : '';
		$instagram_icon         = ( isset( $options['footer_instagram_icon'] ) ) ? $options['footer_instagram_icon'] : '';
		$instagram_link         = ( isset( $options['footer_instagram_link'] ) ) ? $options['footer_instagram_link'] : '';
		$pinterest_icon         = ( isset( $options['footer_pinterest_icon'] ) ) ? $options['footer_pinterest_icon'] : '';
		$pinterest_link         = ( isset( $options['footer_pinterest_link'] ) ) ? $options['footer_pinterest_link'] : '';
		$rss_icon               = ( isset( $options['footer_rss_icon'] ) ) ? $options['footer_rss_icon'] : '';
		$rss_link               = ( isset( $options['footer_rss_link'] ) ) ? $options['footer_rss_link'] : '';
		$inner_default_featured = ( isset( $options['inner_default_featured'] ) ) ? $options['inner_default_featured'] : '';
		$theme_logo             = ( isset( $options['wpim_theme_logo'] ) ) ? $options['wpim_theme_logo'] : '';
		$wpim_theme_footer_copy = ( isset( $options['wpim_theme_footer_copy'] ) ? $options['wpim_theme_footer_copy'] : '' );
		$wpim_theme_email       = ( isset( $options['wpim_theme_email'] ) ) ? $options['wpim_theme_email'] : '';
		$wpim_theme_phone       = ( isset( $options['wpim_theme_phone'] ) ) ? $options['wpim_theme_phone'] : '';
		$opts                   = [
			''        => 'Select...',
			'content' => 'Full Article',
			'excerpt' => 'Excerpt'
		];

		$blog_dropdown    = self::dropdown_array( self::SETTINGS . '[blog_content]', $opts, $blog_content );
		$archive_dropdown = self::dropdown_array( self::SETTINGS . '[archive_content]', $opts, $archive_content );


		$opts = [
			'default'   => 'Default',
			'slideshow' => 'Slideshow',
			'masonry'   => 'Masonry',
		];

		$portfolio_dropdown = self::dropdown_array( self::SETTINGS . '[portfolios_type]', $opts, $portfolios_type );

		$image_link_type = ( ! empty( $image_link_type ) ) ? $image_link_type : 'url';

		update_option( 'image_default_link_type', $image_link_type );

		$opts = [
			'file'   => 'Media File',
			'post'   => 'Attachment Page',
			'custom' => 'Custom Url',
			'none'   => 'No Link'
		];

		$image_link_dropdown = self::dropdown_array( self::SETTINGS . '[image_link_type]', $opts, $image_link_type );

		$opts = [
			''       => 'Off. Do Not Dequeue.',
			'remove' => 'On. Remove Styles Completely.',
			'footer' => 'On. Move Styles to Footer.',
		];

		$dequeue_dropdown = self::dropdown_array( self::SETTINGS . '[dequeue_styles]', $opts, $dequeue_styles );

		?>
        <div id="acg_options">
            <h2>Manage Website Options</h2>

            <form method="post" action="options.php">
				<?php settings_fields( self::SETTINGS_GROUP ); ?>
                <table class="form-table the-acg">
                    <tr>
                        <td colspan="2">
                            <h1><?php echo apply_filters( 'wpim_theme_settings_heading', __( 'WP Inventory Manager Theme Settings' ) ); ?></h1>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_search_text', __( 'Search Placeholder Text' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[placeholder_text]" size="20"
                                   value="<?php echo stripslashes( $placeholder_text ); ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_search_button_text', __( 'Search Button Text' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[search_button_text]" size="20"
                                   value="<?php echo stripslashes( $search_button_text ); ?>"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3><?php echo apply_filters( 'wpim_theme_performance_heading', __( 'Performance Options' ) ); ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_move_scripts_to_footer_text', __( 'Move Scripts to Footer' ) ); ?></th>
                        <td><input type="checkbox"
                                   name="<?php echo self::SETTINGS; ?>[scripts_to_footer]"<?php echo $scripts_to_footer_checked; ?> />

							<?php
							//TODO: internationalize this
							?>
                            <p class="description">Check for better performance.<br>WHY DO IT: Moves all scripts (that
                                are being loaded the proper way) to the footer, making them non-content blocking.<br>DANGER:
                                This may break plugins that rely on script in the &lt;head&gt;</td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_load_styles_inline_in_head', __( 'Load Theme Styles Inline in Head' ) ); ?></th>
                        <td><input type="checkbox"
                                   name="<?php echo self::SETTINGS; ?>[styles_in_head]"<?php echo $styles_in_head_checked; ?> />
							<?php
							//TODO: internationalize this
							?>
                            <p class="description">Check for better performance.<br>WHY DO IT: Loads the theme
                                stylesheet contents into a &lt;style&gt; block in the head, rather than linking to the
                                stylesheet, eliminating one more server request.<br>DANGER: When you view source, it's
                                not as pretty!</td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_dequeue_plugin_and_other_styles', __( 'Dequeue Plugin and Other Styles' ) ); ?></th>
                        <td><?php echo $dequeue_dropdown; ?>
							<?php
							//TODO: internationalize this
							?>
                            <p class="description">Turn on for better performance.<br>WHY DO IT: Eliminates any
                                stylesheet
                                requests that may be added by plugins. DANGER: When you add a plugin, it may be unstyled
                                and not look correct.<br>NOTE: Does not dequeue styles in Admin Dashboard, or for logged
                                in
                                users.</p></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3><?php echo apply_filters( 'wpim_social_media_heading', __( 'Social Media' ) ); ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
							<?php
							//TODO: internationalize this
							?>
                            <p>For a list of available font icons to use for Social Media below, <a target="_blank"
                                                                                                    href="http://fontawesome.io/icons/">Click
                                    Here</a>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_social_media_label', __( 'Label Text' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[social_media_label]" size="40"
                                   value="<?php echo stripslashes( $social_media_label ); ?>"
                                   placeholder="Ex: follow us:"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_social_facebook_text', __( 'Facebook' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_fb_icon]"
                                   value="<?php echo stripslashes( htmlentities( $fb_icon ) ); ?>"
                                   placeholder="Ex. fa-facebook"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_fb_link]"
                                   value="<?php echo stripslashes( htmlentities( $fb_link ) ); ?>"
                                   placeholder="Ex. http://www.facebook.com/12345/"/><br>
                            <small><?php echo apply_filters( 'wpim_social_media_url_tip', __( '*Must include http://' ) ); ?></small>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row">Twitter</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_twitter_icon]"
                                   value="<?php echo stripslashes( htmlentities( $twitter_icon ) ); ?>"
                                   placeholder="Ex. fa-twitter"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_twitter_link]"
                                   value="<?php echo stripslashes( htmlentities( $twitter_link ) ); ?>"
                                   placeholder="Ex. http://www.twitter.com/@example/"/>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row">LinkedIn</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_linked_icon]"
                                   value="<?php echo stripslashes( htmlentities( $linked_icon ) ); ?>"
                                   placeholder="Ex. fa-linkedin"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_linked_link]"
                                   value="<?php echo stripslashes( htmlentities( $linked_link ) ); ?>"
                                   placeholder="Ex. http://www.linkedin.com/in/yourname/"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Yelp</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_pinterest_icon]"
                                   value="<?php echo stripslashes( htmlentities( $pinterest_icon ) ); ?>"
                                   placeholder="Ex. fa-yelp"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_pinterest_link]"
                                   value="<?php echo stripslashes( htmlentities( $pinterest_link ) ); ?>"
                                   placeholder="Ex. http://www.yelp.com/"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Youtube</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_rss_icon]"
                                   value="<?php echo stripslashes( htmlentities( $rss_icon ) ); ?>"
                                   placeholder="Ex. fa-youtube"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_rss_link]"
                                   value="<?php echo stripslashes( htmlentities( $rss_link ) ); ?>"
                                   placeholder="Ex. http://www.youtube.com/your-channel-here/"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Instagram</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[footer_instagram_icon]"
                                   value="<?php echo stripslashes( htmlentities( $instagram_icon ) ); ?>"
                                   placeholder="Ex. fa-instagram"/><br>
                            <input type="text" name="<?php echo self::SETTINGS; ?>[footer_instagram_link]"
                                   value="<?php echo stripslashes( htmlentities( $instagram_link ) ); ?>"
                                   placeholder="Ex. http://www.instagram.com/"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3><?php echo apply_filters( 'wpim_theme_contact_info_heading', __( 'Contact Information' ) ); ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_email', 'Email' ); ?></th>
                        <td><input type="email" name="<?php echo self::SETTINGS; ?>[wpim_theme_email]" size="40"
                                   value="<?php echo $wpim_theme_email; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_phone', __( 'Phone' ) ); ?></th>
                        <td><input type="tel" name="<?php echo self::SETTINGS; ?>[wpim_theme_phone]" size="40"
                                   value="<?php echo $wpim_theme_phone; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3><?php echo apply_filters( 'wpim_theme_blog_setting_heading', __( 'Blog Listing' ) ); ?></h3>
                        </td>
                    </tr>
                    <tr class="">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_blog_page_title', __( 'Blog Page Title' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[blog_page_title]"
                                   value="<?php echo stripslashes( htmlentities( $blog_page_title ) ); ?>"/></td>
                    </tr>
                    <tr class="blog dropdown blog_dropdown">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_blog_page_post_or_excerpt', __( 'Full Post or Excerpt' ) ); ?></th>
                        <td><?php echo $blog_dropdown; ?><p class="description"><?php echo apply_filters( 'wpim_blog_page_post_or_excerpt_description', __( 'Whether to show full post content, or
                                excerpt, on the blog page.' )
								); ?></p></td>
                    </tr>
                    <tr class="blog excerpt blog_excerpt">
                        <th scope="row"><?php echo apply_filters( 'wpim_blog_page_excerpt_length', __( 'Excerpt Length' ) ); ?></th>
                        <td><input type="text" size="4" name="<?php echo self::SETTINGS; ?>[blog_excerpt_length]"
                                   value="<?php echo $blog_excerpt_length; ?>"/></td>
                    </tr>
                    <tr class="blog excerpt blog_excerpt">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_read_more_link_text', __( 'Read More Link' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[blog_read_more]"
                                   value="<?php echo $blog_read_more; ?>"/>

                            <p class="description">The text to display in the link to read the full article.</p></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_use_blog_pagination', __( 'Use Pagination Navigation' ) ); ?></th>
                        <td><input type="checkbox"
                                   name="<?php echo self::SETTINGS; ?>[blog_pagination]"<?php echo $blog_pagination; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3><?php echo apply_filters( 'wpim_theme_archive_listing_heading', __( 'Archive Listing' ) ); ?></h3>
                        </td>
                    </tr>
                    <tr class="archive dropdown archive_dropdown">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_full_post_or_excerpt', __( 'Full Post or Excerpt' ) ); ?></th>
                        <td><?php echo $archive_dropdown; ?><p class="description">Whether to show full post content, or
                                excerpt, on the archive pages (category, date, author, etc).</p></td>
                    </tr>
                    <tr class="archive excerpt archive_excerpt">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_post_excerpt_length', __( 'Excerpt Length' ) ); ?></th>
                        <td><input type="text" size="4" name="<?php echo self::SETTINGS; ?>[archive_excerpt_length]"
                                   value="<?php echo $archive_excerpt_length; ?>"/></td>
                    </tr>
                    <tr class="archive excerpt archive_excerpt">
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_archive_readmore_text', __( 'Archive Read More' ) ); ?></th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[archive_read_more]"
                                   value="<?php echo $archive_read_more; ?>"/>

                            <p class="description"><?php echo apply_filters( 'wpim_theme_post_excerpt_readmore_text', __( 'The text to display in the link to read the full article.' ) ); ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_blog_pagination_navigation', __( 'Use Pagination Navigation' ) ); ?></th>
                        <td><input type="checkbox"
                                   name="<?php echo self::SETTINGS; ?>[archive_pagination]"<?php echo $archive_pagination; ?> />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo apply_filters( 'wpim_theme_single_nav_stays_in_cat', __( 'Single Nav Stays in Category' ) ); ?></th>
                        <td><input type="checkbox"
                                   name="<?php echo self::SETTINGS; ?>[stay_in_category]"<?php echo $stay_in_category; ?> />

                            <p class="description">When you view a blog post via the Category archive, the previous /
                                next navigation in the single will stay in the category.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Use Portfolios</th>
                        <td><input type="checkbox" class="theme_option" data-option=".portfolios"
                                   name="<?php echo self::SETTINGS; ?>[use_portfolios]"<?php echo $use_portfolios; ?> />
                        </td>
                    </tr>
                    <tr class="dropdown gallery portfolios">
                        <th>Portfolio Style</th>
                        <td><?php echo $portfolio_dropdown; ?><p class="description">Which style of portfolio to
                                display.</p></td>
                    </tr>
                    <tr class="portfolios">
                        <th>Portfolio Image Width</th>
                        <td><input type="text" name="<?php echo self::SETTINGS; ?>[portfolio_image_width]"
                                   value="<?php echo $portfolio_image_width; ?>"/>px<p class="description">When using
                                masonry, sets the width of the images.</p></td>
                    </tr>
                    </tr>
                    <tr>
                        <th>Use Testimonials</th>
                        <td><input type="checkbox" class="theme_option" data-option=".testimonials"
                                   name="<?php echo self::SETTINGS; ?>[use_testimonials]"<?php echo $use_testimonials; ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>Logo Upload</th>
                        <td>
                            <input type="hidden" name="<?php echo self::SETTINGS; ?>[wpim_theme_logo]" value="<?php echo $theme_logo; ?>">
                            <input id="upload_image_button" type="button" value="<?php echo( empty( $theme_logo ) ? 'Upload Image' : 'Change Image' ) ?>">
                            <div id="show_wpim_theme_logo"><?php echo( ! empty( $theme_logo ) ? "<img src='$theme_logo'>" : '' ) ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th>Footer Copy</th>
                        <td>
							<?php
							$settings = [
								'quicktags'     => [ 'buttons' => 'em,strong,link' ],
								'textarea_name' => 'acg_options[wpim_theme_footer_copy]',
								'tinymce'       => TRUE
							];
							$id       = 'wpim_theme_footer_copy';//has to be lower case
							wp_editor( $wpim_theme_footer_copy, $id, $settings );
							?>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>"/>
                </p>
            </form>
        </div>
        <script>
          jQuery( function ( $ ) {
            $( 'tr.dropdown select' ).change(
              function () {
                var val  = $( this ).val();
                var type = $( this ).closest( 'tr' ).hasClass( 'blog' ) ? 'blog' : 'archive';
                var el   = $( 'tr.' + type + '_excerpt' );
                if ( val == 'excerpt' ) {
                  el.fadeIn();
                } else {
                  el.fadeOut();
                }
              }
            ).trigger( 'change' );

            $( 'input.theme_option' ).click(
              function () {
                var cname = $( this ).attr( 'data-option' );
                if ( $( this ).is( ':checked' ) ) {
                  $( cname ).fadeIn();
                } else {
                  $( cname ).fadeOut();
                }
              }
            ).each(
              function () {
                if ( !$( this ).is( ':checked' ) ) {
                  var cname = $( this ).attr( 'data-option' );
                  $( cname ).hide();
                }
              }
            );
          } );
        </script>
		<?php
	}

	public static function dropdown_array( $name, $opts, $selected = '' ) {
		$dropdown = '<select name="' . $name . '">';

		foreach ( $opts AS $val => $text ) {
			$dropdown .= '<option value="' . $val . '"';
			$dropdown .= ( $selected == $val ) ? ' selected' : '';
			$dropdown .= '>' . $text . '</option>';
		}

		$dropdown .= '</select>';

		return $dropdown;
	}

	private static function google_font() {
		$google_fonts = self::get_option( 'google_fonts' );

		if ( $google_fonts ) {
			$google_fonts = explode( '|', $google_fonts );
			$google_fonts = implode( "', '", $google_fonts ); ?>
            <script type="text/javascript">
              WebFontConfig = {
                google: { families: ['<?php echo $google_fonts; ?>'] }
              };
              (function () {
                var wf   = document.createElement( 'script' );
                wf.src   = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
                wf.type  = 'text/javascript';
                wf.async = 'true';
                var s    = document.getElementsByTagName( 'script' )[ 0 ];
                s.parentNode.insertBefore( wf, s );
              })(); </script>
			<?php
//             echo '<link href="//fonts.googleapis.com/css?family=' . $google_fonts . '" rel="stylesheet">' . PHP_EOL;
		}
	}

	public static function get_option( $key, $default = '' ) {
		$options = get_option( self::SETTINGS );

		return ( isset( $options[ $key ] ) ) ? $options[ $key ] : $default;
	}

	public static function the_excerpt( $content = NULL ) {
		$readmore = self::excerpt_read_more();
		$content  .= $readmore;

		return $content;
	}

	public static function excerpt_read_more( $read_more = "Read More" ) {
		$type = ( is_archive() ) ? 'archive' : 'blog';

		$read_more = self::get_option( $type . '_read_more' );
		if ( ! $read_more ) {
			$read_more = 'Read More...';
		}

		if ( is_front_page() ) {
			return '';
		}

		return '<span class="read-more-wrapper"><a class="read-more" href="' . get_permalink( get_the_ID() ) . '"><span>' . $read_more . '</span></a></span>';
	}


	function custom_excerpt_length( $length ) {
		$type   = ( is_archive() ) ? 'archive' : 'blog';
		$length = (int) self::get_option( $type . '_excerpt_length', 55 );

		return $length;
	}

	public static function navigation( $location = 'blog' ) {
		$pagination = self::get_option( $location . '_pagination', FALSE );
		if ( $pagination ) {

			echo '<div class="navigation">';
			echo self::paginate_links();
			echo '</div>';

		} else {
			?>
            <div class="navigation">
                <div class="alignleft"><?php next_posts_link( '&laquo; Older Entries' ) ?></div>
                <div class="alignright"><?php previous_posts_link( 'Newer Entries &raquo;' ) ?></div>
            </div>
		<?php }
	}

	public static function the_content( $type = NULL ) {
		if ( ! is_front_page() ) {
			global $post;
			$type = ( is_archive() ) ? 'archive' : 'blog';

			$content = self::get_option( $type . '_content', 'content' );
			if ( $content == 'excerpt' ) {
				the_excerpt();
			} else {
				the_content();
			}
			edit_post_link( 'Edit ' . $post->post_type, '<div class="edit_link">', '</div>' );
		} else {
			the_content();
		}
	}

	public static function excerpt_length( $length = 55 ) {
		return $length;
	}

	public static function comments() {
		global $post;
		$enabled = self::get_option( 'comments_enabled' );
		if ( $post->post_type == $enabled || $enabled == 'both' ) {
			comments_template();
		}
	}

	public static function paginate_links() {
		global $wp_query;
		$big = 999999999; // need an unlikely integer

		$args = array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'total'     => $wp_query->max_num_pages,
			'end_size'  => 1,
			'mid_size'  => 4,
			'prev_text' => '<i class="fa fa-arrow-left"></i><span class="title">Older Posts</span>',
			'next_text' => '<span class="title">Newer Posts</span><i class="fa fa-arrow-right"></i>',
		);

		return paginate_links( $args );
	}

	// Enable custom login logo
	public static function login_head() {
		$logo = ( ACGTheme::get_option( 'wpim_theme_logo' ) ) ? ACGTheme::get_option( 'wpim_theme_logo' ) : '';
		echo '<style type="text/css">
		#login {
			width: 100%;
			padding: 0;
		}
		.login h1 {
			width: 100%;
			text-align: center;
			padding: 30px 0 0;
		}
        .login h1 a {
			background-image:url(' . $logo . ');
			width: 100%;
			max-width: 422px;
			height: 141px;
			background-size: inherit;
			padding: 0;
			margin: 0;
			display: inline-block;
         }
         .login form {
         	margin: 0 20px;
         }
    </style>';

	}

	// Enable custom login logo link
	public static function loginpage_custom_link() {
		return get_bloginfo( 'url' );
	}

	// Enable custom tooltip on login logo
	public static function change_title_on_logo() {
		return 'Welcome to the ' . ACG_THEME_NAME . ' dashboard. Login to manage your site below.';
	}

	//  Removes the WordPress version number from the <head>
	public static function remove_version_info() {
		return '';
	}

	public static function get_previous_post_link( $format, $link ) {
		if ( self::get_option( 'stay_in_category' ) ) {
			self::$stay_in_cat = ( isset( $_GET['cat_specific'] ) ) ? $_GET['cat_specific'] : FALSE;
		}

		return get_previous_post_link( $format, $link, self::$stay_in_cat );
	}

	public static function get_next_post_link( $format, $link ) {
		if ( self::get_option( 'stay_in_category' ) ) {
			self::$stay_in_cat = ( isset( $_GET['cat_specific'] ) ) ? $_GET['cat_specific'] : FALSE;
		}

		return get_next_post_link( $format, $link, self::$stay_in_cat );
	}

	// Modification to stay in the category set in archive.php
	public static function get_previous_post_join_filter( $join ) {
		if ( self::$stay_in_cat ) {
			$join = preg_replace( '/tt.term_id IN \([^(]+\)/', "tt.term_id IN ({self::$stay_in_cat)}", $join );
		}

		return $join;
	}

	public static function get_next_post_join_filter( $join, $in_same_cat = FALSE, $excluded_categories = '' ) {
		if ( self::$stay_in_cat ) {
			$join = preg_replace( '/tt.term_id IN \([^(]+\)/', "tt.term_id IN ({self::$stay_in_cat})", $join );
		}

		return $join;
	}

	public static function previous_post_link_filter( $link = '' ) {
		if ( self::$stay_in_cat && $link ) {
			$link = self::add_query_arg( 'cat_specific', self::$stay_in_cat, $link );
		}

		return $link;
	}

	public static function next_post_link_filter( $link = '' ) {
		if ( self::$stay_in_cat && $link ) {
			$link = self::add_query_arg( 'cat_specific', self::$stay_in_cat, $link );
		}

		return $link;
	}

	public static function add_query_arg( $key, $value, $link ) {
		// Adds the parameter $key=$value to $link, or replaces it if already there.
		// Necessary because add_query_arg fails on previous/next_post_link.
		if ( strpos( $link, 'href' ) ) {
			$hrefpat = '/(href *= *([\"\']?)([^\"\' ]+)\2)/';
		} else {
			$hrefpat = '/(([\"\']?)(http([^\"\' ]+))\2)/';
		}

		if ( preg_match( $hrefpat, $link, $matches ) ) {
			$url    = $matches[3];
			$newurl = add_query_arg( $key, $value, $url );
			$link   = str_replace( $url, $newurl, $link );
		}

		return $link;
	}
}

ACGTheme::initialize();


/*
 * Functions to get the Previous and Next Post Title for the Single Navigation
 */

function get_prev_nav_with_title( $text = 'Previous', $stay_in_cat = TRUE ) {
	get_nav_link_with_title( TRUE, $text, $stay_in_cat );
}

function get_next_nav_with_title( $text = 'Next', $stay_in_cat = TRUE ) {
	get_nav_link_with_title( FALSE, $text, $stay_in_cat );
}

function get_nav_link_with_title( $previous, $text, $stay_in_cat = TRUE ) {
	if ( $previous ) {
		$class = 'alignleft';
		$post  = get_previous_post( $stay_in_cat );
	} else {
		$class = 'alignright';
		$post  = get_next_post( $stay_in_cat );
	}

	if ( $post && $post->post_title ) {
		$permalink = get_permalink( $post->ID );
		echo "<div class='{$class}'>
                <a href='{$permalink}'>
                    <div class='textwrapper'>
                        <span class='text'>{$text}</span>
                        <span class='title'>{$post->post_title}</span>
                    </div>
                </a>
              </div>";
	}
}

function wpim_theme_social() {
	?>
    <div class="wpim_theme_social">
		<?php

		echo '<ul>';

		if ( ACGTheme::get_option( 'social_media_label' ) ) {
			echo '<li id="social_label">' . ACGTheme::get_option( 'social_media_label' ) . '</li>';
		}

		// Youtube
		if ( ACGTheme::get_option( 'footer_rss_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_rss_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_rss_icon' ) . '"></i></a></li>';
		}

		// Facebook
		if ( ACGTheme::get_option( 'footer_fb_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_fb_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_fb_icon' ) . '"></i></a></li>';
		}

		// Twitter
		if ( ACGTheme::get_option( 'footer_twitter_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_twitter_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_twitter_icon' ) . '"></i></a></li>';
		}

		// Yelp
		if ( ACGTheme::get_option( 'footer_pinterest_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_pinterest_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_pinterest_icon' ) . '"></i></a></li>';
		}

		// LinkedIn
		if ( ACGTheme::get_option( 'footer_linked_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_linked_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_linked_icon' ) . '"></i></a></li>';
		}

		// Instagram
		if ( ACGTheme::get_option( 'footer_instagram_icon' ) ) {
			echo '<li><a target="_blank" href="' . ACGTheme::get_option( 'footer_instagram_link' ) . '"><i class="fab ' . ACGTheme::get_option( 'footer_instagram_icon' ) . '"></i></a></li>';
		}

		echo '</ul>';
		?>
    </div>
	<?php
}

function wpim_get_theme_social() {
	if ( ! empty( ACGTheme::get_option( 'wpim_theme_email' ) ) || ! empty( ACGTheme::get_option( 'wpim_theme_phone' ) ) ) {
		echo '<div class="wpim_theme_contact">';
		if ( ! empty( ACGTheme::get_option( 'wpim_theme_email' ) ) ) {
			echo '<span>' . __( 'Email:' ) . ' <a href="mailto:' . ACGTheme::get_option( 'wpim_theme_email' ) . '">' . ACGTheme::get_option( 'wpim_theme_email' ) . '</a></span>';
		}

		if ( ! empty( ACGTheme::get_option( 'wpim_theme_phone' ) ) ) {
			echo '<span>' . __( 'Phone:' ) . ' <a href="tel:' . ACGTheme::get_option( 'wpim_theme_phone' ) . '">' . ACGTheme::get_option( 'wpim_theme_phone' ) . '</a></span>';
		}
		echo '</div>';
	}
}

function wpim_theme_logo() {

	$content = '<a id="logo" href="' . SITE_URL . '"><img src="' . ACGTheme::get_option( 'wpim_theme_logo' ) . '" alt="' . get_bloginfo( "name" ) . '"></a>';

	if ( empty( ACGTheme::get_option( 'wpim_theme_logo' ) ) ) {
		$content = '<a id="logo" href="' . SITE_URL . '"><img
                            src="' . TEMPLATE_URL . '/images/logo.gif" alt="' . get_bloginfo( "name" ) . '"></a>';
	}

	return $content;
}

function wpim_theme_admin_styles( $hook ) {
//	if ( 'edit.php' != $hook ) {
//		return;
//	}
	wp_enqueue_style( 'wpim_theme_admin_styles', TEMPLATE_URL . '/admin.css' );
	wp_enqueue_script( 'wpim_theme_admin_script', TEMPLATE_URL . '/js/theme.admin.js' );
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), FALSE, TRUE );

}

add_action( 'admin_enqueue_scripts', 'wpim_theme_admin_styles' );