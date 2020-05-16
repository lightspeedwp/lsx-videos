<?php
/**
 * @package lsx-videos
 */
?>

<?php
	global $lsx_videos_frontend;

	$categories       = '';
	$categories_class = '';
	$terms            = get_the_terms( get_the_ID(), 'video-category' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		$categories       = array();
		$categories_class = array();

	foreach ( $terms as $term ) {
		$categories[]       = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
		$categories_class[] = 'filter-' . $term->slug;
	}

		$categories       = join( ', ', $categories );
		$categories_class = join( ' ', $categories_class );
	}

	$youtube_url  = get_post_meta( get_the_ID(), 'lsx_video_youtube', true );
	$video_id     = get_post_meta( get_the_ID(), 'lsx_video_video', true );
	$giphy_iframe = get_post_meta( get_the_ID(), 'lsx_video_giphy', true );
	$views        = (int) get_post_meta( get_the_ID(), '_views', true );

	if ( ( ! empty( $video_id ) ) || ( '' === $video_id ) ) {
		$video_url  = wp_get_attachment_url( $video_id );
		$video_meta = get_post_meta( $video_id, '_wp_attachment_metadata', true );
	}

	if ( ! empty( $youtube_url ) ) {
		$video_url = $youtube_url;
	}

	$meta = '';

	// if ( 1 !== $views ) {
	// 	/* Translators: 1: video views */
	// 	$meta = '<span class="meta-views">' . sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views ) . '</span>';
	// } else {
	// 	$meta = '<span class="meta-views">' . esc_html__( '1 view', 'lsx-videos' ) . '</span>';
	// }

	if ( ! empty( $video_meta ) && ! empty( $video_meta['length_formatted'] ) ) {
		$length = $video_meta['length_formatted'];
		$meta   = '<span class="meta-duration">' . $length . '</span> | ' . $meta;
	}

	/* Translators: 1: time ago (video published date) */
	$meta .= '<span class="meta-date">' . sprintf( get_the_time( 'd M Y' ) ) . '</span>';
?>

<?php if ( empty( $lsx_videos_frontend->options['display'] ) || empty( $lsx_videos_frontend->options['display']['videos_disable_modal'] ) ) :
	$video_link = '#lsx-videos-modal';
	else :
		$video_link = get_permalink( get_the_ID() );
	endif;
	?>

<div class="<?php echo esc_attr( apply_filters( 'lsx_slot_class', 'col-xs-12 col-sm-4 col-md-4' ) ); ?> lsx-videos-column <?php echo esc_attr( $categories_class ); ?>">
	<article class="lsx-videos-slot">

		<a href="<?php echo esc_url( $video_link ); ?>" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>">
			<figure class="lsx-videos-avatar"><?php lsx_thumbnail( 'lsx-videos-cover' ); ?></figure>
		</a>

		<h5 class="lsx-videos-title">
			<a href="<?php echo esc_url( $video_link ); ?>" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h5>

		<?php if ( ! empty( $categories ) ) : ?>
			<p class="lsx-videos-categories"><?php echo wp_kses_post( $categories ); ?></p>
		<?php endif; ?>

		<p class="lsx-videos-meta"><?php echo wp_kses_post( $meta ); ?></p>

		<?php if ( empty( $lsx_videos_frontend->options['display'] ) || empty( $lsx_videos_frontend->options['display']['videos_disable_excerpt'] ) ) : ?>
			<div class="lsx-videos-content"><?php the_excerpt(); ?></div>
		<?php else : ?>
			<div class="lsx-videos-content"><a href="<?php echo esc_url( $video_link ); ?>" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-title="<?php the_title(); ?>" class="moretag"><?php esc_html_e( 'View video', 'lsx-videos' ); ?></a></div>
		<?php endif; ?>
	</article>
</div>
