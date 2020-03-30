<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

get_header();
?>
<section <?php svq_main_section_class(); ?>>

	<?php
	/**
	 * Renders the single blog post template.
	 *
	 * @hooked page-parts/post/single.php
	 */
	do_action( 'svq_single' );
	?>

</section>

<?php
get_footer();
