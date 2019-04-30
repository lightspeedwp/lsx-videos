<?php
/*
 * Plugin Name:	LSX Videos
 * Plugin URI:	https://github.com/lightspeeddevelopment/lsx-videos
 * Description:	LSX Videos for LSX Theme.
 * Version:     1.0.8
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
define( 'LSX_VIDEOS_URL',  plugin_dir_url( __FILE__ ) );
define( 'LSX_VIDEOS_VER',  '1.0.8' );

/* ======================= Below is the Plugin Class init ========================= */

require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos.php' );
require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-admin.php' );
require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-frontend.php' );
require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-most-recent.php' );
require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-list.php' );
require_once( LSX_VIDEOS_PATH . '/classes/class-lsx-videos-widget-categories.php' );
require_once( LSX_VIDEOS_PATH . '/includes/functions.php' );
require_once( LSX_VIDEOS_PATH . '/includes/post-order.php' );
