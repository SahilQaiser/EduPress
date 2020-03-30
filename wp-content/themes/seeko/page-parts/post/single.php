<?php
/**
 * The template for displaying Single post
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

/* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'page-parts/post/single', 'content' ); ?>

<?php endwhile;
