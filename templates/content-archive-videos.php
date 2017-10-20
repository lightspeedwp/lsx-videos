<?php
/**
 * @package lsx-videos
 */
?>

<?php
	global $lsx_videos_frontend;

	$categories = '';
	$categories_class = '';
	$terms = get_the_terms( get_the_ID(), 'video-category' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		$categories = array();
		$categories_class = array();

		foreach ( $terms as $term ) {
			$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			$categories_class[] = 'filter-' . $term->slug;
		}

		$categories = join( ', ', $categories );
		$categories_class = join( ' ', $categories_class );
	}

	$video_id = get_post_meta( get_the_ID(), 'lsx_video_video', true );
	$video_url = wp_get_attachment_url( $video_id );
?>

<div class="col-xs-12 col-sm-4 col-md-3 lsx-videos-column <?php echo esc_attr( $categories_class ); ?>">
	<article class="lsx-videos-slot">
		<a href="#lsx-videos-modal" data-toggle="modal" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>">
			<figure class="lsx-videos-avatar"><?php lsx_thumbnail( 'lsx-videos-cover' ); ?></figure>
		</a>

		<h5 class="lsx-videos-title">
			<a href="#lsx-videos-modal" data-toggle="modal" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h5>

		<?php if ( ! empty( $categories ) ) : ?>
			<p class="lsx-videos-categories"><?php echo wp_kses_post( $categories ); ?></p>
		<?php endif; ?>

		<?php if ( empty( $lsx_videos_frontend->options['display'] ) || empty( $lsx_videos_frontend->options['display']['videos_disable_excerpt'] ) ) : ?>
			<div class="lsx-videos-content"><?php the_excerpt(); ?></div>
		<?php else : ?>
			<div class="lsx-videos-content"><a href="#lsx-videos-modal" data-toggle="modal" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>" class="moretag"><?php esc_html_e( 'View video', 'lsx-videos' ); ?></a></div>
		<?php endif; ?>
	</article>
</div>
