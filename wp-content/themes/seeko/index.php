<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Seeko
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Seeko 1.0
 */

get_header();
if ( is_home() && ! is_front_page() ) {
	$blog_page_name = single_post_title( '', false );
} else {
	$blog_page_name = esc_html__( 'Blog', 'seeko' );
	if ( svq_option( 'blog_title', '' ) !== '' ) {
		$blog_page_name = svq_option( 'blog_title', '' );
	}
}
?>

<?php get_template_part( 'page-parts/general-before-wrap' ); ?>

	<header class="svq-page-header">

		<h1 class="page-title entry-title h2"><?php echo esc_html( $blog_page_name ); ?></h1>

		<?php if ( svq_option( 'blog_intro', SVQ_FW::get_config( 'blog_intro' ) ) != '' ): ?>
			<p class="svq-blog-intro"><?php echo esc_html( svq_option( 'blog_intro', SVQ_FW::get_config( 'blog_intro' ) ) ); ?></p>
		<?php endif; ?>

	</header>

<?php

if ( have_posts() ) :
	if ( svq_option( 'blog_show_cats', 0 ) ) {
		svq_show_categories();
	}

	get_template_part( 'page-parts/post/card' );

	// Previous/next post navigation.
	svq_pagination();

endif;
?>

<?php get_template_part( 'page-parts/general-after-wrap' ); ?>

<?php
get_footer();
