<?php
/**
 * The template for displaying Search Results pages
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

get_header(); ?>

<?php get_template_part( 'page-parts/general-before-wrap' ); ?>

<div class="svq-page-header">
	<?php if ( have_posts() ) : ?>
		<h1 class="page-title entry-title h2"><?php printf( esc_html__( 'Search Results for: %s', 'seeko' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
	<?php else : ?>
		<h1 class="page-title entry-title h2"><?php esc_html_e( 'Nothing Found', 'seeko' ); ?></h1>
	<?php endif; ?>
</div><!-- .page-header -->


<?php if ( have_posts() ) :

	get_template_part( 'page-parts/post/card' );

	// Previous/next post navigation.
	svq_pagination();
else: ?>

	<p><?php esc_html_e("We cannot find the item you are searching for, maybe a little spelling mistake?", "seeko"); ?></p>
	<?php get_search_form(); ?>

<?php
endif;
?>

<?php get_template_part( 'page-parts/general-after-wrap' ); ?>

<?php
get_footer();


