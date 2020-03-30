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

$layout = SVQ_FW::get_config( 'current_layout' );
$post_format = get_post_format();
$post_thumbnail = get_the_post_thumbnail( NULL, 'post-featured' );
$has_thumbnail = ( $post_thumbnail == '' || in_array( $post_format, ['video', 'gallery'] ) ) ? false : true;
$has_thumbnail = apply_filters( 'svq_show_featured_image', $has_thumbnail, get_the_ID() );
$classes       = 'standard-post';
if ( $has_thumbnail ) {
	$classes .= ' with-cover';
}
$container_classes = ['col' => 'col-12'];
if($layout == 'full' ) {
	$container_classes['col'] = 'col-lg-10 offset-lg-1';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>

	<?php
	/**
	 * Before main content - action
	 */
	do_action( 'svq_before_main_row' );
	?>

	<div <?php svq_main_row_class(); ?>>

		<?php
		/**
		 * Before main content - action
		 */
		do_action( 'svq_before_content' );
		?>

		<div class="col-header <?php echo esc_attr( join( ' ', $container_classes ) ); ?>">

			<div class="standard-post-meta col-inner">

				<?php svq_the_breadcrumb(); ?>

				<header class="entry-header">

					<?php svq_the_meta_category( ' ' ); ?>

					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>

				<?php if ( svq_option( 'blog_author_meta', 1 ) ) : ?>

					<div class="author-wrapper">

						<figure class="img-dynamic aspect-ratio avatar avatar-square-sm avatar-status<?php if (function_exists('svq_get_member_online_class')) { echo esc_attr( svq_get_member_online_class( get_the_author_meta( 'ID' ) ) ); } ?>">
							<a class="img-card meta-author" href="<?php echo esc_url( svq_get_author_link() ); ?>">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), '50' ); ?>
							</a>
						</figure>

						<span class="author-and-date">
							<a class="author-name" href="<?php echo esc_url( svq_get_author_link() ); ?>"><?php the_author(); ?></a>
							<time datetime="<?php the_date('Y-m-d H:i'); ?>" class="entry-date">
								<?php
								// String escaped by by get_the_date
								echo get_the_date(); // WPCS: XSS OK.
								?>
							</time>
						</span>

					</div>

				<?php endif; ?>

			</div>
		</div>

		<div <?php svq_main_col_class( $container_classes ); ?>>

			<?php
			/**
			 * Before main content - action
			 */
			do_action( 'svq_before_main_content' );
			?>

			<div class="entry-content clearfix col-inner">

				<?php if ( $has_thumbnail ) : ?>
					<div class="standard-post-img">
						<figure class="img-dynamic have-shadow aspect-ratio ratio-16-9 img-round">
							<div class="img-card">
								<?php
								// This variable has been safely escaped by get_the_post_thumbnail function
								echo $post_thumbnail; // WPCS: XSS OK.
								?>
							</div>

						</figure>
					</div>
				<?php endif; ?>

				<div class="entry-content-inner clearfix">
					<?php
					the_content();
					?>
				</div>

				<?php
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'seeko' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'seeko' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				) );
				?>

				<?php svq_the_meta_tags(); ?>

				<?php
				/**
				 * After main content - action
				 */
				do_action('svq_after_main_content');
				?>

			</div><!-- .entry-content -->

			<?php
			// Previous/next post navigation.
			if ( svq_option( 'blog_nav', 1 ) ) {
				svq_post_nav();
			}
			?>

			<?php
			// Related articles
			if ( svq_option( 'blog_author_bio', 1 ) ) {
				get_template_part( 'author-bio' );
			}
			?>

			<?php
			if ( svq_option( 'blog_related', 1 ) ) {
				get_template_part( 'page-parts/post/related' );
			}
			?>

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) : ?>
				<!-- Begin Comments -->
				<?php comments_template(); ?>
				<!-- End Comments -->
			<?php endif; ?>

		</div>

		<?php
		/**
		 * After main content - action
		 */
		do_action('svq_after_content');
		?>


	</div>

</article><!-- #post-## -->
