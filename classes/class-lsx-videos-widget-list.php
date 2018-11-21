<?php
/**
 * LSX Videos Widget (List/Grid) Class.
 *
 * @package lsx-videos
 */
class LSX_Videos_Widget_List extends \WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'lsx-videos lsx-videos-list',
		);

		parent::__construct( 'LSX_Videos_Widget_List', esc_html__( 'LSX Videos - List/Grid', 'lsx-videos' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		// @codingStandardsIgnoreLine
		extract( $args );

		$title = $instance['title'];
		$title_link = $instance['title_link'];
		$tagline = $instance['tagline'];
		$button_text = $instance['button_text'];
		$columns = $instance['columns'];
		$orderby = $instance['orderby'];
		$order = $instance['order'];
		$limit = $instance['limit'];
		$include = $instance['include'];
		$category = $instance['category'];
		$display = $instance['display'];
		$size = $instance['size'];
		$carousel = $instance['carousel'];
		$featured = $instance['featured'];

		if ( empty( $limit ) ) {
			$limit = '99';
		}

		if ( ! empty( $include ) ) {
			$limit = '99';
		}

		if ( '1' == $carousel ) {
			$carousel = 'true';
		} else {
			$carousel = 'false';
		}

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

		lsx_videos( array(
			'columns' => $columns,
			'orderby' => $orderby,
			'order' => $order,
			'limit' => $limit,
			'include' => $include,
			'category' => $category,
			'display' => $display,
			'size' => $size,
			'carousel' => $carousel,
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
		$instance['columns'] = strip_tags( $new_instance['columns'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['display'] = strip_tags( $new_instance['display'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['carousel'] = strip_tags( $new_instance['carousel'] );
		$instance['featured'] = strip_tags( $new_instance['featured'] );

		return $instance;
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => esc_html__( 'Videos', 'lsx-videos' ),
			'title_link' => '',
			'tagline' => '',
			'button_text' => '',
			'columns' => '3',
			'orderby' => 'name',
			'order' => 'ASC',
			'limit' => '',
			'include' => '',
			'category' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'carousel' => 1,
			'featured' => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = esc_attr( $instance['title'] );
		$title_link = esc_attr( $instance['title_link'] );
		$tagline = esc_attr( $instance['tagline'] );
		$button_text = esc_attr( $instance['button_text'] );
		$columns = esc_attr( $instance['columns'] );
		$orderby = esc_attr( $instance['orderby'] );
		$order = esc_attr( $instance['order'] );
		$limit = esc_attr( $instance['limit'] );
		$include = esc_attr( $instance['include'] );
		$category = esc_attr( $instance['category'] );
		$display = esc_attr( $instance['display'] );
		$size = esc_attr( $instance['size'] );
		$carousel = esc_attr( $instance['carousel'] );
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
			<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns:', 'lsx-videos' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" class="widefat">
			<?php
				$options = array( '1', '2', '3', '4' );

				foreach ( $options as $option ) {
					echo '<option value="' . esc_attr( lcfirst( $option ) ) . '" id="' . esc_attr( $option ) . '"', lcfirst( $option ) == $columns ? ' selected="selected"' : '', '>', esc_html( $option ), '</option>';
				}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By:', 'lsx-videos' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" class="widefat">
			<?php
				$options = array(
					esc_html__( 'None', 'lsx-videos' ) => 'none',
					esc_html__( 'ID', 'lsx-videos' ) => 'ID',
					esc_html__( 'Name', 'lsx-videos' ) => 'name',
					esc_html__( 'Date', 'lsx-videos' ) => 'date',
					esc_html__( 'Modified Date', 'lsx-videos' ) => 'modified',
					esc_html__( 'Random', 'lsx-videos' ) => 'rand',
					esc_html__( 'Menu (WP dashboard order)', 'lsx-videos' ) => 'menu_order',
				);

				foreach ( $options as $name => $value ) {
					echo '<option value="' . esc_attr( $value ) . '" id="' . esc_attr( $value ) . '"', $orderby == $value ? ' selected="selected"' : '', '>', esc_html( $name ), '</option>';
				}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'lsx-videos' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" class="widefat">
			<?php
				$options = array(
					esc_html__( 'Ascending', 'lsx-videos' ) => 'ASC',
					esc_html__( 'Descending', 'lsx-videos' ) => 'DESC',
				);

				foreach ( $options as $name => $value ) {
					echo '<option value="' . esc_attr( $value ) . '" id="' . esc_attr( $value ) . '"', $order == $value ? ' selected="selected"' : '', '>', esc_html( $name ), '</option>';
				}
			?>
			</select>
		</p>
		<p class="limit">
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Maximum amount:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
			<small><?php esc_html_e( 'Leave empty to display all', 'lsx-videos' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"><?php esc_html_e( 'Specify Videos by ID:', 'lsx-videos' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>" />
			<small><?php esc_html_e( 'Comma separated list, overrides limit and order settings', 'lsx-videos' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Category:', 'lsx-videos' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" class="widefat">
			<?php
				$args = array(
					'taxonomy'   => 'video-category',
					'hide_empty' => false,
				);

				$options = get_terms( $args );

				echo '<option value="" id=""', empty( $category ) ? ' selected="selected"' : '', '>', esc_html__( 'None', 'lsx-videos' ), '</option>';

				foreach ( $options as $name => $value ) {
					echo '<option value="' . esc_attr( $value->slug ) . '" id="' . esc_attr( $value->slug ) . '"', $category == $value->slug ? ' selected="selected"' : '', '>', esc_html( $value->name ), '</option>';
				}
			?>
			</select>
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
			<input id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $carousel ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>"><?php esc_html_e( 'Carousel', 'lsx-videos' ); ?></label>
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
function lsx_videos_widget_list() {
	register_widget( 'LSX_Videos_Widget_List' );
}
add_action( 'widgets_init', 'lsx_videos_widget_list' );
