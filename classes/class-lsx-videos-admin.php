<?php

/**
 * LSX Videos Admin Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Admin {

	public $options = false;

	/**
	 * Construct method.
	 */
	public function __construct() {
		$this->load_classes();

		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_action( 'init', array( $this, 'tag_taxonomy_setup' ) );
		add_action( 'cmb2_admin_init', array( $this, 'field_setup' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		if ( is_admin() ) {
			add_filter( 'lsx_customizer_colour_selectors_body', array( $this, 'customizer_body_colours_handler' ), 15, 2 );
		}

		add_filter( 'type_url_form_media', array( $this, 'change_attachment_field_button' ), 20, 1 );
		add_filter( 'enter_title_here', array( $this, 'change_title_text' ) );

		add_filter( 'cf_custom_fields_pre_save_meta_key_to_post_type', array( $this, 'save_video_to_cmb' ), 10, 5 );

		// add_filter( 'manage_video_posts_columns', array( $this, 'columns_head' ), 10 );
		// add_action( 'manage_video_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
	}

	/**
	 * Loads the admin subclasses
	 */
	private function load_classes() {
		require_once LSX_VIDEOS_PATH . 'classes/admin/class-settings.php';
		$this->settings = \lsx\videos\classes\admin\Settings::get_instance();

		require_once LSX_VIDEOS_PATH . 'classes/admin/class-settings-theme.php';
		$this->settings_theme = \lsx\videos\classes\admin\Settings_Theme::get_instance();
	}

	/**
	 * Register the Video post type.
	 */
	public function post_type_setup() {
		$labels = array(
			'name'               => LSX_VIDEOS_PLURAL_NAME,
			'singular_name'      => LSX_VIDEOS_SINGULAR_NAME,
			'add_new'            => esc_html_x( 'Add New', 'post type general name', 'lsx-videos' ),
			'add_new_item'       => esc_html__( 'Add New', 'lsx-videos' ),
			'edit_item'          => esc_html__( 'Edit', 'lsx-videos' ),
			'new_item'           => esc_html__( 'New', 'lsx-videos' ),
			'all_items'          => esc_html__( 'All', 'lsx-videos' ),
			'view_item'          => esc_html__( 'View', 'lsx-videos' ),
			'search_items'       => esc_html__( 'Search', 'lsx-videos' ),
			'not_found'          => esc_html__( 'None found', 'lsx-videos' ),
			'not_found_in_trash' => esc_html__( 'None found in Trash', 'lsx-videos' ),
			'parent_item_colon'  => '',
			'menu_name'          => LSX_VIDEOS_PLURAL_NAME,
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-video-alt',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => LSX_VIDEOS_SINGLE_SLUG,
			),
			'capability_type'    => 'post',
			'has_archive'        => LSX_VIDEOS_ARCHIVE_SLUG,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'comments',
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
			'rewrite'           => array(
				'slug' => 'video-category',
			),
		);

		register_taxonomy( 'video-category', array( 'video' ), $args );
	}

	/**
	 * Register the Video Tags taxonomy.
	 */
	public function tag_taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Video Tags', 'taxonomy general name', 'lsx-videos' ),
			'singular_name'     => esc_html_x( 'Tag', 'taxonomy singular name', 'lsx-videos' ),
			'search_items'      => esc_html__( 'Search Tags', 'lsx-videos' ),
			'all_items'         => esc_html__( 'All Tags', 'lsx-videos' ),
			'parent_item'       => esc_html__( 'Parent Tag', 'lsx-videos' ),
			'parent_item_colon' => esc_html__( 'Parent Tag:', 'lsx-videos' ),
			'edit_item'         => esc_html__( 'Edit Tag', 'lsx-videos' ),
			'update_item'       => esc_html__( 'Update Tag', 'lsx-videos' ),
			'add_new_item'      => esc_html__( 'Add New Tag', 'lsx-videos' ),
			'new_item_name'     => esc_html__( 'New Tag Name', 'lsx-videos' ),
			'menu_name'         => esc_html__( 'Tags', 'lsx-videos' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'videos-tag',
			),
		);

		register_taxonomy( 'video-tag', array( 'video' ), $args );
	}

	/**
	 * Add metabox with custom fields to the Video post type.
	 */
	public function field_setup() {
		$prefix = 'lsx_video_';

		$cmb = new_cmb2_box(
			array(
				'id'           => $prefix . '_details',
				'title'        => esc_html__( 'Video Details', 'lsx-videos' ),
				'object_types' => 'video',
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);

		$cmb->add_field(
			array(
				'name'         => esc_html__( 'Featured:', 'lsx-videos' ),
				'id'           => $prefix . 'featured',
				'type'         => 'checkbox',
				'value'        => 1,
				'default'      => 0,
				'show_in_rest' => true,
			)
		);

		$cmb->add_field(
			array(
				'name'    => esc_html__( 'Video source:', 'lsx-videos' ),
				'desc'    => esc_html__( 'Allowed formats: MP4 (.mp4), WebM (.webm) and Ogg/Ogv (.ogg).', 'lsx-videos' ),
				'id'      => $prefix . 'video',
				'type'    => 'file',
				'options' => array(
					'url' => false,
				),
				'text'    => array(
					'add_upload_file_text' => esc_html__( 'Add Video', 'lsx-videos' ),
				),
				'query_args' => array(
					'type' => array(
						'video/mp4',
						'video/webm',
						'video/ogg',
					),
				),
				'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
			)
		);

		$cmb->add_field(
			array(
				'name'         => esc_html__( 'Youtube source:', 'lsx-videos' ),
				'id'           => $prefix . 'youtube',
				'type'         => 'text',
				'show_in_rest' => true,
				'desc'         => esc_html__( 'It will replace the original video source on front-end.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'         => esc_html__( 'Giphy source:', 'lsx-videos' ),
				'id'           => $prefix . 'giphy',
				'type'         => 'text',
				'show_in_rest' => true,
				'desc'         => esc_html__( 'The HTML will be stripped leaving only the URL.', 'lsx-videos' ),
			)
		);

		$cmb2 = new_cmb2_box(
			array(
				'id'           => $prefix . '_uploader_details',
				'title'        => esc_html__( 'Video Uploader Details', 'lsx-videos' ),
				'object_types' => 'video',
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);

		$cmb2->add_field(
			array(
				'name'         => esc_html__( 'First Name:', 'lsx-videos' ),
				'id'           => $prefix . 'first_name',
				'type'         => 'text',
				'show_in_rest' => true,
			)
		);

		$cmb2->add_field(
			array(
				'name'         => esc_html__( 'Last Name:', 'lsx-videos' ),
				'id'           => $prefix . 'last_name',
				'type'         => 'text',
				'show_in_rest' => true,
			)
		);

		$cmb2->add_field(
			array(
				'name'         => esc_html__( 'Email Address:', 'lsx-videos' ),
				'id'           => $prefix . 'email',
				'type'         => 'text',
				'show_in_rest' => true,
			)
		);

		$cmb2->add_field(
			array(
				'name'         => esc_html__( 'Phone Number:', 'lsx-videos' ),
				'id'           => $prefix . 'phone',
				'type'         => 'text',
				'show_in_rest' => true,
			)
		);

		$cmb2->add_field(
			array(
				'name'         => esc_html__( 'Country of Residence', 'lsx-videos' ),
				'id'           => $prefix . 'country',
				'type'         => 'text',
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Enqueue JS and CSS.
	 */
	public function assets( $hook ) {

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/js/lsx-videos-admin.min.js', array( 'jquery' ), LSX_VIDEOS_VER, true );
		wp_enqueue_style( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/css/lsx-videos-admin.css', array(), LSX_VIDEOS_VER );
	}

	/**
	 * Handle body colours that might be change by LSX Customizer.
	 */
	public function customizer_body_colours_handler( $css, $colors ) {
		$css .= '
			@import "' . LSX_VIDEOS_PATH . '/assets/css/scss/customizer-videos-body-colours";

			/**
			 * LSX Customizer - Body (LSX Videos)
			 */
			@include customizer-videos-body-colours (
				$bg: 		' . $colors['background_color'] . ',
				$breaker: 	' . $colors['body_line_color'] . ',
				$color:    	' . $colors['body_text_color'] . ',
				$link:    	' . $colors['body_link_color'] . ',
				$hover:    	' . $colors['body_link_hover_color'] . ',
				$small:    	' . $colors['body_text_small_color'] . '
			);
		';

		return $css;
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
