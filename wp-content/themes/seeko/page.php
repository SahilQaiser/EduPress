<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package Wordpress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

get_header();

$page_title_class = apply_filters( 'svq_page_title_wrap_class', 'svq-page-header', get_the_ID() );
?>

<?php get_template_part( 'page-parts/general-before-wrap' ); ?>

<?php
if ( have_posts() ) :
	// Start the Loop.
	while ( have_posts() ) : the_post();
		?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="<?php echo esc_attr( $page_title_class ); ?>">
				<?php svq_the_breadcrumb(); ?>
				<?php the_title( '<h1 class="entry-title page-title h2">', '</h1>' ); ?>
			</div>

			<div class="entry-content clearfix">
				<?php the_content(); ?>
			</div>

			<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'seeko' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'seeko' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
			?>

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() ) : ?>
				<!-- Begin Comments -->
				<?php comments_template(); ?>
				<!-- End Comments -->
			<?php endif; ?>
		</div><!-- #post-## -->

	<?php
	endwhile;

endif;
?>

<?php get_template_part( 'page-parts/general-after-wrap' ); ?>

<?php get_footer(); ?>