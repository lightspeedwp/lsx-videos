<?php

/**
 * LSX Videos Frontend Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Frontend {

	public $options = false;

	/**
	 * Construct method.
	 */
	public function __construct() {
		if ( function_exists( 'tour_operator' ) ) {
			$this->options = get_option( '_lsx-to_settings', false );
		} else {
			$this->options = get_option( '_lsx_settings', false );

			if ( false === $this->options ) {
				$this->options = get_option( '_lsx_lsx-settings', false );
			}
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 5 );
		add_action( 'wp_footer', array( $this, 'add_video_modal' ) );

		add_action( 'wp_ajax_get_video_embed', array( $this, 'get_video_embed' ) );
		add_action( 'wp_ajax_nopriv_get_video_embed', array( $this, 'get_video_embed' ) );

		add_filter( 'wp_kses_allowed_html', array( $this, 'wp_kses_allowed_html' ), 10, 2 );
		add_filter( 'template_include', array( $this, 'archive_template_include' ), 99 );
		add_filter( 'template_include', array( $this, 'single_template_include' ), 99 );

		// LSX.
		add_filter( 'lsx_global_header_disable', array( $this, 'lsx_videos_disable_banner' ) );
		// LSX Banners - Banner.
		add_filter( 'lsx_banner_disable', array( $this, 'lsx_videos_disable_lsx_banner' ) );

		add_filter( 'lsx_banner_title', array( $this, 'lsx_banner_archive_title' ), 15 );

		add_filter( 'excerpt_more_p', array( $this, 'change_excerpt_more' ) );
		add_filter( 'excerpt_length', array( $this, 'change_excerpt_length' ) );
		add_filter( 'excerpt_strip_tags', array( $this, 'change_excerpt_strip_tags' ) );

		add_action( 'lsx_content_top', array( $this, 'categories_tabs' ), 15 );

		add_filter( 'lsx_global_header_title', array( $this, 'lsx_videos_archives_header_title' ), 200, 1 );
		add_filter( 'lsx_banner_container_top', array( $this, 'lsx_videos_archives_header_title' ) );
	}

	/**
	 * Enqueue JS and CSS.
	 */
	public function assets() {
		$has_slick = wp_script_is( 'slick.min.js', 'queue' );

		if ( ! $has_slick ) {
			wp_enqueue_style( 'slick', LSX_VIDEOS_URL . 'assets/css/vendor/slick.css', array(), LSX_VIDEOS_URL, null );
			wp_enqueue_script( 'slick', LSX_VIDEOS_URL . 'assets/js/vendor/slick.min.js', array( 'jquery' ), null );
		}

		wp_enqueue_script( 'lsx-videos-js', LSX_VIDEOS_URL . 'assets/js/lsx-videos.min.js', array( 'jquery', 'slick' ), null );

		$params = apply_filters( 'lsx_videos_js_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));

		wp_localize_script( 'lsx-videos-js', 'lsx_videos_params', $params );

		wp_enqueue_style( 'lsx-videos', LSX_VIDEOS_URL . 'assets/css/lsx-videos.css', array(), null );
		wp_style_add_data( 'lsx-videos', 'rtl', 'replace' );
	}

	/**
	 * Add video modal.
	 */
	public function add_video_modal() {
		?>
		<div class="lsx-modal modal fade" id="lsx-videos-modal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="modal-body"></div>
					<div class="modal-header">
						<h4 class="modal-title"></h4>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get video embed (ajax).
	 */
	public function get_video_embed() {
		if ( ! empty( $_GET['video'] ) && ! empty( $_GET['post_id'] ) ) {
			$video = sanitize_text_field( wp_unslash( $_GET['video'] ) );
			$post_id = sanitize_text_field( wp_unslash( $_GET['post_id'] ) );

			if ( ! empty( $video ) && ! empty( $post_id ) ) {
				$this->increase_views_counter( $post_id );
				$video_parts = parse_url( $video );

				echo '<div class="embed-responsive embed-responsive-16by9">';

				if ( in_array( $video_parts['host'], array( 'www.youtube.com', 'youtube.com', 'youtu.be' ) ) ) {
					// @codingStandardsIgnoreLine
					echo wp_oembed_get( $video, array(
						'height' => 558,
						'width' => 992,
					) );
				} else {
					echo do_shortcode( '[video width="992" height="558" src="' . $video . '"]' );
				}

				echo '</div>';
			}
		}

		wp_die();
	}

	/**
	 * Increase video views counter.
	 */
	public function increase_views_counter( $post_id ) {
		$count = (int) get_post_meta( $post_id, '_views', true );
		$count++;
		update_post_meta( $post_id, '_views', $count );
	}

	/**
	 * Allow data params for Slick slider addon.
	 * Allow data params for Bootstrap modal.
	 */
	public function wp_kses_allowed_html( $allowedtags, $context ) {
		$allowedtags['div']['data-slick'] = true;
		$allowedtags['a']['data-toggle'] = true;
		$allowedtags['a']['data-video'] = true;
		$allowedtags['a']['data-post-id'] = true;
		$allowedtags['a']['data-title'] = true;
		return $allowedtags;
	}

	/**
	 * Archive template.
	 */
	public function archive_template_include( $template ) {
		if ( is_main_query() && ( is_post_type_archive( 'video' ) || is_tax( 'video-category' ) ) ) {
			if ( empty( locate_template( array( 'archive-video.php' ) ) ) && file_exists( LSX_VIDEOS_PATH . 'templates/archive-video.php' ) ) {
				$template = LSX_VIDEOS_PATH . 'templates/archive-video.php';
			}
		}
		return $template;
	}

	/**
	 * Disable LSX Banners in some Video pages.
	 *
	 * @package    lsx
	 * @subpackage sensei
	 */
	public function lsx_videos_disable_lsx_banner( $disabled ) {
		if ( is_singular( 'video' ) ) {
			$disabled = true;
		}
		return $disabled;
	}

	/**
	 * Single template.
	 */
	public function single_template_include( $template ) {
		if ( is_main_query() && is_singular( 'video' ) ) {
			if ( empty( locate_template( array( 'single-video.php' ) ) ) && file_exists( LSX_VIDEOS_PATH . 'templates/single-video.php' ) ) {
				$template = LSX_VIDEOS_PATH . 'templates/single-video.php';
			}
		}
		return $template;
	}

	/**
	 * Change the LSX Banners title for videos archive.
	 */
	public function lsx_banner_archive_title( $title ) {
		if ( is_main_query() && is_post_type_archive( 'video' ) ) {
			$title = '<h1 class="page-title">' . esc_html__( 'Videos', 'lsx-videos' ) . '</h1>';
		}

		if ( is_main_query() && is_tax( 'video-category' ) ) {
			$tax = get_queried_object();
			$title = '<h1 class="page-title">' . esc_html__( 'Videos Category', 'lsx-videos' ) . ': ' . apply_filters( 'the_title', $tax->name ) . '</h1>';
		}

		return $title;
	}

	/**
	 * Remove the "continue reading".
	 */
	public function change_excerpt_more( $excerpt_more ) {
		global $post;

		if ( 'video' === $post->post_type ) {
			$youtube_url = get_post_meta( $post->ID, 'lsx_video_youtube', true );
			$video_id = get_post_meta( $post->ID, 'lsx_video_video', true );

			if ( ! empty( $youtube_url ) ) {
				$video_url = $youtube_url;
			} elseif ( ! empty( $video_id ) ) {
				$video_url = wp_get_attachment_url( $video_id );
			}

			$excerpt_more = '<p><a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . the_title( '', '', false ) . '" class="moretag">' . esc_html__( 'View video', 'lsx' ) . '</a></p>';
		}

		return $excerpt_more;
	}

	/**
	 * Change the word count when crop the content to excerpt.
	 */
	public function change_excerpt_length( $excerpt_word_count ) {
		global $post;

		if ( is_front_page() && 'video' === $post->post_type ) {
			$excerpt_word_count = 20;
		}

		if ( is_singular( 'video' ) ) {
			$excerpt_word_count = 20;
		}

		return $excerpt_word_count;
	}

	/**
	 * Change the allowed tags crop the content to excerpt.
	 */
	public function change_excerpt_strip_tags( $allowed_tags ) {
		global $post;

		if ( is_front_page() && 'video' === $post->post_type ) {
			$allowed_tags = '<p>,<br>,<b>,<strong>,<i>,<u>,<ul>,<ol>,<li>,<span>';
		}

		if ( is_singular( 'video' ) ) {
			$allowed_tags = '<p>,<br>,<b>,<strong>,<i>,<u>,<ul>,<ol>,<li>,<span>';
		}

		return $allowed_tags;
	}

	/**
	 * Display categories tabs.
	 */
	public function categories_tabs() {
		if ( is_post_type_archive( 'video' ) ) :
			$args = array(
				'taxonomy'   => 'video-category',
				'hide_empty' => false,
			);

			$categories = get_terms( $args );
			$category_selected = get_query_var( 'video-category' );

			if ( count( $categories ) > 0 ) :
				?>

				<ul class="nav nav-tabs lsx-videos-filter">
					<?php
						$category_selected_class = '';

						if ( empty( $category_selected ) ) {
							$category_selected_class = ' class="active"';
						}
					?>

					<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo esc_url( get_post_type_archive_link( 'video' ) ); ?>" data-filter="*"><?php esc_html_e( 'All', 'lsx-videos' ); ?></a></li>

					<?php foreach ( $categories as $category ) : ?>
						<?php
							$category_selected_class = '';

							if ( (string) $category_selected === (string) $category->slug ) {
								$category_selected_class = ' class="active"';
							}
						?>

						<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo esc_url( get_term_link( $category ) ); ?>" data-filter=".filter-<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_attr( $category->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>

				<?php
			endif;
		endif;
	}

	/**
	 * Titles For video archive pages
	 *
	 */
	public function lsx_videos_archives_header_title( $title ) {
		if ( is_archive() && is_post_type_archive( 'video' ) ) {

			$title = ' All Videos ';
		}
		return $title;
	}

}

global $lsx_videos_frontend;
$lsx_videos_frontend = new LSX_Videos_Frontend();
