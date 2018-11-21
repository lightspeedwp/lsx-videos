<?php
/**
 * LSX Videos Widget (Most Recent) Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Widget_Most_Recent extends \WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'lsx-videos lsx-videos-most-recent',
		);

		parent::__construct( 'LSX_Videos_Widget_Most_Recent', esc_html__( 'LSX Videos - Most Recent', 'lsx-videos' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		// @codingStandardsIgnoreLine
		extract( $args );

		$title = $instance['title'];
		$title_link = $instance['title_link'];
		$tagline = $instance['tagline'];
		$button_text = $instance['button_text'];
		$include = $instance['include'];
		$display = $instance['display'];
		$size = $instance['size'];
		$featured = $instance['featured'];

		if ( '1' == $featured ) {
			$featured = 'true';
		} else {
			$featured = 'false';
		}

		if ( $title_link ) {
			//$link_open = '<a href="' . $title_link . '">';
			$link_open = '';
			$link_btn_open = '<a href="' . $title_link . '" class="btn border-btn">';
			//$link_close = '</a>';
			$link_close = '';
			$link_btn_close = '</a>';
		} else {
			$link_open = '';
			$link_btn_open = '';
			$link_close = '';
			$link_btn_close = '';
		}

		echo wp_kses_post( $before_widget );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $before_title . $link_open . $title );

			if ( ! empty( $tagline ) ) {
				echo '<small>' . esc_html( $tagline ) . '</small>';
			}

			echo wp_kses_post( $link_close . $after_title );
		}

		lsx_videos_most_recent( array(
			'include' => $include,
			'display' => $display,
			'size' => $size,
			'featured' => $featured,
		) );

		if ( $button_text && $title_link ) {
			echo wp_kses_post( '<p class="text-center lsx-videos-archive-link-wrap"><span class="lsx-videos-archive-link">' . $link_btn_open . $button_text . ' <i class="fa fa-angle-right"></i>' . $link_btn_close . '</span></p>' );
		}

		echo wp_kses_post( $after_widget );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_kses_post( force_balance_tags( $new_instance['title'] ) );
		$instance['title_link'] = strip_tags( $new_instance['title_link'] );
		$instance['tagline'] = wp_kses_post( force_balance_tags( $new_instance['tagline'] ) );
		$instance['button_text'] = strip_tags( $new_instance['button_text'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['display'] = strip_tags( $new_instance['display'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['featured'] = strip_tags( $new_instance['featured'] );

		return $instance;
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => esc_html__( 'Most Recent Video', 'lsx-videos' ),
			'title_link' => '',
			'tagline' => '',
			'button_text' => '',
			'include' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'featured' => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = esc_attr( $instance['title'] );
		$title_link = esc_attr( $instance['title_link'] );
		$tagline = esc_attr( $instance['tagline'] );
		$button_text = esc_attr( $instance['button_text'] );
		$include = esc_attr( $instance['include'] );
		$display = esc_attr( $instance['display'] );
		$size = esc_attr( $instance['size'] );
		$featured = esc_attr( $instance['featured'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_link' ) ); ?>"><?php esc_html_e( 'Page Link:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_link' ) ); ?>" type="text" value="<?php echo esc_attr( $title_link ); ?>" />
			<small><?php esc_html_e( 'Link the widget to a page', 'lsx-videos' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tagline' ) ); ?>"><?php esc_html_e( 'Tagline:', 'lsx-videos' ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'tagline' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tagline' ) ); ?>"><?php echo esc_html( $tagline ); ?></textarea>
			<small><?php esc_html_e( 'Tagline to display below the widget title', 'lsx-videos' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button "view all" text:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" />
			<small><?php esc_html_e( 'Leave empty to not display the button', 'lsx-videos' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"><?php esc_html_e( 'Specify Video by ID:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><?php esc_html_e( 'Display:', 'lsx-videos' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" class="widefat">
			<?php
				$options = array(
					esc_html__( 'Excerpt', 'lsx-videos' ) => 'excerpt',
					esc_html__( 'Full Content', 'lsx-videos' ) => 'full',
					esc_html__( 'None', 'lsx-videos' ) => 'none',
				);

				foreach ( $options as $name => $value ) {
					echo '<option value="' . esc_attr( $value ) . '" id="' . esc_attr( $value ) . '"', $display == $value ? ' selected="selected"' : '', '>', esc_html( $name ), '</option>';
				}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Image size:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" type="text" value="<?php echo esc_attr( $size ); ?>" />
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'featured' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $featured ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'featured' ) ); ?>"><?php esc_html_e( 'Featured posts', 'lsx-videos' ); ?></label>
		</p>
		<?php
	}

}

/**
 * Registers the Widget
 */
function lsx_videos_widget_most_recent() {
	register_widget( 'LSX_Videos_Widget_Most_Recent' );
}
add_action( 'widgets_init', 'lsx_videos_widget_most_recent' );
