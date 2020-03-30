<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage NewDate
 * @since 1.0
 * @version 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

if (SVQ_FW::get_config( 'current_layout') == 'full') {
	$col_classes = 'col-xl-8 offset-xl-2 col-lg-10 offset-lg-1';
} else {
	$col_classes = 'col-12';
}

?>

<div id="comments" class="comments-area">
	<div class="container">
		<div class="row">
			<div class="<?php echo esc_attr( $col_classes ); ?>">
				<div class="col-inner">
					<h2 class="svq-section-title"><?php esc_html_e('Leave a comment', 'seeko'); ?></h2>

					<?php
					if ( have_comments() ) : ?>
						<p class="comments-title"><strong>
							<?php
							$comments_number = get_comments_number();
							if ( '1' === $comments_number ) {
								/* translators: %s: post title */
								printf( _x( 'One Reply to &ldquo;%s&rdquo;', 'comments title', 'seeko' ), get_the_title() );
							} else {
								printf(
								/* translators: 1: number of comments, 2: post title */
									_nx(
										'%1$s Reply to &ldquo;%2$s&rdquo;',
										'%1$s Replies to &ldquo;%2$s&rdquo;',
										$comments_number,
										'comments title',
										'seeko'
									),
									number_format_i18n( $comments_number ),
									get_the_title()
								);
							}
							?>
							</strong>
						</p>

						<ul class="comment-list list-unstyled">
							<?php
							wp_list_comments( array(
								'avatar_size' => 54,
								'style'       => 'ul',
								'short_ping'  => true,
								'reply_text'  => esc_html__( 'Reply', 'seeko' ) ,
								'callback'    => 'svq_custom_comments'
							) );
							?>
						</ul>

						<?php the_comments_pagination( array(
							'prev_text' => esc_html__( 'Previous', 'seeko' ) . '</span>',
							'next_text' => '<span>' . esc_html__( 'Next', 'seeko' ) . '</span>',
						) );

					endif; // Check for have_comments().
					?>

					<?php
					// If comments are closed and there are comments, let's leave a little note, shall we?
					if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
						<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'seeko' ); ?></p>
					<?php
					endif;

					svq_comment_form();
					?>

				</div>
			</div>
		</div>
	</div>



</div><!-- #comments -->
