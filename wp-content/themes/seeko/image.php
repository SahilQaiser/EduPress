<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

// Retrieve attachment metadata.
$metadata = wp_get_attachment_metadata();

get_header();
?>

<?php get_template_part( 'page-parts/general-before-wrap' ); ?>

<?php
// Start the Loop.
while ( have_posts() ) : the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">

		</header><!-- .entry-header -->

		<div class="entry-content">
			<div class="entry-attachment text-center">
				<div class="attachment">
						<?php svq_the_attached_image(); ?>
				</div><!-- .attachment -->

				<?php if ( has_excerpt() ) : ?>
					<div class="entry-caption">
						<?php the_excerpt(); ?>
					</div><!-- .entry-caption -->
				<?php endif; ?>
			</div><!-- .entry-attachment -->

			<?php
			the_content();
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'seeko' ),
				'after'  => '</div>',
			) );
			?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->

	<nav id="image-navigation" class="navigation image-navigation">
		<div class="nav-links">
			<?php previous_image_link( false, '<div class="previous-image">' . esc_html__( 'Previous Image', 'seeko' ) . '</div>' ); ?>
			<?php next_image_link( false, '<div class="next-image">' . esc_html__( 'Next Image', 'seeko' ) . '</div>' ); ?>
		</div><!-- .nav-links -->
	</nav><!-- #image-navigation -->

	<?php comments_template(); ?>

<?php endwhile; // end of the loop. ?>

<?php get_template_part( 'page-parts/general-after-wrap' ); ?>

<?php
get_footer();
