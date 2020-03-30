<?php
/**
 * Author Bio template
 *
 * @package WordPress
 * @subpackage Seeko
 * @since 1.0
 */

$description = get_the_author_meta( 'description' );
$twitter     = get_the_author_meta( 'twitter' );
$facebook    = get_the_author_meta( 'facebook' );
$linkedin    = get_the_author_meta( 'linkedin' );
$profession  = get_the_author_meta( 'profession' );

if ( ! $description ) {
	return;
}

if ( SVQ_FW::get_config( 'current_layout' ) == 'full' ) {
	$col_classes = 'col-xl-8 offset-xl-2 col-lg-10 offset-lg-1';
} else {
	$col_classes = 'col-12';
}
?>

<div class="author-bio">
	<div class="container">
		<div class="row">
			<div class="<?php echo esc_attr( $col_classes ); ?>">
				<div class="col-inner">
					<h2><?php esc_html_e( 'About the author', 'seeko' ); ?></h2>

					<div class="author-wrapper">
						<figure
							class="img-dynamic aspect-ratio avatar avatar-square-lg avatar-status is-online have-shadow">
							<a class="img-card meta-author" href="<?php echo esc_url( svq_get_author_link() ); ?>">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), '72' ); ?>
							</a>
						</figure>

						<span class="name-and-entitling">
                             <a class="author-name" href="<?php echo esc_url( svq_get_author_link() ); ?>"><?php the_author(); ?></a>
							<?php if ( $profession !== '' ): ?>
								<span class="author-entitling"><?php echo wp_kses_post( $profession ); ?></span>
							<?php endif; ?>
                           </span>
					</div>

					<?php if ( $description != '' ): ?>
						<p class="author-desc lead">
							<?php echo wp_kses_post( $description ); ?>
						</p>
					<?php endif; ?>

					<ul class="author-social-links list-inline">
						<?php if ( $facebook !== '' ): ?>
							<li><a href="<?php echo esc_url( $facebook ); ?>"><i
										class="icon icon-facebook-fill"></i></a></li>
						<?php endif; ?>
						<?php if ( $twitter !== '' ): ?>
							<li><a href="https://twitter.com/<?php echo esc_attr( $twitter ); ?>"><i
										class="icon icon-twitter-fill"></i></a></li>
						<?php endif; ?>
						<?php if ( $linkedin !== '' ): ?>
							<li><a href="<?php echo esc_url( $linkedin ); ?>"><i
										class="icon icon-linkedin-fill"></i></a></li>
						<?php endif; ?>
					</ul>

				</div>
			</div>
		</div>
	</div>
</div>
