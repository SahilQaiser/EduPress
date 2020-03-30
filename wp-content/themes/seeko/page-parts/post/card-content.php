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

global $post;

$classes        = [ 'post-card' ];
$post_thumbnail = get_the_post_thumbnail( null, 'post-card' );
$has_thumbnail  = $post_thumbnail == '' ? false : true;

$post_format = get_post_format();

if ( ! $has_thumbnail ) {
	$classes['entire'] = 'entire-post-card';
} elseif ( 'image' == $post_format ) {
	$classes['entire'] = 'entire-post-card';
	$classes['format'] = $post_format . '-post-card';
}

/* Gallery format */
$gallery_images = false;
if ( $post_format == 'gallery' ) {

	$gallery_images = svq_get_gallery_images( null, [ 'format' => 'id' ] );
	if ( ! $gallery_images === false && ! empty( $gallery_images ) ) {
		$classes['entire'] = 'entire-post-card';
		$classes['format'] = $post_format . '-post-card';
	} else {
		$post_format = 'standard';
	}
}

if ( ( 'video' === $post_format && $has_thumbnail ) || 'quote' === $post_format ) {
	$classes['entire'] = 'entire-post-card';
	$classes['format'] = $post_format . '-post-card';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( join( ' ', $classes ) ); ?>>

	<?php if ( $post_format == 'gallery' ) : ?>

		<?php if ( $gallery_images ) :
			wp_enqueue_script( 'slick' );
			?>
			<section class="svq-slick svq-slick-inner-arrows"
			         data-arrows="true" data-infinite="true" data-show-slides="1" data-scroll-slides="1"
			         data-fade="true"
			         data-ease="linear" data-speed="500">

				<?php foreach ( $gallery_images as $gallery_image ): ?>
					<div>
						<figure class="img-dynamic aspect-ratio ratio-16-9 img-round post-thumbnail">
							<div class="img-card">
								<?php echo wp_get_attachment_image( $gallery_image, 'post-card' ); ?>
							</div>
						</figure>
					</div>
				<?php endforeach; ?>
			</section>

		<?php endif; ?>

	<?php elseif ( $post_format == 'quote' ) : ?>

	<?php elseif ( $has_thumbnail ) : ?>

		<figure class="post-thumbnail img-dynamic aspect-ratio ratio-16-9 img-round">
			<a class="img-card" href="<?php the_permalink(); ?>">
				<?php
				// This variable has been safely escaped by get_the_post_thumbnail function
				echo $post_thumbnail; // WPCS: XSS OK.
				?>
			</a>
			<noscript>
				<a class="img-card" href="<?php the_permalink(); ?>">
					<span style="background-image:url(<?php the_post_thumbnail_url( 'post-card' ); ?>);"></span>
				</a>
			</noscript>
		</figure>

	<?php endif; ?>

	<div class="entry-wrapper">
		<div class="entry-all">

			<?php if ( $post_format == 'quote' ) : ?>

				<div class="entry-top"></div>
				<div class="entry-middle">
					<a class="img-card-hover" href="<?php the_permalink(); ?>"></a>
					<div class="entry-content">
						<?php echo svq_get_content_quote( apply_filters( 'the_content', $post->post_content ) ); ?>
					</div>
				</div>

			<?php else : ?>

				<div class="entry-top">
					<?php svq_the_meta_category( ' ' ); ?>
				</div>

				<div class="entry-middle">
					<a class="img-card-hover" href="<?php the_permalink(); ?>"></a>
					<header class="entry-header">

						<?php
						the_title(
							sprintf( '<h3 class="entry-title h4"><a href="%s">', get_permalink() ),
							'</a></h3>'
						);
						?>
					</header>
					<div class="entry-content">
						<a href="<?php the_permalink(); ?>"></a>
						<div class="meta-excerpt">
							<?php the_excerpt(); ?>
						</div>
					</div>
				</div>

			<?php endif; ?>
            
            <?php if ( svq_option( 'blog_author_meta_archive', 1 ) ) : ?>
            <footer class="entry-footer">
				<a class="meta-author" href="<?php echo esc_url( svq_get_author_link() ); ?>">
					<span class="avatar avatar-square-xs<?php if ( function_exists( 'svq_get_member_online_class' ) ) {
						echo svq_get_member_online_class( get_the_author_meta( 'ID' ) );
					} ?>">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 30, '', '', [ 'class' => 'img-progressive progressive--not-loaded' ] ); ?>
					</span>

					<span class="author-and-date">
						<span class="author-name"><?php the_author(); ?></span>
						<time datetime="<?php echo get_the_date( 'Y-m-d H:i' ); ?>"
						      class="entry-date"><?php echo get_the_date(); ?></time>
					</span>
				</a>
			</footer>
            <?php endif; ?>

        </div>
	</div>

</article><!-- #post-## -->
