<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

if ( have_posts() ) : ?>
	<div class="svq-page-header">
		<?php svq_the_breadcrumb(); ?>
		<?php
		the_archive_title( '<h1 class="page-title h2">', '</h1>' );
		the_archive_description( '<div class="taxonomy-description">', '</div>' );
		?>
	</div><!-- .page-header -->
<?php endif; ?>

<?php if ( have_posts() ) : ?>

	<?php
	get_template_part( 'page-parts/post/card' );

	// page navigation.
	svq_pagination();

endif;
