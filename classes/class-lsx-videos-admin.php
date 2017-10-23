<?php
/**
 * LSX Videos Admin Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Admin {

	/**
	 * Construct method.
	 */
	public function __construct() {
		if ( ! class_exists( 'CMB_Meta_Box' ) ) {
			require_once( LSX_VIDEOS_PATH . '/vendor/Custom-Meta-Boxes/custom-meta-boxes.php' );
		}

		if ( function_exists( 'tour_operator' ) ) {
			$this->options = get_option( '_lsx-to_settings', false );
		} else {
			$this->options = get_option( '_lsx_settings', false );

			if ( false === $this->options ) {
				$this->options = get_option( '_lsx_lsx-settings', false );
			}
		}

		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_filter( 'cmb_meta_boxes', array( $this, 'field_setup' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		add_action( 'init', array( $this, 'create_settings_page' ), 100 );
		add_filter( 'lsx_framework_settings_tabs', array( $this, 'register_tabs' ), 100, 1 );

		add_filter( 'type_url_form_media', array( $this, 'change_attachment_field_button' ), 20, 1 );
		add_filter( 'enter_title_here', array( $this, 'change_title_text' ) );

		add_filter( 'cf_custom_fields_pre_save_meta_key_to_post_type', array( $this, 'save_video_to_cmb' ), 10, 5 );

		add_filter( 'manage_video_posts_columns', array( $this, 'columns_head' ), 10 );
		add_action( 'manage_video_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
	}

	/**
	 * Register the Video post type.
	 */
	public function post_type_setup() {
		$labels = array(
			'name'               => esc_html_x( 'Videos', 'post type general name', 'lsx-videos' ),
			'singular_name'      => esc_html_x( 'Video', 'post type singular name', 'lsx-videos' ),
			'add_new'            => esc_html_x( 'Add New', 'post type general name', 'lsx-videos' ),
			'add_new_item'       => esc_html__( 'Add New Video', 'lsx-videos' ),
			'edit_item'          => esc_html__( 'Edit Video', 'lsx-videos' ),
			'new_item'           => esc_html__( 'New Video', 'lsx-videos' ),
			'all_items'          => esc_html__( 'All Videos', 'lsx-videos' ),
			'view_item'          => esc_html__( 'View Video', 'lsx-videos' ),
			'search_items'       => esc_html__( 'Search Videos', 'lsx-videos' ),
			'not_found'          => esc_html__( 'No videos found', 'lsx-videos' ),
			'not_found_in_trash' => esc_html__( 'No videos found in Trash', 'lsx-videos' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'Videos', 'admin menu', 'lsx-videos' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'menu_icon'          => 'dashicons-video-alt',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => 'videos',
			),
			'capability_type'    => 'post',
			'has_archive'        => 'videos',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
			),
		);

		register_post_type( 'video', $args );
	}

	/**
	 * Register the Video Category taxonomy.
	 */
	public function taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Video Categories', 'taxonomy general name', 'lsx-videos' ),
			'singular_name'     => esc_html_x( 'Category', 'taxonomy singular name', 'lsx-videos' ),
			'search_items'      => esc_html__( 'Search Categories', 'lsx-videos' ),
			'all_items'         => esc_html__( 'All Categories', 'lsx-videos' ),
			'parent_item'       => esc_html__( 'Parent Category', 'lsx-videos' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'lsx-videos' ),
			'edit_item'         => esc_html__( 'Edit Category', 'lsx-videos' ),
			'update_item'       => esc_html__( 'Update Category', 'lsx-videos' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'lsx-videos' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'lsx-videos' ),
			'menu_name'         => esc_html__( 'Categories', 'lsx-videos' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array(
				'slug' => 'videos-category',
			),
		);

		register_taxonomy( 'video-category', array( 'video' ), $args );
	}

	/**
	 * Add metabox with custom fields to the Video post type.
	 */
	public function field_setup( $meta_boxes ) {
		$prefix = 'lsx_video_';

		$fields = array(
			array(
				'name' => esc_html__( 'Featured:', 'lsx-videos' ),
				'id'   => $prefix . 'featured',
				'type' => 'checkbox',
			),
			// * Using post title
			// array(
			// 	'name' => esc_html__( 'Video Title:', 'lsx-videos' ),
			// 	'id'   => $prefix . 'title',
			// 	'type' => 'text',
			// ),
			// * Using post description
			// array(
			// 	'name' => esc_html__( 'Video Description', 'lsx-videos' ),
			// 	'id'   => $prefix . 'description',
			// 	'type' => 'textarea',
			// 	'rows' => 5,
			// ),
			array(
				'name' => esc_html__( 'Video source:', 'lsx-videos' ),
				'id'   => $prefix . 'video',
				'type' => 'file',
				'desc' => esc_html__( 'Allowed formats: MP4 (.mp4), WebM (.webm) and Ogg/Ogv (.ogg).', 'lsx-videos' ),
			),
			// * Will save those as hidden meta
			// array(
			// 	'name' => esc_html__( 'Video Time:', 'lsx-videos' ),
			// 	'id'   => $prefix . 'time',
			// 	'type' => 'text',
			// ),
			// array(
			// 	'name' => esc_html__( 'Video Total Views:', 'lsx-videos' ),
			// 	'id'   => $prefix . 'views',
			// 	'type' => 'text',
			// ),
			array(
				'name' => esc_html__( 'Youtube source:', 'lsx-videos' ),
				'id'   => $prefix . 'youtube',
				'type' => 'text_url',
				'desc' => esc_html__( 'It will replace the original video source on front-end.', 'lsx-videos' ),
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Video Details', 'lsx-videos' ),
			'pages'  => 'video',
			'fields' => $fields,
		);

		$fields = array(
			array(
				'name' => esc_html__( 'First Name:', 'lsx-videos' ),
				'id'   => $prefix . 'first_name',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Last Name:', 'lsx-videos' ),
				'id'   => $prefix . 'last_name',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Email Address:', 'lsx-videos' ),
				'id'   => $prefix . 'email',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Phone Number:', 'lsx-videos' ),
				'id'   => $prefix . 'phone',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Country of Residence', 'lsx-videos' ),
				'id'   => $prefix . 'country',
				'type' => 'text',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Video Uploader Details', 'lsx-videos' ),
			'pages'  => 'video',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	/**
	 * Enqueue JS and CSS.
	 */
	public function assets( $hook ) {
		// wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/js/lsx-videos-admin.min.js', array( 'jquery' ), LSX_VIDEOS_VER, true );
		wp_enqueue_style( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/css/lsx-videos-admin.css', array(), LSX_VIDEOS_VER );
	}

	/**
	 * Returns the array of settings to the UIX Class.
	 */
	public function create_settings_page() {
		if ( is_admin() ) {
			if ( ! class_exists( '\lsx\ui\uix' ) && ! function_exists( 'tour_operator' ) ) {
				include_once LSX_VIDEOS_PATH . 'vendor/uix/uix.php';
				$pages = $this->settings_page_array();
				$uix = \lsx\ui\uix::get_instance( 'lsx' );
				$uix->register_pages( $pages );
			}

			if ( function_exists( 'tour_operator' ) ) {
				add_action( 'lsx_to_framework_display_tab_content', array( $this, 'display_settings' ), 11 );
			} else {
				add_action( 'lsx_framework_display_tab_content', array( $this, 'display_settings' ), 11 );
			}
		}
	}

	/**
	 * Returns the array of settings to the UIX Class.
	 */
	public function settings_page_array() {
		$tabs = apply_filters( 'lsx_framework_settings_tabs', array() );

		return array(
			'settings'  => array(
				'page_title'  => esc_html__( 'Theme Options', 'lsx-videos' ),
				'menu_title'  => esc_html__( 'Theme Options', 'lsx-videos' ),
				'capability'  => 'manage_options',
				'icon'        => 'dashicons-book-alt',
				'parent'      => 'themes.php',
				'save_button' => esc_html__( 'Save Changes', 'lsx-videos' ),
				'tabs'        => $tabs,
			),
		);
	}

	/**
	 * Register tabs.
	 */
	public function register_tabs( $tabs ) {
		$default = true;

		if ( false !== $tabs && is_array( $tabs ) && count( $tabs ) > 0 ) {
			$default = false;
		}

		if ( ! function_exists( 'tour_operator' ) ) {
			if ( ! array_key_exists( 'display', $tabs ) ) {
				$tabs['display'] = array(
					'page_title'        => '',
					'page_description'  => '',
					'menu_title'        => esc_html__( 'Display', 'lsx-videos' ),
					'template'          => LSX_VIDEOS_PATH . 'includes/settings/display.php',
					'default'           => $default,
				);

				$default = false;
			}

			if ( ! array_key_exists( 'api', $tabs ) ) {
				$tabs['api'] = array(
					'page_title'        => '',
					'page_description'  => '',
					'menu_title'        => esc_html__( 'API', 'lsx-videos' ),
					'template'          => LSX_VIDEOS_PATH . 'includes/settings/api.php',
					'default'           => $default,
				);

				$default = false;
			}
		}

		return $tabs;
	}

	/**
	 * Outputs the display tabs settings.
	 */
	public function display_settings( $tab = 'general' ) {
		if ( 'videos' === $tab ) {
			$this->disable_excerpt();
			$this->placeholder_field();
		}
	}

	/**
	 * Disable excerpt setting.
	 */
	public function disable_excerpt() {
		?>
			<tr class="form-field">
				<th scope="row">
					<label for="videos_disable_excerpt"><?php esc_html_e( 'Disable Excerpt', 'lsx-videos' ); ?></label>
				</th>
				<td>
					<input type="checkbox" {{#if videos_disable_excerpt}} checked="checked" {{/if}} name="videos_disable_excerpt">
					<small><?php esc_html_e( 'Disable Excerpt.', 'lsx-videos' ); ?></small>
				</td>
			</tr>
		<?php
	}

	/**
	 * Outputs the flag position field
	 */
	public function placeholder_field() {
		?>
			<tr class="form-field">
				<th scope="row">
					<label for="banner"><?php esc_html_e( 'Placeholder', 'lsx-videos' ); ?></label>
				</th>
				<td>
					<input class="input_image_id" type="hidden" {{#if videos_placeholder_id}} value="{{videos_placeholder_id}}" {{/if}} name="videos_placeholder_id">
					<input class="input_image" type="hidden" {{#if videos_placeholder}} value="{{videos_placeholder}}" {{/if}} name="videos_placeholder">
					<div class="thumbnail-preview">
						{{#if videos_placeholder}}<img src="{{videos_placeholder}}" width="150">{{/if}}
					</div>
					<a {{#if videos_placeholder}}style="display:none;"{{/if}} class="button-secondary lsx-thumbnail-image-add" data-slug="videos_placeholder"><?php esc_html_e( 'Choose Image', 'lsx-videos' ); ?></a>
					<a {{#unless videos_placeholder}}style="display:none;"{{/unless}} class="button-secondary lsx-thumbnail-image-delete" data-slug="videos_placeholder"><?php esc_html_e( 'Delete', 'lsx-videos' ); ?></a>
				</td>
			</tr>
		<?php
	}

	/**
	 * Change the "Insert into Post" button text when media modal is used for feature images.
	 */
	public function change_attachment_field_button( $html ) {
		if ( isset( $_GET['feature_image_text_button'] ) ) {
			$html = str_replace( 'value="Insert into Post"', sprintf( 'value="%s"', esc_html__( 'Select featured image', 'lsx-videos' ) ), $html );
		}

		return $html;
	}

	/**
	 * Change the Video post title.
	 */
	public function change_title_text( $title ) {
		$screen = get_current_screen();

		if ( 'video' === $screen->post_type ) {
			$title = esc_attr__( 'Enter video title', 'lsx-videos' );
		}

		return $title;
	}

	/**
	 * Save the video ID (and not video URL) on video meta.
	 */
	public function save_video_to_cmb( $value, $slug, $entry_id, $field, $form ) {
		global $wpdb;

		if ( 'lsx_video_video' === $slug ) {
			$media = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $value ) );

			if ( ! empty( $media ) && ! empty( $media[0] ) ) {
				return $media[0];
			}
		}

		return $value;
	}

	/**
	 * Add new column - Download video.
	 */
	public function columns_head( $defaults ) {
		$defaults['video_source'] = esc_html__( 'Video Source', 'lsx-videos' );
		$defaults['video_youtube_source'] = esc_html__( 'Youtube Source', 'lsx-videos' );
		return $defaults;
	}

	/**
	 * Show the new column - Download video.
	 */
	public function columns_content( $column_name, $post_id ) {
		if ( 'video_source' === $column_name ) {
			$video_id = get_post_meta( $post_id, 'lsx_video_video', true );

			if ( ! empty( $video_id ) ) {
				$video_url = wp_get_attachment_url( $video_id );

				if ( ! empty( $video_url ) ) {
					echo '<a href="' . esc_url( $video_url ) . '" target="_blank" class="button-secondary">' . esc_html__( 'Download', 'lsx-videos' ) . '</a>';
				} else {
					echo '-';
				}
			} else {
				echo '-';
			}
		} elseif ( 'video_youtube_source' === $column_name ) {
			$youtube_url = get_post_meta( $post_id, 'lsx_video_youtube', true );

			if ( ! empty( $youtube_url ) ) {
				echo '<a href="' . esc_url( $youtube_url ) . '" target="_blank" class="button-secondary">' . esc_html__( 'View', 'lsx-videos' ) . '</a>';
			} else {
				echo '-';
			}
		}
	}

}

global $lsx_videos_admin;
$lsx_videos_admin = new LSX_Videos_Admin();
