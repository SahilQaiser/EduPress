<?php
/**
 * The default template for displaying content
 *
 * Used for single posts display
 *
 * @package WordPress
 * @subpackage Seeko
 * @since 1.0
 */

$count        = 0;
$page_no      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
?>

<div class="row">
	<?php
	// Start the Loop.
	while ( have_posts() ) :
		the_post();
		$count++;

		if (SVQ_FW::get_config( 'current_layout') == 'full' ) {
			if ( is_home() && is_sticky() && 1 === $page_no && $count === 1 ) {
				$responsive_classes = 'col-xl-6 col-lg-8 col-md-12';
			} else {
				$responsive_classes = 'col-xl-3 col-lg-4 col-md-6';
			}
		} else {
			if ( is_home() && is_sticky() && 1 === $page_no && $count === 1 ) {
				$responsive_classes = 'col-xl-8 col-md-12';
			}  else {
				$responsive_classes = 'col-xl-4 col-md-6';
			}
		}
		?>

		<div class="<?php echo esc_attr( $responsive_classes ); ?>">
			<?php get_template_part( 'page-parts/post/card-content' ); ?>
		</div>

		<?php
	endwhile;
	?>
</div>
