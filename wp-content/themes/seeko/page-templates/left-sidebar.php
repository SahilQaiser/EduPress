<?php
/**
 * Template Name: Left Sidebar Page Template
 *
 * Description: Show a page template with left sidebar
 *
 *
 * @package WordPress
 * @subpackage BuddyApp
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since BuddyApp 1.0
 */

//create left sidebar template
svq_switch_layout( 'left' );

//compatibility for posts
if (is_single()) {
	get_template_part( 'single' );
	return;
}

get_header(); ?>

<?php get_template_part('page-parts/general-before-wrap'); ?>

<?php
if ( have_posts() ) :
	// Start the Loop.
	while ( have_posts() ) : the_post();
		?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="<?php echo esc_attr( apply_filters( 'svq_page_title_wrap_class' ,'svq-page-header', get_the_ID() ) );?>">
				<?php the_title( '<h1 class="entry-title page-title h2">', '</h1>' ); ?>
			</div>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'seeko' ),
				'after'  => '</div>',
			) );
			?>
		</div><!-- #post-## -->

		<?php
	endwhile;

endif;
?>

<?php get_template_part('page-parts/general-after-wrap'); ?>


<?php get_footer(); ?>