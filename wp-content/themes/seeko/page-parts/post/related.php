<?php

/* Query args */
$args = array(
	'post__not_in' => array( $post->ID ),
	'showposts'    => 8,
	'orderby'      => 'rand', //random posts
	'order'        => 'ASC' //most recent first
);

//logic for blog posts
if ( is_singular( 'post' ) ) {

	//related text
	$related_text = esc_html__( "Related Articles", "seeko" );

	$categories = get_the_category( $post->ID );

	if ( ! empty( $categories ) ) {
		$category_ids = array();
		foreach ( $categories as $rcat ) {
			$category_ids[] = $rcat->term_id;
		}

		$args['category__in'] = $category_ids;
	}
} // logic for custom post types
else {

	//related text
	$related_text = esc_html__( "Related", "seeko" );

	global $post;
	$categories = get_object_taxonomies( $post );

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $tax ) {
			$terms = wp_get_object_terms( $post->ID, $tax, array( 'fields' => 'ids' ) );

			$args['tax_query'][] = array(
				'taxonomy' => $tax,
				'field'    => 'id',
				'terms'    => $terms
			);
		}
	}
}

/* Remove this line to show related posts even no categories are found */
if ( ! $categories ) { return; }

$layout = SVQ_FW::get_config( 'current_layout' );
$posts_in_view = 4;
$posts_in_view_lg = 3;

if ( $layout !== 'full' ) {
	$posts_in_view = 3;
	$posts_in_view_lg = 2;
}

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) :

	wp_enqueue_script( 'slick' );
	?>
<!-- Related Articles -->
<div class="related-articles">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="col-inner">
					<?php if ( $the_query->post_count > 3 ) : ?>
					<a href="#" class="svq-slick-prev btn btn-xs" data-carousel="slick01">
						<i class="icon icon-arrow-left"></i>
					</a>
					<?php endif; ?>

					<h2 class="svq-section-title"><?php echo esc_html( $related_text ); ?></h2>
					<?php if ( $the_query->post_count > 3 ) : ?>
					<a href="#" class="svq-slick-next btn btn-xs" data-carousel="slick01">
						<i class="icon icon-arrow-right"></i>
					</a>
					<?php endif; ?>
				</div>

				<section class="svq-slick row" data-carousel="slick01" data-arrows="false" data-infinite="true"
				         data-show-slides="<?php echo esc_attr( $posts_in_view ); ?>" data-lg-show-slides="<?php echo esc_attr( $posts_in_view_lg ); ?>" data-md-show-slides="2" data-sm-show-slides="1"
				         data-xs-show-slides="1" data-scroll-slides="1"
				         data-sm-center-padding="8px" data-xs-center-padding="8px">

						<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
							<div class="col-lg-3">
								<?php get_template_part('page-parts/post/card-content'); ?>
							</div>
						<?php endwhile; ?>
				</section>
			</div>
		</div>
	</div>
</div>

<?php endif;

/* Restore original Post Data */
wp_reset_postdata();
