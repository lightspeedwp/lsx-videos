<?php
/**
 * Contains the settings class for LSX
 *
 * @package lsx-videos
 */

namespace lsx\videos\classes\admin;

class Settings {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_videos\classes\admin\Settings()
	 */
	protected static $instance = null;

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 */
	protected $screen_id = 'lsx_videos_settings';

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'register_settings_page' ) );
		add_action( 'lsx_videos_settings_page', array( $this, 'general_settings' ), 1, 1 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object Settings()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook in and register a submenu options page for the Page post-type menu.
	 */
	public function register_settings_page() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->screen_id,
				'title'        => esc_html__( 'Settings', 'lsx-videos' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'lsx_videos_options', // The option key and admin menu page slug.
				'parent_slug'  => 'edit.php?post_type=video', // Make options page a submenu item of the themes menu.
				'capability'   => 'manage_options', // Cap required to view options-page.
			)
		);
		do_action( 'lsx_videos_settings_page', $cmb );
	}

	/**
	 * Registers the general settings.
	 *
	 * @param object $cmb new_cmb2_box().
	 * @return void
	 */
	public function general_settings( $cmb ) {
		$cmb->add_field(
			array(
				'id'      => 'settings_general_title',
				'type'    => 'title',
				'name'    => __( 'General', 'lsx-videos' ),
				'default' => __( 'General', 'lsx-videos' ),
			)
		);
		$cmb->add_field(
			array(
				'name'        => __( 'Restrict Archive', 'lsx-videos' ),
				'id'          => 'videos_restrict_archive',
				'type'        => 'checkbox',
				'value'       => 1,
				'default'     => 0,
				'description' => __( 'A user will need to have purchase a membershihp plan to view the archive.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'        => __( 'Disable Excerpt', 'lsx-videos' ),
				'id'          => 'videos_disable_excerpt',
				'type'        => 'checkbox',
				'value'       => 1,
				'default'     => 0,
				'description' => __( 'Disable Excerpt.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'        => __( 'Disable Modal', 'lsx-videos' ),
				'id'          => 'videos_disable_modal',
				'type'        => 'checkbox',
				'value'       => 1,
				'default'     => 0,
				'description' => __( 'Disable Modal.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'        => __( 'Disable Single Video Related', 'lsx-videos' ),
				'id'          => 'single_video_disable_related',
				'type'        => 'checkbox',
				'value'       => 1,
				'default'     => 0,
				'description' => __( 'Disable Single Video Related.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'        => __( 'Disable Single Video Post Nav', 'lsx-videos' ),
				'id'          => 'single_video_disable_post_nav',
				'type'        => 'checkbox',
				'value'       => 1,
				'default'     => 0,
				'description' => __( 'Disable Single Video Post Nav.', 'lsx-videos' ),
			)
		);

		$cmb->add_field(
			array(
				'name'    => 'Placeholder',
				'desc'    => __( 'Upload an image.', 'lsx-videos' ),
				'id'      => 'videos_placeholder',
				'type'    => 'file',
				'options' => array(
					'url' => false, // Hide the text input for the url.
				),
				'text'    => array(
					'add_upload_file_text' => 'Choose Image',
				),
			)
		);

		$cmb->add_field(
			array(
				'id'   => 'settings_general_closing',
				'type' => 'tab_closing',
			)
		);
	}

}
