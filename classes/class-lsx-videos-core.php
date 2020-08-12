<?php

/**
 * This class loads the other classes and function files
 *
 * @package lsx-videos
 */
class Core {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_videos\classes\Core()
	 */
	protected static $instance = null;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \MAG_CMB2_Field_Post_Search_Ajax()
	 */
	public $cmb2_post_search_ajax = false;

	/**
	 * @var object \lsx_videos\classes\Admin();
	 */
	public $admin;

	/**
	 * @var object \lsx_videos\classes\Frontend();
	 */
	public $frontend;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'cmb2_post_search_ajax' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_videos\classes\Core()    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Returns the post types currently active
	 *
	 * @return void
	 */
	public function get_post_types() {
		$post_types = apply_filters( 'lsx_videos_post_types', isset( $this->post_types ) );
		foreach ( $post_types as $index => $post_type ) {
			$is_disabled = \cmb2_get_option( 'lsx_videos_options', $post_type . '_disabled', false );
			if ( true === $is_disabled || 1 === $is_disabled || 'on' === $is_disabled ) {
				unset( $post_types[ $index ] );
			}
		}
		return $post_types;
	}

	/**
	 * Includes the Post Search Ajax if it is there.
	 *
	 * @return void
	 */
	public function cmb2_post_search_ajax() {
		require_once LSX_TEAM_PATH . 'vendor/lsx-field-post-search-ajax/cmb-field-post-search-ajax.php';
		$this->cmb2_post_search_ajax = new \MAG_CMB2_Field_Post_Search_Ajax();
	}
}
