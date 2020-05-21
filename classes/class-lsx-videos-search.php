<?php
/**
 * Class Core
 * @package lsx-videos
 */
class LSX_Videos_Search {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object lsx-videos\Search()
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct() {
		add_filter( 'lsx_search_post_types', array( $this, 'enable_post_type' ), 100, 1 );
		add_filter( 'lsx_search_post_types_plural', array( $this, 'post_type_plural' ), 100, 1 );
		add_filter( 'lsx_search_categories', array( $this, 'enable_categories' ), 100, 1 );
		add_filter( 'lsx_search_enabled', array( $this, 'enable_categories_search' ), 100, 1 );
		add_filter( 'lsx_search_prefix', array( $this, 'set_search_prefix' ), 100, 1 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object lsx-videos\Search()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @param array $post_types
	 *
	 * @return array
	 */
	public function enable_post_type( $post_types = array() ) {
		$post_types[] = 'video';
		return $post_types;
	}

	/**
	 * @param array $post_types
	 *
	 * @return array
	 */
	public function post_type_plural( $post_types = array() ) {
		$post_types['video'] = 'videos';
		return $post_types;
	}

	/**
	 * @param array $categories
	 *
	 * @return array
	 */
	public function enable_categories( $categories = array() ) {
		return array_merge( $categories, array( 'video-category' ) );
	}

	/**
	 * Enabled the search for the categories
	 * @param $prefix
	 *
	 * @return string
	 */
	public function set_search_prefix( $prefix ) {
		if ( is_tax( 'video-category' ) ) {
			$prefix = 'video_archive';
		}
		return $prefix;
	}

	/**
	 * Enabled the search for the categories
	 * @param $enabled
	 *
	 * @return string
	 */
	public function enable_categories_search( $enabled ) {
		if ( is_tax( 'video-category' ) ) {
			$enabled = false;
		}
		if ( isset( $this->options['display'][ $this->video_archive_enable_search ] ) && ( ! empty( $this->options ) ) && 'on' === $this->options['display'][ $this->video_archive_enable_search ] ) {
			$enabled = true;
		}
		return $enabled;
	}
}
