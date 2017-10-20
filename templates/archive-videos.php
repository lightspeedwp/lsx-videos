<?php
/**
 * The template for displaying all single videos.
 *
 * @package lsx-videos
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main">

		<?php lsx_content_top(); ?>

		<?php
			$args = array(
				'taxonomy'   => 'video-category',
				'hide_empty' => false,
			);

			$categories = get_terms( $args );
			$category_selected = get_query_var( 'video-category' );

			if ( count( $categories ) > 0 ) :
			?>

			<ul class="nav nav-tabs lsx-videos-filter">
				<?php
					$category_selected_class = '';

					if ( empty( $category_selected ) ) {
						$category_selected_class = ' class="active"';
					}
				?>

				<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo get_post_type_archive_link( 'video' ); ?>" data-filter="*"><?php esc_html_e( 'All', 'lsx-videos' ); ?></a></li>

				<?php foreach ( $categories as $category ) : ?>
					<?php
						$category_selected_class = '';

						if ( (string) $category_selected === (string) $category->slug ) {
							$category_selected_class = ' class="active"';
						}
					?>

					<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo get_term_link( $category ); ?>" data-filter=".filter-<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_attr( $category->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>

			<?php
			endif;
		?>

		<?php if ( have_posts() ) : ?>

			<div class="lsx-videos-container">
				<div class="row row-flex lsx-videos-row">

					<?php
						$count = 0;

						while ( have_posts() ) {
							the_post();
							include( LSX_VIDEOS_PATH . '/templates/content-archive-videos.php' );
						}
					?>

				</div>
			</div>

			<?php lsx_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'partials/content', 'none' ); ?>

		<?php endif; ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php get_footer();
