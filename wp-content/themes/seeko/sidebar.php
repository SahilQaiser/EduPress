<?php
/**
 * The Sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

$sidebar_name = apply_filters( 'svq_sidebar_name', 'sidebar-1' );
$svq_sidebar_classes = [ 'col-sidebar col-12' ];
$sidebar_classes = apply_filters( 'svq_sidebar_classes', $svq_sidebar_classes );

if ( ! is_active_sidebar( $sidebar_name ) ) {
	return;
}
?>

<div class="<?php echo esc_attr ( join( ' ', $sidebar_classes ) ); ?>">
	<div class="inner-content widgets-container">
		<?php
		if ( function_exists( 'generated_dynamic_sidebar' ) ) {
			generated_dynamic_sidebar( $sidebar_name );
		} else {
			dynamic_sidebar( 'sidebar-1' );
		}
		?>
	</div><!--end inner-content-->
</div><!--end sidebar-->
