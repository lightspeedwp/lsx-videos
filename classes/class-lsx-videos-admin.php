<?php
/**
 * LSX Videos Admin Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Admin {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	public function assets() {
		//wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/js/lsx-videos-admin.min.js', array( 'jquery' ), LSX_VIDEOS_VER, true );
		wp_enqueue_style( 'lsx-videos-admin', LSX_VIDEOS_URL . 'assets/css/lsx-videos-admin.css', array(), LSX_VIDEOS_VER );
	}

}

global $lsx_videos_admin;
$lsx_videos_admin = new LSX_Videos_Admin();
