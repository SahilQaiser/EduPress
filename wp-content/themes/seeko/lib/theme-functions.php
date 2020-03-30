<?php
/* Specific theme settings and functions */


/***************************************************
 * :: Set some theme configuration options
 ***************************************************/

SVQ_FW::init_config( array(
	'slug'                    => 'seeko',
	'item_id'                 => '23175730',
	'font_css'                => get_template_directory_uri() . '/assets/icon/style.css',
	'menu_icon_default'       => 'icon-default',
	//Post image sizes for carousels and galleries.
	'post_gallery_img_width'  => 600,
	'post_gallery_img_height' => 400,

	//page templates.
	'tpl_map'                 => array(
		'page-templates/full-width.php'    => 'full',
		'page-templates/left-sidebar.php'  => 'left',
		'page-templates/right_sidebar.php' => 'right',
	),
	'layouts'                 => [
		'full'  => array(
			'alt' => 'Full, no sidebar',
			'img' => SVQ_LIB_URI . '/assets/images/1c.png'
		),
		'left'  => array(
			'alt' => 'Left Sidebar',
			'img' => SVQ_LIB_URI . '/assets/images/2cl.png'
		),
		'right' => array(
			'alt' => 'Right Sidebar',
			'img' => SVQ_LIB_URI . '/assets/images/2cr.png'
		),
	],
	'container_class'         => 'container',
) );

add_filter( 'theme_post_templates', 'svq_add_page_templates', 10 );

function svq_add_page_templates( $page_templates = [] ) {

	$page_templates = [
		                  'page-templates/full-width.php'    => 'Full, no sidebar',
		                  'page-templates/left-sidebar.php'  => 'Left Sidebar',
		                  'page-templates/right-sidebar.php' => 'Right Sidebar',
	                  ] + $page_templates;

	return $page_templates;
}


/***************************************************
 * ::  Theme specific functions
 ***************************************************/


if ( ! function_exists( 'svq_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 *
	 * @since Seeko 1.0
	 *
	 */
	function svq_post_nav( $same_cat = false ) {
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( $same_cat, '', true );
		$next     = get_adjacent_post( $same_cat, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		$current_layout = SVQ_FW::get_config( 'current_layout' );
		?>

		<nav class="svq-post-nav" role="navigation">

			<?php
			global $post;
			if ( is_attachment() ) :
				previous_post_link( '%link', wp_kses_post( __( '<span id="older-nav">Go to article</span>', 'seeko' ) ) );
			else :
				if ( $previous ) {
					$post = $previous;
					setup_postdata( $previous );
					?>
					<div class="nav2-post nav2-post-prev"
					     data-text="<?php echo esc_attr__( 'Previous article', 'seeko' ); ?>">
						<?php get_template_part( 'page-parts/post/card-content' ); ?>
					</div>
					<?php
					wp_reset_postdata();
				}
				if ( $next ) {
					$post = $next;
					setup_postdata( $next );
					?>
					<div class="nav2-post nav2-post-next"
					     data-text="<?php echo esc_attr__( 'Next article', 'seeko' ); ?>">
						<?php get_template_part( 'page-parts/post/card-content' ); ?>
					</div>
					<?php
					wp_reset_postdata();
				}
			endif;
			?>
		</nav><!-- .navigation -->

		<?php
	}
}

add_filter( 'previous_post_link', 'svq_nav_anchor_class' );
add_filter( 'next_post_link', 'svq_nav_anchor_class' );
function svq_nav_anchor_class( $output ) {
	return str_replace( '<a ', '<a class="btn btn-xs" ', $output );
}


if ( ! function_exists( 'svq_icons_array' ) ) {
	/**
	 * Generate and array with theme icons
	 *
	 * @param string $prefix
	 * @param string $key_prefix
	 * @param array $before
	 *
	 * @return array|mixed
	 */
	function svq_icons_array( $prefix = '', $key_prefix = '',  $before = [] ) {

		// Get any existing copy of our transient data
		$transient_name = SVQ_FW::get_config( 'slug' ) . '_font_icons_' . $prefix . $key_prefix . implode( '', $before );
		delete_transient( $transient_name );

		if ( false === ( $icons = get_transient( $transient_name ) ) ) {

			// It wasn't there, so regenerate the data and save the transient
			$icons = (array) $before;

			if ( is_child_theme() && file_exists( CHILD_THEME_DIR . '/assets/icon/style.css' ) ) {
				$icons_json = svq_fs_get_contents( CHILD_THEME_DIR . '/assets/icon/icon-names.txt' );
			} elseif ( file_exists( THEME_DIR . '/assets/icon/icon-names.txt' ) ) {
				$icons_json = svq_fs_get_contents( THEME_DIR . '/assets/icon/icon-names.txt' );
			}

			if ( isset( $icons_json ) && $icons_json ) {
				$arr = explode( "\n", $icons_json );
				foreach ( $arr as $icon ) {
					$icons[ $key_prefix . $icon ] = $prefix . $icon;
				}
				asort( $icons );
			}

			// set transient for one day
			set_transient( $transient_name, $icons, 86400 );
		}

		return $icons;
	}
}

function svq_get_font_icons_path() {

	if ( is_child_theme() && file_exists( CHILD_THEME_DIR . '/assets/icon/style.css' ) ) {
		$path = CHILD_THEME_URI . '/assets/icon/style.css';
	} else {
		$path = THEME_URI . '/assets/icon/style.css';
	}

	return $path;
}

function sks_get_dual_icons_array( $prefix = '', $prefix_key = '' ) {
	 $icons = [
		'icon-dual-ux',
		'icon-dual-what',
		'icon-dual-down',
		'icon-dual-arrow-up',
		'icon-dual-star',
		'icon-dual-video-camera',
		'icon-dual-cool',
		'icon-dual-match',
		'icon-dual-filter',
		'icon-dual-who',
		'icon-dual-why',
		'icon-dual-how',
		'icon-dual-share',
		'icon-dual-photo-camera',
		'icon-dual-settings',
		'icon-dual-bookmark',
		'icon-dual-download',
	];

	if ($prefix !== '' || $prefix_key !== '' ) {
		$arr = [];
		foreach ( $icons as $icon ) {
			$arr[ $prefix_key . $icon ] = $prefix . $icon;
		}
		return $arr;
	}

	return $icons;
}

function sks_generate_dual_icon( $name = null, $extra_class = '' ) {
	if ( ! $name ) {
		return '';
	}

	return "<i class='$name-hover sko-icon-hover $extra_class'></i>" .
	       "<i class='$name $extra_class'></i>";
}

if ( ! function_exists( 'svq_image_make_progressive' ) ) {
	/**
	 * For progressive image loading
	 *
	 * @param $attr
	 * @param $attachment
	 * @param $size
	 *
	 * @return mixed
	 */
	function svq_image_make_progressive( $attr, $attachment, $size ) {

		if ( apply_filters( 'seeko_img_progressive_bail', false, $attachment, 'attachment' ) ) {
			return $attr;
		}

		$large_image = wp_get_attachment_image_src( $attachment->ID, $size );

		//bail if can't get size
		if ( ! isset( $large_image[1] ) || ! isset( $large_image[2] ) ) {
			return $attr;
		}

		//bail if image too small
		if ( $large_image[1] < 70 || $large_image[2] < 70 ) {
			return $attr;
		}

		$small_image = wp_get_attachment_image_src( $attachment->ID, 'post-tiny' );

		if ( $small_image && $attr['src'] !== $small_image ) {

			if ( ! isset( $attr['class'] ) ) {
				$attr['class'] = '';
			} else {
				$attr['class'] .= ' ';
			}
			$attr['class'] .= 'img-progressive progressive--not-loaded';

			$attr['data-src'] = $attr['src'];

			if ( $large_image[1] < 80 && $large_image[2] < 80 ) {
				$attr['src'] = get_template_directory_uri() . '/assets/img/1x1.png';
			} else {
				$attr['src'] = $small_image[0];
			}

			if ( isset( $attr['srcset'] ) ) {
				$attr['data-srcset'] = $attr['srcset'];
				unset( $attr['srcset'] );
			}
			if ( isset( $attr['sizes'] ) ) {
				$attr['data-sizes'] = $attr['sizes'];
				unset( $attr['sizes'] );
			}
		}

		return $attr;
	}
}


add_filter( 'wp_get_attachment_image_attributes', 'svq_image_make_progressive', 80, 3 );

add_filter( 'seeko_img_progressive_bail', function ( $early_bail, $img ) {

	if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || is_admin() || ! svq_option( 'img_lazy', 1, true ) ) {
		return true;
	}

	if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['vc_editable'] ) ) {
		return true;
	}

	//Bail if Facebook avatar
	if ( is_string( $img ) && strpos( $img, 'graph.facebook.com' ) !== false ) {
		return true;
	}

	return $early_bail;
}, 10, 2 );


if ( ! function_exists( 'svq_get_meta_category' ) ) {
	/**
	 * Prints HTML with meta information for current categories
	 * Create your own svq_entry_meta() to override in a child theme.
	 */
	function svq_get_meta_category( $separator = ', ' ) {

		$categories = apply_filters( 'the_category_list', get_the_category(), false );

		$category_list = svq_get_categories_html_array( $categories );
		$output = '';
		if ( ! empty( $category_list ) ) {
			$output .= '<div class="meta-category">';
			$output .= join( $separator, $category_list );
			$output .= '</div>';
		}

		return apply_filters( 'svq_get_meta_category', $output );
	}
}

if ( ! function_exists( 'svq_the_meta_category' ) ) {
	function svq_the_meta_category( $separator = ', ' ) {
		echo svq_get_meta_category( $separator );
	}
}

function svq_get_categories_html_array( $categories ) {
	$category_list = [];
	foreach ( $categories as $category ) {
		$color = '';
		if ( class_exists( 'RainbowCategories' ) ) {
			$color = RainbowCategories::getColorForTerm( $category->term_id, true );
		}
		$category_list[] = '<a class="btn-tag btn-tag-sm btn-tag-' . $color . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' .
		                   $category->name .
		                   '</a>';
	}

	return $category_list;
}

if ( ! function_exists( 'svq_get_meta_tags' ) ) {
	/**
	 * Prints HTML with meta information for current tags
	 * Create your own svq_get_meta_tags() to override in a child theme.
	 */
	function svq_get_meta_tags( $separator = ' ' ) {
		$tags   = get_the_tag_list( '', $separator );
		if ( ! $tags ) {
			return '';
		}

		$output = '<div class="meta-tags">';
		$tags   = str_replace( 'href', 'class="btn-tag btn-tag-sm" href', $tags );
		$output .= $tags;
		$output .= '</div>';

		return apply_filters( 'svq_get_meta_tags', $output );
	}
}

if ( ! function_exists( 'svq_the_meta_tags' ) ) {
	function svq_the_meta_tags( $separator = ' ' ) {
		echo svq_get_meta_tags( $separator );
	}
}

function svq_show_categories() {
	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC'
	) );

	$categories_list = svq_get_categories_html_array( $categories );

	echo '<div class="blog-filters">';
	echo join( ' ', $categories_list );
	echo '</div>';


}

/***************************************************
 * :: Comments functions
 ***************************************************/


if ( ! function_exists( 'svq_custom_comments' ) ) {
	/**
	 * Display customized comments
	 *
	 * @param object $comment
	 * @param array $args
	 * @param integer $depth
	 */
	function svq_custom_comments( $comment, $args, $depth ) {
		$GLOBALS['comment']       = $comment;
		$GLOBALS['comment_depth'] = $depth;
		$avatar_class             = $depth > 1 ? 'avatar-square-sm' : 'avatar avatar-square';
		?>
		<li id="comment-<?php comment_ID() ?>" <?php comment_class( 'clearfix' ) ?>>
		<div class="comment-wrap clearfix">
			<div class="comment-avatar">

				<?php
				$avatar = '';
				if ( function_exists( 'get_avatar' ) ) {
					$avatar = get_avatar( $comment, '54' );
				}
				?>
				<?php if ( $avatar ) : ?>
					<figure class="<?php echo esc_attr( $avatar_class ); ?>">
						<div class="img-card">
							<?php
							// This variable has been safely escaped by get_avatar function
							echo $avatar; // WPCS: XSS OK.
							?>
						</div>
					</figure>
				<?php endif; ?>
			</div>
			<div class="comment-content">
				<div class="comment-meta">
					<?php

					edit_comment_link( esc_html__( 'Edit', 'seeko' ), '<span class="edit-link">', '</span>' );

					printf( '<span class="comment-author font-weight-bold">%1$s</span> <span class="comment-date">%2$s</span>',
						get_comment_author_link(),
						human_time_diff( get_comment_time( 'U' ),
							current_time( 'timestamp' ) ) . ' ' . esc_html__( 'ago', 'seeko' )
					);
					?>

				</div>
				<?php
				if ( '0' == $comment->comment_approved ) {
					echo '<span class="unapproved">';
					esc_html_e( "Your comment is awaiting moderation.", 'seeko' );
					echo "\n</span>";
				}
				?>
				<div class="comment-body">
					<?php comment_text() ?>
				</div>
				<div class="comment-meta-actions">
					<?php if ( 'all' == $args['type'] || 'comment' == get_comment_type() ) :
						comment_reply_link( array_merge( $args, array(
							'reply_text' => '<i class="icon icon-reply"></i><span>' . esc_html__( 'Reply', 'seeko' ) . '</span>',
							'login_text' => '<i class="icon icon-arrow-right"></i><span>' . esc_html__( 'Log in to reply.', 'seeko' ) . '</span>',
							'depth'      => $depth,
							'before'     => '<span class="comment-reply">',
							'after'      => '</span>',
						) ) );
					endif; ?>
				</div>
			</div>
		</div>
	<?php }
} // end svq_custom_comments


if ( ! function_exists( 'svq_comment_form' ) ) {
	/**
	 * Outputs a complete commenting form for use within a template.
	 * Most strings and form fields may be controlled through the $args array passed
	 * into the function, while you may also choose to use the comment_form_default_fields
	 * filter to modify the array of default fields if you'd just like to add a new
	 * one or remove a single field. All fields are also individually passed through
	 * a filter of the form comment_form_field_$name where $name is the key used
	 * in the array of fields.
	 *
	 * @param array $args Options for strings, fields etc in the form
	 * @param mixed $post_id Post ID to generate the form for, uses the current post if null
	 *
	 * @return void
	 */
	function svq_comment_form( $args = array(), $post_id = null ) {
		global $id;

		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		if ( null === $post_id ) {
			$post_id = $id;
		} else {
			$id = $post_id;
		}

		if ( comments_open( $post_id ) ) :
			?>
			<div id="respond-wrap">
				<?php
				$commenter = wp_get_current_commenter();
				$req       = get_option( 'require_name_email' );
				$aria_req  = ( $req ? " aria-required='true'" : '' );
				$fields    = array(
					'author' => '<div class="row"><p class="comment-form-author col-sm-4"><label for="author">' . esc_html__( 'Name', 'seeko' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
					'email'  => '<p class="comment-form-email col-sm-4"><label for="email">' . esc_html__( 'Email', 'seeko' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
					'url'    => '<p class="comment-form-url col-sm-4"><label for="url">' . esc_html__( 'Website', 'seeko' ) . '</label><input id="url" name="url" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p></div>',
				);

				if ( function_exists( 'bp_is_active' ) ) {
					$profile_link = bp_get_loggedin_user_link();
				} else {
					$profile_link = admin_url( 'profile.php' );
				}

				$req      = get_option( 'require_name_email' );
				$required_text = sprintf( ' ' . esc_html__( 'Required fields are marked %s', 'seeko' ), '<span class="required">*</span>' );

				$comments_args = array(
					'fields'             => apply_filters( 'comment_form_default_fields', $fields ),
					'logged_in_as'       => '<p class="logged-in-as">' . sprintf( wp_kses_post(  __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'seeko' ) ), esc_url( $profile_link ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '</p>',

					'comment_notes_before' => '<p class="svq-comments-info">In response to:</p>' .
					                          '<h4 class="svq-comments-title">'. get_the_title() .'</h4>' .
					                          '<p class="comment-notes"><span id="email-notes">' . esc_html__( 'Your email address will not be published.', 'seeko' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>',
					'title_reply'        => esc_html__( 'Leave a reply', 'seeko' ),
					'title_reply_to'     => esc_html__( 'Leave a reply to %s', 'seeko' ),
					'title_reply_before' => '<strong id="reply-title" class="comment-reply-title">',
					'title_reply_after'  => '</strong>' . ( is_user_logged_in() ?
					                        '<figure class="avatar avatar-square">
												<a class="img-card meta-author">
												' . svq_get_user_avatar() . '
												</a>
											</figure>' : '' ),
					'cancel_reply_link'  => esc_html__( 'Click here to cancel the reply', 'seeko' ),
					'label_submit'       => esc_html__( 'Post comment', 'seeko' ),
					'class_submit'       => 'submit btn-primary',
					'comment_field'      => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'seeko' ) . '</label><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
					'must_log_in'        => '<p class="must-log-in">' . sprintf( wp_kses_post( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'seeko' ) ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '</p>',
				);

				ob_start();
				comment_form( $comments_args );
				$comment_form = ob_get_clean();
				preg_match( '/<strong id="reply-title.*?<\/strong>/i', $comment_form, $matches );
				$comment_form = str_replace( $matches[0], '', $comment_form );
				$comment_form = str_replace( '<p class="logged-in-as">', $matches[0] . '<p class="logged-in-as">', $comment_form );
				$comment_form = str_replace( '<label for="comment"', '<label for="message" class="screen-reader-text"', $comment_form );
				/* Escaped in comment_form() function */
				echo $comment_form;  // WPCS: XSS OK.

				?>
			</div>

		<?php
		endif;

	}
}


add_filter( 'comment_reply_link', 'svq_comment_reply_class' );
function svq_comment_reply_class( $link ) {
	$link = str_replace( 'comment-reply-link', 'comment-reply-link btn btn-xs', $link );
	$link = str_replace( 'comment-reply-login', 'comment-reply-login btn btn-xs', $link );

	return $link;
}

/**
 * Make avatar load progressive only if img-progressive class exists
 *
 * @param $avatar
 * @param $id_or_email
 * @param $size
 * @param $default
 * @param $alt
 * @param $args
 *
 * @return mixed
 */
function svq_make_comment_avatar_lazy( $avatar, $id_or_email, $size, $default, $alt, $args ) {

	// Bail if filter is true
	if ( apply_filters( 'seeko_img_progressive_bail', false, $avatar, 'avatar' ) ) {
		return $avatar;
	}

	$proceed = false;
	if ( isset( $args['class'] ) ) {
		if ( is_array( $args['class'] ) && in_array( 'img-progressive', $args['class'] ) ) {
			$proceed = true;
		} elseif ( strpos( $args['class'], 'img-progressive' ) !== false ) {
			$proceed = true;
		}

		if ( $proceed === true ) {
			$url = get_template_directory_uri() . '/assets/img/1x1.png';
			$avatar = str_replace( 'src=', 'src="' . $url . '" data-src=', $avatar );
			$avatar = str_replace( 'srcset=', 'data-srcset=', $avatar );
		}
	}

	return $avatar;
}

add_filter( 'get_avatar', 'svq_make_comment_avatar_lazy', 10, 6 );

/* END COMMENTS SECTION */

function svq_get_author_link() {
	/* If buddypress is active then create a link to Buddypress profile instead */
	if ( function_exists( 'bp_is_active' ) ) {
		$author_link = bp_core_get_userlink( get_the_author_meta( 'ID' ), $no_anchor = false, $just_link = true );
	} else {
		$author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
	}

	return $author_link;
}

/**
 * @param null $id
 * @param int $size
 *
 * @return false|string
 */
function svq_get_user_avatar( $id = null, $size = 54 ) {
	if ( $id === null ) {
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if ( ( $current_user instanceof WP_User ) ) {
				$id = $current_user->ID;
			}
		}
	}

	return get_avatar( $id, $size );
}


function svq_get_gallery_images( $post = null, $attributes = [] ) {
	if ( ! $post ) {
		global $post;
	}

	remove_filter( 'wp_get_attachment_image_attributes', 'svq_image_make_progressive', 20 );

	// Retrieve the first gallery in the post
	$gallery = get_post_gallery_images( $post );

	add_filter( 'wp_get_attachment_image_attributes', 'svq_image_make_progressive', 20, 3 );

	if ( empty( $gallery ) && function_exists( 'has_blocks' ) && has_blocks( $post ) ) {
		preg_match( '/<ul class="wp-block-gallery.*?<\/ul>/', $post->post_content, $matches );
		if ( isset( $matches[0] ) ) {
			preg_match_all( '@src="([^"]+)"@', $matches[0], $matches_img );
			if ( isset( $matches_img[1] ) ) {
				$gallery = $matches_img[1];
			}
		}
	}


	$image_list = [];

	// Loop through each image in each gallery
	foreach ( $gallery as $image_url ) {
		$image = preg_replace( '/-\d+[Xx]\d+\./i', '.', $image_url );
		if ( isset( $attributes['format'] ) && $attributes['format'] == 'id' ) {
			if ( $image_id = attachment_url_to_postid( $image ) ) {
				$image_list[] = $image_id;
			}
		} else {
			$image_list[] = $image;
		}
	}

	return $image_list;
}

function svq_the_logo() {
	echo svq_get_the_logo();
}

function svq_get_the_logo() {
	$output = '';
	if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		$output         .= '<img class="seeko-logo" src="' . esc_attr( $image[0] ) . '">';
	} else {
		$output .= get_bloginfo();
	}

	return $output;
}


function svq_404_suggestions() {
	$output      = '';
	$suggestions = svq_option( '404_suggestions', [], true );

	if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
		$output .= '<p class="my-3">' . esc_html__( 'Maybe you want to try ...', 'seeko' ) . '</p>';
		foreach ( $suggestions as $suggestion ) {
			$output .= '<a class="text-underline mr-3" href="' . esc_url( get_the_permalink( $suggestion ) ) . '">';
			$output .= get_the_title( $suggestion ) . '</a>';
		}
	}

	return $output;
}

function svq_get_col_classes( $layout = '', $type = '' ) {

	if ( $layout == '' ) {
		$layout = SVQ_FW::get_config( 'current_layout' );
	}

	if ( $layout == 'full' ) {
		$classes = [ 'col-xl-1p5', 'col-lg-3', 'col-md-4', 'col-sm-6' ];
	} elseif ( $layout == '4' ) {
		$classes = [ 'col-xl-3', 'col-md-4', 'col-sm-6' ];
	} elseif ( $layout == '3' ) {
		$classes = [ 'col-md-4', 'col-sm-6' ];
	} elseif ( $layout == '2' ) {
		$classes = [ 'col-sm-6' ];
	} elseif ( $layout == '1' ) {
		$classes = [ 'col-sm-12' ];
	} else {
		$classes = [ 'col-xl-3', 'col-md-4', 'col-sm-6' ];
	}

	return $classes;
}


/* Some cleanup */
add_action( 'after_switch_theme', function () {

	$status = get_option( 'seeko_version_update' );

	if ( $status && isset( $status['initial'] ) ) {
		return;
	}

	//rtmedia
	update_option( 'rtmedia_inspirebook_release_notice', 'hide' );
	update_option( 'rtmedia_premium_addon_notice', 'hide' );
	update_option( 'rtmedia-update-template-notice-v3_9_4', 'hide' );
	update_site_option( 'install_transcoder_admin_notice', '0' );

	//elementor theme settings
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	update_option( 'elementor_page_title_selector', 'h1.entry-title' );
	update_option( 'elementor_viewport_lg', '1150' );
	update_option( 'elementor_container_width', '1390' );

	$default_colors = SVQ_FW::get_config( 'colors' );
	$default_site_options = SVQ_FW::get_config( 'styling_variables' );
	update_option( 'elementor_scheme_color', [
			1 => $default_site_options['body-color']['default'], //texts
			2 => $default_colors['secondary']['default'], //secondary
			3 => $default_site_options['body-color']['default'], //texts
			4 => $default_colors['primary']['default'] //primary
		]
	);
	if ( defined( 'ELEMENTOR_PATH' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	update_option( 'seeko_version_update', [ 'initial' => true ] );
} );
remove_action( 'admin_notices', 'pmpro_license_nag' );

/* rtMedia */
add_filter( 'rtmedia_custom_image_style', '__return_false' );


/* Likes functionality */
add_action( 'wp', function () {

	if ( is_singular( 'post' ) && class_exists( 'SVQ_Likes' ) ) {

		add_action( 'svq_after_main_content', function () {
			echo '<div class="like-and-share">';
			echo '<h5> ' . esc_html__( 'Do you like this article?', 'seeko' ) .'</h5>';

			svq_likes();

			echo '</div>';

		} );
	}
} );


/**
 * WP 5.2
 */
if ( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}