<?php
/**
 * The template used for displaying page content in single.php (custom post type)
 *
 * @package lsx
 */

?>

<?php lsx_entry_before(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php lsx_entry_top(); ?>

	<div class="entry-content">

		<?php
			$youtube_url  = get_post_meta( get_the_ID(), 'lsx_video_youtube', true );
			$video_id     = get_post_meta( get_the_ID(), 'lsx_video_video', true );
			$giphy_iframe = get_post_meta( get_the_ID(), 'lsx_video_giphy', true );
			$views        = (int) get_post_meta( get_the_ID(), '_views', true );

			$youtube_iframe = preg_replace( '/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', '<iframe width=\'100%\' height=\'450\' src=\'//www.youtube.com/embed/$1\' frameborder=\'0\' allowfullscreen></iframe>', $youtube_url );

			$video_url = wp_get_attachment_url( $video_id );
			/* Translators: 1: days */
			$meta_date = '<span class="meta-date">' . get_the_time( 'd M Y' ) . '</span>';

		// if ( 1 !== $views ) {
		// 	/* Translators: 1: video views */
		// 	$meta_views = ' | <span class="meta-views">' . sprintf( esc_html__( '%1$s views', 'lsx-videos' ), $views ) . '</span>';
		// } else {
		// 	$meta_views = ' | <span class="meta-views">' . esc_html__( '1 view', 'lsx-videos' ) . '</span>';
		// }

		?>

		<div class="lsx-videos-slot">

			<?php if ( '' !== $youtube_iframe ) { ?>
				<div class="media-youtube">
					<?php echo $youtube_iframe;//phpcs:ignore ?>
				</div>
			<?php } elseif ( '' !== $giphy_iframe ) { ?>
				<div class="media-gif">
					<?php echo $giphy_iframe;//phpcs:ignore ?>
				</div>
			<?php } elseif ( '' !== $video_url ) { ?>
				<div class="media-uploaded">
					<video width="100%" height="auto" controls>
						<source src="<?php echo $video_url;//phpcs:ignore ?>" type="video/mp4">
					</video>
				</div>
			<?php } ?>
			<div class="lsx-videos-title"><?php the_title(); ?></div>
			<div class="lsx-videos-meta">
				<?php echo wp_kses_post( $meta_date ); ?>
				<?php //echo wp_kses_post( $meta_views ); ?>
			</div>
		</div>

		<div class="video-content">
			<?php the_content(); ?>
		</div>

	</div><!-- .entry-content -->

	<?php lsx_entry_bottom(); ?>

</article><!-- #post-## -->

<?php
lsx_entry_after();
