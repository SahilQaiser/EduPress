<?php
/**
 * This generates theme header
 * Stax Builder will automatically populate content.
 *
 * @package NewDate
 */

svq_flex_enqueue();
?>

<!-- Header
============================================= -->

<header id="header">
<section class="<?php echo esc_attr( SVQ_FW::get_config( 'container_class' ) ); ?>">
	<nav class="navbar navbar-expand-lg navbar-light px-0">
		<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php svq_the_logo(); ?>
		</a>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#seekoMainMenu"
			        aria-controls="seekoMainMenu" aria-expanded="false" aria-label="<?php esc_html_e( 'Toggle navigation', 'seeko' ); ?>">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="seekoMainMenu">

				<?php
				// Top left navigation menu.
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'menu_class'     => 'navbar-nav ml-auto flex-menu flex-menu-overflow',
					'container' => false,
					//'link_before'       => '<span>',
					//'link_after'        => '</span>',
					'depth' => 4,
					//'max_elements' => 5,
					'walker' => new SVQ_Walker_Nav_Walker(),
					'fallback_cb' => ''
				) );
				?>

			</div>
		<?php endif; ?>
	</nav>


</section>
</header><!-- #header end -->
