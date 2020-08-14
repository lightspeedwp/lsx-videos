<?php
/**
 * The Template for displaying all single videos.
 *
 * @package lsx
 */

get_header();

global $lsx_videos_frontend

?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main" role="main">

		<?php lsx_content_top(); ?>

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php include LSX_VIDEOS_PATH . '/templates/content-single-video.php'; ?>

			<?php endwhile; ?>

		<?php endif; ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

	<?php

	if ( empty( videos_get_option( 'single_video_disable_related' ) ) ) :
		lsx_videos_most_recent_related( get_the_ID() );
	endif;
	?>

	<?php
	if ( is_singular( 'video' ) ) {
		if ( empty( videos_get_option( 'single_video_disable_post_nav' ) ) ) :
			lsx_post_nav();
		endif;
	}
	?>

	<?php
	if ( comments_open() ) {
		comments_template();
	}
	?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php
get_footer();
