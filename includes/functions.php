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
