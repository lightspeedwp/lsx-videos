<?php
/**
 * LSX Videos Main Class.
 *
 * @package lsx-videos
 */
class LSX_Videos {

	public $options = false;

	/**
	 * LSX_Videos_Search
	 *
	 * @var object LSX_Videos_Search()
	 */
	public $search = false;

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

		require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-search.php' );
		$this->search = LSX_Videos_Search::get_instance();

		add_filter( 'lsx_banner_allowed_post_types', array( $this, 'lsx_banner_allowed_post_types' ) );
		add_filter( 'lsx_banner_allowed_taxonomies', array( $this, 'lsx_banner_allowed_taxonomies' ) );
	}

	/**
	 * Enable video custom post type on LSX Banners.
	 */
	public function lsx_banner_allowed_post_types( $post_types ) {
		$post_types[] = 'video';
		return $post_types;
	}

	/**
	 * Enable video custom taxonomies on LSX Banners.
	 */
	public function lsx_banner_allowed_taxonomies( $taxonomies ) {
		$taxonomies[] = 'video-category';
		return $taxonomies;
	}

	/**
	 * Enable custom image sizes.
	 */
	public function custom_image_sizes( $post_types ) {
		add_image_size( 'lsx-videos-cover', 750, 350, true ); // 16:9
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'columns' => 3,
			'orderby' => 'name',
			'order' => 'ASC',
			'limit' => '-1',
			'include' => '',
			'category' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'carousel' => 'true',
			'featured' => 'false',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$include = explode( ',', $include );

			$args = array(
				'post_type' => 'video',
				'posts_per_page' => $limit,
				'post__in' => $include,
				'orderby' => 'post__in',
				'order' => $order,
			);
		} else {
			$args = array(
				'post_type' => 'video',
				'posts_per_page' => $limit,
				'orderby' => $orderby,
				'order' => $order,
			);

			if ( 'true' === $featured || true === $featured ) {
				$args['meta_key'] = 'lsx_video_featured';
				$args['meta_value'] = 1;
			}

			if ( ! empty( $category ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'video-category',
						'field' => 'slug',
						'terms' => array( $category ),
					),
				);
			}
		}

		$videos = new \WP_Query( $args );

		if ( $videos->have_posts() ) {
			global $post;

			$count = 0;
			$count_global = 0;

			if ( 'true' === $carousel || true === $carousel ) {
				$output .= '<div class="lsx-videos-shortcode lsx-videos-slider" data-slick=\'{"slidesToShow": ' . $columns . ', "slidesToScroll": ' . $columns . '}\'>';
			} else {
				$output .= '<div class="lsx-videos-shortcode"><div class="row">';
			}

			while ( $videos->have_posts() ) {
				$video_url = '';
				$videos->the_post();

				$count++;
				$count_global++;

				$youtube_url = get_post_meta( $post->ID, 'lsx_video_youtube', true );
				$video_id = get_post_meta( $post->ID, 'lsx_video_video', true );
				$views = (int) get_post_meta( $post->ID, '_views', true );

				$video_meta = get_post_meta( $video_id , '_wp_attachment_metadata', true );

				if ( ! empty( $youtube_url ) ) {
					$video_url = $youtube_url;
				} elseif ( ! empty( $video_id ) ) {
					$video_url = wp_get_attachment_url( $video_id );
				}

				if ( 'full' === $display ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
				} elseif ( 'excerpt' === $display ) {
					$content = apply_filters( 'the_excerpt', get_the_excerpt() );
				} elseif ( 'none' === $display ) {
					$content = '<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '" class="moretag">' . esc_html__( 'View video', 'lsx-videos' ) . '</a>';
				}
				$text_content = $content;
				$content = apply_filters( 'lsx_videos_widget_content', $content, $post->ID );

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				if ( ! empty( get_the_post_thumbnail( $post->ID ) ) ) {
					$image = get_the_post_thumbnail( $post->ID, $thumb_size, array(
						'class' => 'img-responsive',
					) );
				} else {
					$image = '';
				}

				if ( empty( $image ) ) {
					if ( $this->options['display'] && ! empty( $this->options['display']['videos_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['videos_placeholder'] . '" alt="placeholder">';
					} else {
						$image = '';
					}
				}

				$categories = '';
				$terms = get_the_terms( $post->ID, 'video-category' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$categories = array();

					foreach ( $terms as $term ) {
						$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
					}

					$categories = join( ', ', $categories );
				}

				$video_categories = '' !== $categories ? ( '<p class="lsx-videos-categories">' . $categories . '</p>' ) : '';

				if ( 1 !== $views ) {
					/* Translators: 1: video views */
					$meta = sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views );
				} else {
					$meta = esc_html__( '1 view', 'lsx-videos' );
				}

				if ( ! empty( $video_meta ) && ! empty( $video_meta['length_formatted'] ) ) {
					$length = $video_meta['length_formatted'];
					$meta   = $length . ' | ' . $meta;
				}

				/* Translators: 1: time ago (video published date) */
				$meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-videos' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

				if ( 'true' === $carousel || true === $carousel ) {
					$output .= '
						<div class="lsx-videos-slot">
							' . ( ! empty( $image ) ? '<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
							<h5 class="lsx-videos-title"><a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
							' . $video_categories . '
							<p class="lsx-videos-meta">' . wp_kses_post( $meta ) . '</p>
							<div class="lsx-videos-content">' . $content . $text_content . '</div>
						</div>';
				} elseif ( $columns >= 1 && $columns <= 4 ) {
					$md_col_width = 12 / $columns;

					$output .= '
						<div class="col-xs-12 col-md-' . $md_col_width . '">
							<div class="lsx-videos-slot">
								' . ( ! empty( $image ) ? '<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
								<h5 class="lsx-videos-title"><a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
								' . $video_categories . '
								<p class="lsx-videos-meta">' . wp_kses_post( $meta ) . '</p>
								<div class="lsx-videos-content">' . $content . '</div>
							</div>
						</div>';

					if ( $count == $columns && $videos->post_count > $count_global ) {
						$output .= '</div>';
						$output .= '<div class="row">';
						$count = 0;
					}
				} else {
					$output .= '
						<div class="alert alert-danger">
							' . esc_html__( 'Invalid number of columns set. LSX Videos supports 1 to 4 columns.', 'lsx-videos' ) . '
						</div>';
				}

				wp_reset_postdata();
			}

			if ( 'true' !== $carousel && true !== $carousel ) {
				$output .= '</div>';
			}

			$output .= '</div>';

			return $output;
		}
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output_most_recent( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'include' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'featured' => 'false',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$args = array(
				'post_type' => 'video',
				'posts_per_page' => 1,
				'post__in' => array( $include ),
				'orderby' => 'post__in',
				'order' => 'DESC',
			);
		} else {
			$args = array(
				'post_type' => 'video',
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
			);

			if ( 'true' === $featured || true === $featured ) {
				$args['meta_key'] = 'lsx_video_featured';
				$args['meta_value'] = 1;
			}
		}

		$videos = new \WP_Query( $args );

		if ( $videos->have_posts() ) {
			global $post;

			$output .= '<div class="lsx-videos-shortcode lsx-videos-most-recent-shortcode"><div class="row">';

			while ( $videos->have_posts() ) {
				$videos->the_post();

				$youtube_url = get_post_meta( $post->ID, 'lsx_video_youtube', true );
				$video_id = get_post_meta( $post->ID, 'lsx_video_video', true );
				$views = (int) get_post_meta( $post->ID, '_views', true );

				$video_meta = get_post_meta( $video_id , '_wp_attachment_metadata', true );

				if ( ! empty( $youtube_url ) ) {
					$video_url = $youtube_url;
				} elseif ( ! empty( $video_id ) ) {
					$video_url = wp_get_attachment_url( $video_id );
				}

				if ( 'full' === $display ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
				} elseif ( 'excerpt' === $display ) {
					$content = apply_filters( 'the_excerpt', get_the_excerpt() );
				} elseif ( 'none' === $display ) {
					$content = '<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '" class="moretag">' . esc_html__( 'View video', 'lsx-videos' ) . '</a>';
				}

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				if ( ! empty( get_the_post_thumbnail( $post->ID ) ) ) {
					$image = get_the_post_thumbnail( $post->ID, $thumb_size, array(
						'class' => 'img-responsive',
					) );
				} else {
					$image = '';
				}

				if ( empty( $image ) ) {
					if ( ! empty( $this->options['display'] ) && ! empty( $this->options['display']['videos_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['videos_placeholder'] . '" alt="placeholder">';
					}
				}

				$categories = '';
				$terms = get_the_terms( $post->ID, 'video-category' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$categories = array();

					foreach ( $terms as $term ) {
						$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
					}

					$categories = join( ', ', $categories );
				}

				$video_categories = '' !== $categories ? ( '<p class="lsx-videos-categories">' . $categories . '</p>' ) : '';

				if ( 1 !== $views ) {
					/* Translators: 1: video views */
					$meta = sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views );
				} else {
					$meta = esc_html__( '1 view', 'lsx-videos' );
				}

				if ( ! empty( $video_meta ) && ! empty( $video_meta['length_formatted'] ) ) {
					$length = $video_meta['length_formatted'];
					$meta = $length . ' | ' . $meta;
				}

				/* Translators: 1: time ago (video published date) */
				$meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-videos' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

				$output .= '
					<div class="col-xs-12 col-md-6">
						<div class="lsx-videos-slot-single">
							' . ( ! empty( $image ) ? '<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="lsx-videos-slot-single">
							<h5 class="lsx-videos-title"><a href="#lsx-videos-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-video="' . esc_url( $video_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
							' . $video_categories . '
							<p class="lsx-videos-meta">' . wp_kses_post( $meta ) . '</p>
							<div class="lsx-videos-content">' . $content . '</div>
						</div>
					</div>';

				wp_reset_postdata();
			}

			$output .= '</div></div>';

			return $output;
		}
	}

	/**
	 * Returns the related output markup.
	 */
	public function output_most_recent_related( $post_id ) {
		$output = '';
		$post_terms    = get_the_terms( $post_id, 'video-category' );
		$post_category = array_pop( $post_terms );
		$args = array(
			'post__not_in'   => array( $post_id ),
			'post_type'      => 'video',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'video-category',
					'field'    => 'slug',
					'terms'    => $post_category->name,
				),
			),
		);
		$videos = new \WP_Query( $args );
		if ( $videos->have_posts() ) {
			global $post;
			$output .= '<div class="lsx-videos-most-recent-related"><div class="row">';
			$output .= '<h2 class="lsx-title">' . esc_html( 'Related videos', 'lsx-videos' ) . '</h2>';
			$output .= '<div class="row row-flex lsx-related-videos-row">';
			while ( $videos->have_posts() ) {
				$videos->the_post();
				$youtube_url = get_post_meta( $post->ID, 'lsx_video_youtube', true );
				$video_id    = get_post_meta( $post->ID, 'lsx_video_video', true );
				$views       = (int) get_post_meta( $post->ID, '_views', true );
				$video_meta = get_post_meta( $video_id, '_wp_attachment_metadata', true );
				if ( ! empty( $youtube_url ) ) {
					$video_url = $youtube_url;
				} elseif ( ! empty( $video_id ) ) {
					$video_url = wp_get_attachment_url( $video_id );
				}
				$content = '<a href="' . get_the_permalink() . '" class="moretag">' . esc_html__( 'View video', 'lsx-videos' ) . '</a>';
				if ( ! empty( get_the_post_thumbnail( $post->ID ) ) ) {
					$image = get_the_post_thumbnail( $post->ID, 'lsx-videos-cover', array(
						'class' => 'img-responsive',
					) );
				} else {
					$image = '';
				}
				if ( empty( $image ) ) {
					if ( ! empty( $this->options['display'] ) && ! empty( $this->options['display']['videos_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['videos_placeholder'] . '" alt="placeholder">';
					}
				}
				$categories = '';
				$terms      = get_the_terms( $post->ID, 'video-category' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$categories = array();
					foreach ( $terms as $term ) {
						$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
					}
					$categories = join( ', ', $categories );
				}
				$video_categories = '' !== $categories ? ( '<p class="lsx-videos-categories">' . $categories . '</p>' ) : '';
				if ( 1 !== $views ) {
					/* Translators: 1: video views */
					$meta = sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views );
				} else {
					$meta = esc_html__( '1 view', 'lsx-videos' );
				}
				if ( ! empty( $video_meta ) && ! empty( $video_meta['length_formatted'] ) ) {
					$length = $video_meta['length_formatted'];
					$meta = $length . ' | ' . $meta;
				}
				/* Translators: 1: time ago (video published date) */
				$meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-videos' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
				$output .= '
					<div class="col-xs-12 col-sm-4 col-md-4 lsx-related-videos-column">
						<article class="lsx-videos-slot">
							' . ( ! empty( $image ) ? '<a href="' . get_the_permalink() . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
							<h5 class="lsx-videos-title"><a href="' . get_the_permalink() . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
							' . $video_categories . '
							<p class="lsx-videos-meta">' . wp_kses_post( $meta ) . '</p>
							<div class="lsx-videos-content">' . $content . '</div>
						</article>
					</div>';
				wp_reset_postdata();
			}
			$output .= '</div></div></div>';
			return $output;
		}
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output_categories( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'columns' => 3,
			'orderby' => 'name',
			'order' => 'ASC',
			'limit' => '-1',
			'include' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'carousel' => 'true',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$include = explode( ',', $include );

			$args = array(
				'taxonomy' => 'video-category',
				'number' => (int) $limit,
				'include' => $include,
				'orderby' => 'include',
				'order' => $order,
				'hide_empty' => 0,
			);
		} else {
			$args = array(
				'taxonomy' => 'video-category',
				'number' => (int) $limit,
				'orderby' => $orderby,
				'order' => $order,
				'hide_empty' => 0,
			);
		}

		if ( 'none' !== $orderby ) {
			$args['suppress_filters']           = true;
			$args['disabled_custom_post_order'] = true;
		}

		$video_categories = get_terms( $args );

		if ( ! empty( $video_categories ) && ! is_wp_error( $video_categories ) ) {
			global $post;

			$count = 0;
			$count_global = 0;

			if ( 'true' === $carousel || true === $carousel ) {
				$output .= '<div class="lsx-videos-shortcode lsx-videos-slider" data-slick=\'{"slidesToShow": ' . $columns . ', "slidesToScroll": ' . $columns . '}\'>';
			} else {
				$output .= '<div class="lsx-videos-shortcode"><div class="row">';
			}

			foreach ( $video_categories as $term ) {
				$count++;
				$count_global++;

				$content = '<p><a href="' . get_term_link( $term, 'video-category' ) . '" class="moretag">' . esc_html__( 'View more', 'lsx-videos' ) . '</a></p>';

				if ( 'none' !== $display ) {
					$content = apply_filters( 'term_description', $term->description ) . $content;
				}

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				$term_image_id = get_term_meta( $term->term_id, 'thumbnail', true );
				$image = wp_get_attachment_image_src( $term_image_id, $thumb_size );

				if ( ! empty( $image ) ) {
					$image = '<img class="img-responsive" src="' . $image[0] . '" alt="' . $term->name . '">';
				} else {
					if ( $this->options['display'] && ! empty( $this->options['display']['videos_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['videos_placeholder'] . '" alt="placeholder">';
					} else {
						$image = '';
					}
				}

				if ( 'true' === $carousel || true === $carousel ) {
					$output .= '
						<div class="lsx-videos-slot">
							' . ( ! empty( $image ) ? '<a href="' . get_term_link( $term, 'video-category' ) . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
							<h5 class="lsx-videos-title"><a href="' . get_term_link( $term, 'video-category' ) . '">' . apply_filters( 'the_title', $term->name ) . '</a></h5>
							<div class="lsx-videos-content">' . $content . '</div>
						</div>';
				} elseif ( $columns >= 1 && $columns <= 4 ) {
					$md_col_width = 12 / $columns;

					$output .= '
						<div class="col-xs-12 col-md-' . $md_col_width . '">
							<div class="lsx-videos-slot">
								' . ( ! empty( $image ) ? '<a href="' . get_term_link( $term, 'video-category' ) . '"><figure class="lsx-videos-avatar">' . $image . '</figure></a>' : '' ) . '
								<h5 class="lsx-videos-title"><a href="' . get_term_link( $term, 'video-category' ) . '">' . apply_filters( 'the_title', $term->name ) . '</a></h5>
								<div class="lsx-videos-content">' . $content . '</div>
							</div>
						</div>';

					if ( $count == $columns && $videos->post_count > $count_global ) {
						$output .= '</div>';
						$output .= '<div class="row">';
						$count = 0;
					}
				} else {
					$output .= '
						<div class="alert alert-danger">
							' . esc_html__( 'Invalid number of columns set. LSX Videos supports 1 to 4 columns.', 'lsx-videos' ) . '
						</div>';
				}

				wp_reset_postdata();
			}

			if ( 'true' !== $carousel && true !== $carousel ) {
				$output .= '</div>';
			}

			$output .= '</div>';

			return $output;
		}
	}

}

global $lsx_videos;
$lsx_videos = new LSX_Videos();
