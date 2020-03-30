<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */

//create full width template
svq_switch_layout( 'full' );

get_header(); ?>

<?php get_template_part( 'page-parts/general-before-wrap' ); ?>

<?php get_template_part( 'page-parts/register-form' ); ?>

	<div class="row">
		<div class="col-md-6">
			<div class="svq-image-404">
				<img src="<?php echo esc_url( svq_option( '404_image', get_template_directory_uri() . '/assets/img/404.png', true ) ); ?>" class="svq-image-404 p-4 w-100"
				     alt="404">
			</div>
		</div>

		<div class="col-md-6 align-self-center order-md-first">
			<h1 class="article-title font-weight-black mb-4"><?php esc_html_e( 'Ooooops', 'seeko' ); ?></h1>

			<h3 class="font-weight-bold mb-3"><?php esc_html_e( 'Don\'t give up that easy', 'seeko' ); ?></h3>

			<h6 class="mb-4"><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'seeko' ); ?></h6>
			<?php get_search_form(); ?>

			<div class="svq-404-suggest">
				<?php echo wp_kses_post( svq_404_suggestions() ); ?>
			</div>
		</div>

	</div>


<?php get_template_part( 'page-parts/general-after-wrap' ); ?>

<?php
get_footer();
