<?php

class WPIMThemeDB {
	const LISTING_SIDEBAR = 'wpim_listing_page_sidebar';

	/**
	 * Constructor magic method.
	 */
	public function __construct() {
		self::add_actions();
//		self::wpim_theme_default_values();
	}

	/**
	 * This is here purely to prevent someone from cloning the class
	 */
	private function __clone() {
	}

	public static function add_actions() {
		$actions = [
			'add_meta_boxes_page' => NULL,
			'save_post'           => NULL
		];

		foreach ( $actions as $action => $args ) {
			if ( method_exists( __CLASS__, $action ) ) {
				if ( ! $args ) {
					add_action( $action, [ __CLASS__, $action ] );
				} else {
					add_action( $action, [ __CLASS__, $action ], $args[0], $args[1] );
				}
			}
		}
	}

	public static function add_meta_boxes_page() {
		global $post;
		if ( 'page-inventory-listing.php' == get_post_meta( $post->ID, '_wp_page_template', TRUE ) ) {
			add_meta_box( 'listing_page_settings', 'Listing Page Settings', [ __CLASS__, 'listing_page_metabox' ] );
		}
	}

	public static function listing_page_metabox() {
		global $post;
		$sidebar_option = (int) get_post_meta( $post->ID, self::LISTING_SIDEBAR, TRUE );
		if ( ! $sidebar_option ) {
			$sidebar_option = 0;
		}
		wp_nonce_field( 'wpim_theme_listing_page_metabox_nonce', 'wpim_theme_listing_page_settings_nonce_value' );

		?>
        <fieldset>
            <legend><?php _e( 'Use WP Inventory Manager Sidebar' ); ?></legend>
            <div class="switch-field">
				<?php
				$checked = '';
				if ( $sidebar_option === 1 ) {
					$checked = 'checked';
				}
				echo '<label><input type="checkbox" name="wpim_listing_page_sidebar" value="1" ' . $checked . '>
  <span class="slider round"></span></label>';

				?>
            </div>
        </fieldset>
        <fieldset>
			<?php
			$sidebar_position = get_post_meta( $post->ID, 'wpim_listing_page_sidebar_position', TRUE );
			if ( ! $sidebar_position ) {
				$sidebar_position = 'left';
			}
			?>
            <legend><?php _e( 'Move Sidebar to the Left?' ); ?></legend>
            <div class="switch-field">
				<?php
				$checked = '';
				if ( $sidebar_position === 'left' ) {
					$checked = 'checked';
				}
				echo '<label><input type="checkbox" name="wpim_listing_page_sidebar_position" value="left" ' . $checked . '>
  <span class="slider round"></span></label>';

				?>
            </div>
        </fieldset>
        <fieldset>
			<?php
			$sidebar_on_detail = (int) get_post_meta( $post->ID, 'wpim_listing_page_sidebar_on_detail', TRUE );
			if ( ! $sidebar_on_detail ) {
				$sidebar_on_detail = 0;
			}
			?>
            <legend><?php _e( 'Show Sidebar on Detail Page' ); ?></legend>
            <div class="switch-field">
				<?php
				$checked = '';
				if ( $sidebar_on_detail === 1 ) {
					$checked = 'checked';
				}
				echo '<label><input type="checkbox" name="wpim_listing_page_sidebar_on_detail" value="1" ' . $checked . '>
  <span class="slider round"></span></label>';

				?>
            </div>
        </fieldset>
        <fieldset>
			<?php
			$hide_content = (int) get_post_meta( $post->ID, 'wpim_listing_page_hide_content_on_detail', TRUE );
			if ( ! $hide_content ) {
				$hide_content = 0;
			}
			?>
            <legend><?php _e( 'Hide Content on Detail Page' ); ?></legend>
            <small><?php _e( 'This will hide any content you have put either above or below the shortcode when the detail page is rendered.  Effectively displaying the item details only.' ); ?></small>
            <div class="switch-field">
				<?php
				$checked = '';
				if ( $hide_content === 1 ) {
					$checked = 'checked';
				}
				echo '<label><input type="checkbox" name="wpim_listing_page_hide_content_on_detail" value="1" ' . $checked . '>
  <span class="slider round"></span></label>';

				?>
            </div>
        </fieldset>
	<?php }

	public static function save_post( $post ) {

		if ( ! isset( $_POST['wpim_theme_listing_page_settings_nonce_value'] ) || ! wp_verify_nonce( $_POST['wpim_theme_listing_page_settings_nonce_value'], 'wpim_theme_listing_page_metabox_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post ) ) {
			return;
		}

		update_post_meta( $post, 'wpim_listing_page_sidebar', sanitize_text_field( $_POST['wpim_listing_page_sidebar'] ) );
		update_post_meta( $post, 'wpim_listing_page_sidebar_position', sanitize_text_field( $_POST['wpim_listing_page_sidebar_position'] ) );
		update_post_meta( $post, 'wpim_listing_page_sidebar_on_detail', sanitize_text_field( $_POST['wpim_listing_page_sidebar_on_detail'] ) );
		update_post_meta( $post, 'wpim_listing_page_hide_content_on_detail', sanitize_text_field( $_POST['wpim_listing_page_hide_content_on_detail'] ) );
	}
}

new WPIMThemeDB();