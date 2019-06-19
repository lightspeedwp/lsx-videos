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

	$youtube_url = get_post_meta( get_the_ID(), 'lsx_video_youtube', true );
	$video_id = get_post_meta( get_the_ID(), 'lsx_video_video', true );
	$giphy_iframe = get_post_meta( get_the_ID(), 'lsx_video_giphy', true );
	$views = (int) get_post_meta( get_the_ID(), '_views', true );

	if ( ! empty( $video_id ) ) {
		$video_url = wp_get_attachment_url( $video_id );
		$video_meta = get_post_meta( $video_id , '_wp_attachment_metadata', true );
	}

	if ( ! empty( $youtube_url ) ) {
		$video_url = $youtube_url;
	}

	if ( 1 !== $views ) {
		/* Translators: 1: video views */
		$meta = sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views );
	} else {
		$meta = esc_html__( '1 view', 'lsx-videos' );
	}

	if ( ! empty( $video_meta ) && ! empty( $video_meta['length_formatted'] ) ) {
		$length = $video_meta['length_formatted'];
		$meta = $length . ' | ' . $meta;
	}

	/* Translators: 1: time ago (video published date) */
	$meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-videos' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
?>

<div class="<?php echo esc_attr( apply_filters( 'lsx_slot_class', 'col-xs-12 col-sm-4 col-md-3' ) ); ?> lsx-videos-column <?php echo esc_attr( $categories_class ); ?>">
	<article class="lsx-videos-slot">

		<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>">
			<figure class="lsx-videos-avatar"><?php lsx_thumbnail( 'lsx-videos-cover' ); ?></figure>
		</a>

		<h5 class="lsx-videos-title">
			<a href="#lsx-videos-modal" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h5>

		<?php if ( ! empty( $categories ) ) : ?>
			<p class="lsx-videos-categories"><?php echo wp_kses_post( $categories ); ?></p>
		<?php endif; ?>

		<p class="lsx-videos-meta"><?php echo wp_kses_post( $meta ); ?></p>

		<?php if ( empty( $lsx_videos_frontend->options['display'] ) || empty( $lsx_videos_frontend->options['display']['videos_disable_excerpt'] ) ) : ?>
			<div class="lsx-videos-content"><?php the_excerpt(); ?></div>
		<?php else : ?>
			<div class="lsx-videos-content"><a href="#lsx-videos-modal" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>" class="moretag"><?php esc_html_e( 'View video', 'lsx-videos' ); ?></a></div>
		<?php endif; ?>
	</article>
</div>