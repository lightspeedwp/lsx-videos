<?php
/**
 * LSX Videos Main Class.
 *
 * @package lsx-videos
 */
class LSX_Videos {

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
		add_image_size( 'lsx-videos-cover', 765, 420, true ); // 16:9
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output( $atts ) {}

}

global $lsx_videos;
$lsx_videos = new LSX_Videos();
