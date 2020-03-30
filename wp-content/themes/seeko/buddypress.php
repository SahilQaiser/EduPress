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

if( bp_is_register_page() ) {
	svq_switch_layout( 'full' );
}
get_header(); ?>

<?php

if ( ! svq_bp_is_single_page() ) {
	get_template_part('page-parts/general-before-wrap');
}
?>

<?php
if ( have_posts() ) :
	// Start the Loop.
	while ( have_posts() ) : the_post();
?>
	    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	        <?php if ( ! svq_bp_is_single_page() ) : ?>
				<div class="<?php echo esc_attr( apply_filters( 'svq_page_title_wrap_class' ,'svq-page-header', get_the_ID() ) );?>">
					<?php svq_the_breadcrumb(); ?>
					<?php the_title( '<h1 class="entry-title page-title h2">', '</h1>' ); ?>
				</div>
			<?php endif; ?>

	            <?php
	            the_content();

	            wp_link_pages( array(
	                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'seeko' ),
	                'after'  => '</div>',
	            ) );
	            ?>
	    </div><!-- #post-## -->
        
<?php
	endwhile;

endif;
?>
        
<?php
if ( ! svq_bp_is_single_page() ) {
	get_template_part('page-parts/general-after-wrap');
}
?>

<?php get_footer();
