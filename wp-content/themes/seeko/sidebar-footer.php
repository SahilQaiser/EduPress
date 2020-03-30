<?php
/**
 * The Footer Sidebar
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

$svq_footer_hidden = apply_filters( 'svq_footer_hidden', SVQ_FW::get_config( 'footer_inactive' ) );
if ( $svq_footer_hidden == true ) {
	return;
}

?>

<footer id="footer" class="svq-footer">
	<div class="svq-footer-inner">
		<div class="<?php echo esc_attr( SVQ_FW::get_config( 'container_class' ) ); ?>">
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div id="footer-sidebar-1" class="footer-sidebar widget-area widgets-container" role="complementary">
						<?php
						if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'footer-1' ) ):
						endif;
						?>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div id="footer-sidebar-2" class="footer-sidebar widget-area widgets-container" role="complementary">
						<?php
						if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'footer-2' ) ):
						endif;
						?>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div id="footer-sidebar-3" class="footer-sidebar widget-area widgets-container" role="complementary">
						<?php
						if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'footer-3' ) ):
						endif;
						?>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div id="footer-sidebar-4" class="footer-sidebar widget-area widgets-container" role="complementary">
						<?php
						if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'footer-4' ) ):
						endif;
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer><!-- #footer -->
