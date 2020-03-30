<?php
/**
 * The Footer Sidebar
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

$footer_text = do_shortcode( svq_option( 'footer_copy', SVQ_FW::get_config( 'footer_copy_default' ) ) );
$footer_hidden = ( $footer_text == '' ) ? true : false;

$svq_socket_hidden = apply_filters( 'svq_footer_socket_hidden', $footer_hidden );
if ( $svq_socket_hidden == true ) {
	return;
}
?>
<div class="svq-footer-socket text-center py-4">
	<div class="<?php echo esc_attr( SVQ_FW::get_config( 'container_class' ) ); ?>">
		<div class="row">
			<div class="col-12">
				<?php echo wp_kses_post( $footer_text ); ?>
			</div>
		</div>
	</div>
</div>
