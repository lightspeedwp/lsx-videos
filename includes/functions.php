<?php
/**
 * LSX Videos functions.
 *
 * @package lsx-videos
 */

/**
 * Adds text domain.
 */
function lsx_videos_load_plugin_textdomain() {
	load_plugin_textdomain( 'lsx-videos', false, basename( LSX_VIDEOS_PATH ) . '/languages' );
}
add_action( 'init', 'lsx_videos_load_plugin_textdomain' );

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_videos( $args ) {
	$lsx_videos = new LSX_Videos;
	echo wp_kses_post( $lsx_videos->output( $args ) );
}

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_videos_most_recent( $args ) {
	$lsx_videos = new LSX_Videos;
	echo wp_kses_post( $lsx_videos->output_most_recent( $args ) );
}

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_videos_categories( $args ) {
	$lsx_videos = new LSX_Videos;
	echo wp_kses_post( $lsx_videos->output_categories( $args ) );
}

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_videos_most_recent_related( $post_id ) {
	$lsx_videos = new LSX_Videos;
	echo wp_kses_post( $lsx_videos->output_most_recent_related( $post_id ) );
}

/**
 * Shortcode [lsx_videos].
 */
function lsx_videos_shortcode( $atts ) {
	$lsx_videos = new LSX_Videos;
	return $lsx_videos->output( $atts );
}
add_shortcode( 'lsx_videos', 'lsx_videos_shortcode' );

/**
 * Shortcode [lsx_videos_most_recent].
 */
function lsx_videos_most_recent_shortcode( $atts ) {
	$lsx_videos = new LSX_Videos;
	return $lsx_videos->output_most_recent( $atts );
}
add_shortcode( 'lsx_videos_most_recent', 'lsx_videos_most_recent_shortcode' );

/**
 * Shortcode [lsx_videos_categories].
 */
function lsx_videos_categories_shortcode( $atts ) {
	$lsx_videos = new LSX_Videos;
	return $lsx_videos->output_categories( $atts );
}
add_shortcode( 'lsx_videos_categories', 'lsx_videos_categories_shortcode' );

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function videos_get_options() {
	$options = array();
	if ( function_exists( 'tour_operator' ) ) {
		$options = get_option( '_lsx-to_settings', false );
	} else {
		$options = get_option( '_lsx_settings', false );

		if ( false === $options ) {
			$options = get_option( '_lsx_lsx-settings', false );
		}
	}

	// If there are new CMB2 options available, then use those.
	$new_options = get_option( 'lsx_videos_options', false );
	if ( false !== $new_options ) {
		$options['display'] = $new_options;
	}
	return $options;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function videos_get_option( $key = '', $default = false ) {
	$options = array();
	$value   = $default;
	if ( function_exists( 'tour_operator' ) ) {
		$options = get_option( '_lsx-to_settings', false );
	} else {
		$options = get_option( '_lsx_settings', false );

		if ( false === $options ) {
			$options = get_option( '_lsx_lsx-settings', false );
		}
	}

	// If there are new CMB2 options available, then use those.
	$new_options = get_option( 'lsx_videos_options', false );
	if ( false !== $new_options ) {
		$options['display'] = $new_options;
	}

	if ( isset( $options['display'] ) && isset( $options['display'][ $key ] ) ) {
		$value = $options['display'][ $key ];
	}
	return $value;
}
