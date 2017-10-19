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
