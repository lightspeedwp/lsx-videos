<?php
/**
 * LSX Videos Frontend Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Frontend {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 999 );
	}

	public function assets() {
		wp_enqueue_script( 'lsx-videos', LSX_VIDEOS_URL . 'assets/js/lsx-videos.min.js', array( 'jquery' ), LSX_VIDEOS_VER, true );

		$params = apply_filters( 'lsx_videos_js_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));

		wp_localize_script( 'lsx-videos', 'lsx_customizer_params', $params );

		wp_enqueue_style( 'lsx-videos', LSX_VIDEOS_URL . 'assets/css/lsx-videos.css', array(), LSX_VIDEOS_VER );
		wp_style_add_data( 'lsx-videos', 'rtl', 'replace' );
	}

}

global $lsx_videos_frontend;
$lsx_videos_frontend = new LSX_Videos_Frontend();
