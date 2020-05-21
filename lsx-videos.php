<?php
/*
 * Plugin Name:	LSX Videos
 * Plugin URI:	https://lsx.lsdev.biz/extensions/videos/
 * Description:	The LSX Videos extension adds videos for LSX Theme.
 * Version:     1.2.0
 * Author:      LightSpeed
 * Author URI:  https://www.lsdev.biz/
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: lsx-videos
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'LSX_VIDEOS_PATH', plugin_dir_path( __FILE__ ) );
define( 'LSX_VIDEOS_CORE', __FILE__ );
define( 'LSX_VIDEOS_URL', plugin_dir_url( __FILE__ ) );
define( 'LSX_VIDEOS_VER', '1.2.0' );

if ( ! defined( 'LSX_VIDEOS_ARCHIVE_SLUG' ) ) {
	define( 'LSX_VIDEOS_ARCHIVE_SLUG', 'videos' );
}
if ( ! defined( 'LSX_VIDEOS_SINGLE_SLUG' ) ) {
	define( 'LSX_VIDEOS_SINGLE_SLUG', 'video' );
}
if ( ! defined( 'LSX_VIDEOS_SINGULAR_NAME' ) ) {
	define( 'LSX_VIDEOS_SINGULAR_NAME', esc_html_x( 'Video', 'post type singular name', 'lsx-videos' ) );
}
if ( ! defined( 'LSX_VIDEOS_PLURAL_NAME' ) ) {
	define( 'LSX_VIDEOS_PLURAL_NAME', __( 'Videos', 'lsx-member-directory' ) );
}

/* ======================= Below is the Plugin Class init ========================= */

require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-admin.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-frontend.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-most-recent.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-list.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-categories.php';
require_once LSX_VIDEOS_PATH . '/classes/class-lsx-videos-search.php';
require_once LSX_VIDEOS_PATH . '/includes/functions.php';
require_once LSX_VIDEOS_PATH . '/includes/post-order.php';
