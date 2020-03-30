<?php
/**
 * @package WordPress
 * @subpackage KLEO
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since 1.6
 */

/* BuddyPress compat class */

if ( 'nouveau' === get_option( '_bp_theme_package_id' ) ) {
	require_once 'bp-nouveau.php';
} else {
	require_once 'bp-legacy.php';
}

/* TODO Remove this BuddyPress 3.0 compat */
if ( 'legacy' !== get_option( '_bp_theme_package_id' ) && false == apply_filters( 'bp_30_disable_compat', false ) ) {
	update_option( '_bp_theme_package_id', 'legacy' );
}

SVQ_FW::set_config( 'default_avatar_size', 'large' );
SVQ_FW::set_config( 'default_avatar_style', 'square' );

$pack = bp_get_theme_package_id();

/* Remove Buddypress css */
add_action( 'wp_enqueue_scripts', 'svq_bp_scripts', 9999 );

function svq_bp_scripts() {
	wp_script_add_data( 'bp-confirm', 'defer', true );
	wp_script_add_data( 'bp-widget-members', 'defer', true );
	wp_script_add_data( 'bp-jquery-query', 'defer', true );
	//wp_script_add_data( 'bp-jquery-cookie', 'defer', true );
	wp_script_add_data( 'bp-jquery-scroll-to', 'defer', true );
	wp_script_add_data( 'wp-util', 'defer', true );
	wp_script_add_data( 'bp-avatar', 'defer', true );
	wp_script_add_data( 'bp-cover-image', 'defer', true );
	wp_script_add_data( 'bp-plupload', 'defer', true );

	wp_script_add_data( 'bp-parent-js', 'defer', true );
	wp_script_add_data( 'bp_core_widget_friends-js', 'defer', true );
	wp_script_add_data( 'groups_widget_groups_list-js', 'defer', true );

	if ( class_exists( 'RTMedia' ) ) {
		wp_deregister_style( 'rtmedia-main' );
		wp_dequeue_style( 'rtmedia-main' );
		wp_enqueue_style( 'rtmedia-main', THEME_URI . '/rtmedia/css/rtmedia.css', array(), RTMEDIA_VERSION, 'all' );

		wp_script_add_data( 'rt-mediaelement', 'defer', true );
		wp_script_add_data( 'rt-mediaelement-wp', 'defer', true );
		wp_script_add_data( 'rtmedia-main', 'defer', true );
		wp_script_add_data( 'rtmedia-magnific', 'defer', true );
		wp_script_add_data( 'rtmedia-backbone', 'defer', true );
		wp_script_add_data( 'rtmedia-touchswipe', 'defer', true );
		wp_script_add_data( 'rtmedia-upload-terms-main', 'defer', true );
		wp_script_add_data( 'rtmedia-magnific-popup', 'defer', true );
		wp_script_add_data( 'jquery-masonry', 'defer', true );
		wp_script_add_data( 'moxiejs', 'defer', true );
		wp_script_add_data( 'plupload', 'defer', true );
		wp_script_add_data( 'jquery-query.', 'defer', true );
		//wp_script_add_data( 'backbone', 'defer', true );
		wp_script_add_data( 'wp-backbone', 'defer', true );
	}

	if ( svq_bp_is_single_page() ) {
		svq_flex_enqueue();
	}

}

/**
 * Seeko templates structure
 */
add_filter( 'bp_get_template_stack', function ( $stack = [] ) {

	$pack = bp_get_theme_package_id();

	$themes  = [ wp_get_theme()->stylesheet . '/buddypress' ];
	$replace = [ wp_get_theme()->stylesheet . '/buddypress/' . $pack ];
	if ( is_child_theme() ) {
		$themes[]  = wp_get_theme()->Template . '/buddypress';
		$replace[] = wp_get_theme()->Template . '/buddypress/' . $pack;
	}

	if ( ! empty( $stack ) ) {
		foreach ( $stack as $k => $item ) {
			$stack[ $k ] = str_replace( $themes, $replace, $item );
		}
	}

	return $stack;
} );

add_action( 'init', 'svq_set_bp_constants', 8 );
function svq_set_bp_constants() {
	if ( ! defined( 'BP_AVATAR_THUMB_WIDTH' ) ) {
		define( 'BP_AVATAR_THUMB_WIDTH', 70 );
	}

	if ( ! defined( 'BP_AVATAR_THUMB_HEIGHT' ) ) {
		define( 'BP_AVATAR_THUMB_HEIGHT', 70 );
	}

	if ( ! defined( 'BP_AVATAR_FULL_WIDTH' ) ) {
		define( 'BP_AVATAR_FULL_WIDTH', 400 );
	}

	if ( ! defined( 'BP_AVATAR_FULL_HEIGHT' ) ) {
		define( 'BP_AVATAR_FULL_HEIGHT', 400 );
	}
}

add_filter( 'svq_main_section_class', function ( $classes ) {

	if ( svq_bp_is_single_page() ) {
		$keys = array_keys( $classes, 'container-finite' );
		if ( ! empty( $keys ) ) {
			unset( $classes[ $keys[0] ] );
		}
		$keys = array_keys( $classes, 'container' );
		if ( ! empty( $keys ) ) {
			unset( $classes[ $keys[0] ] );
		}
		if ( ! in_array( 'container-fluid', $classes ) ) {
			$classes[] = 'container-fluid';
		}

		$classes[] = 'px-0';
	}

	return $classes;
}, 22 );

add_filter( 'svq_main_row_class', function ( $classes ) {

	if ( svq_bp_is_single_page() ) {
		$classes['layout']  = 'layout-custom';
		$classes['sidebar'] = 'sidebar-single';
	}

	return $classes;
}, 20 );


add_filter( 'svq_theme_settings', 'svq_bp_settings' );

function svq_bp_settings( $sq ) {
	//
	// Settings Sections
	//

	$bp_single_layouts = SVQ_FW::get_config( 'layouts' );
	unset( $bp_single_layouts['right'] );

	$sq['sec']['svq_section_bp'] = array(
		'title'    => esc_html__( 'BuddyPress Settings', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 16
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'        => 'thumb_size',
		'type'      => 'select',
		'title'     => esc_html__( 'Avatars size', 'seeko' ),
		'default'   => 'large',
		'choices'   => [
			'small' => esc_html__( 'Small', 'seeko' ),
			'large' => esc_html__( 'Large', 'seeko' ),
		],
		'section'   => 'svq_section_bp',
		'transport' => 'refresh',
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'              => 'thumb_style',
		'type'            => 'select',
		'title'           => esc_html__( 'Style', 'seeko' ),
		'default'         => 'square',
		'choices'         => [
			'square' => esc_html__( 'Square', 'seeko' ),
			'round'  => esc_html__( 'Round', 'seeko' ),
		],
		'section'         => 'svq_section_bp',
		'customizer'      => true,
		'transport'       => 'refresh',
		'active_callback' => array(
			array(
				'setting'  => 'thumb_size',
				'operator' => '==',
				'value'    => 'small',
			),
		),

	);

	$sq['set']['svq_section_bp'][] = array(
		'id'         => 'bp_members_perpage',
		'type'       => 'text',
		'title'      => esc_html__( 'BuddyPress Members per page', 'seeko' ),
		'default'    => '24',
		'validate'   => 'numeric',
		'section'    => 'svq_section_bp',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'         => 'bp_groups_perpage',
		'type'       => 'text',
		'title'      => esc_html__( 'BuddyPress Groups per page', 'seeko' ),
		'default'    => '24',
		'validate'   => 'numeric',
		'section'    => 'svq_section_bp',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_bp_layout',
		'default'    => '<h4 class="customizer-separator">Pages Layout</h4>',
		'section'    => 'svq_section_bp',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'         => 'bp_layout',
		'type'       => 'image_select',
		'title'      => esc_html__( 'BuddyPress Pages Default Layout', 'seeko' ),
		'default'    => 'default',
		'options'    => [
			                'default' =>
				                [
					                'alt' => 'Default',
					                'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                ]
		                ] + SVQ_FW::get_config( 'layouts' ),
		'section'    => 'svq_section_bp',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_members_dir',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Members Directory Layout', 'seeko' ),
		'default'     => 'full',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + SVQ_FW::get_config( 'layouts' ),
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_profile',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Member Profile Layout', 'seeko' ),
		'default'     => 'default',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + $bp_single_layouts,
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_groups',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Groups Layout', 'seeko' ),
		'default'     => 'full',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + SVQ_FW::get_config( 'layouts' ),
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_group',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Group Page Layout', 'seeko' ),
		'default'     => 'default',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + $bp_single_layouts,
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_activity',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Activity Layout', 'seeko' ),
		'default'     => 'right',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + SVQ_FW::get_config( 'layouts' ),
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_bp'][] = array(
		'id'          => 'bp_layout_register',
		'type'        => 'image_select',
		'title'       => esc_html__( 'Register page Layout', 'seeko' ),
		'default'     => 'default',
		'options'     => [
			                 'default' =>
				                 [
					                 'alt' => 'BuddyPress default',
					                 'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                 ]
		                 ] + SVQ_FW::get_config( 'layouts' ),
		'description' => esc_html__( 'Default option will use BuddyPress default layout, as set above', 'seeko' ),
		'section'     => 'svq_section_bp',
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_performance'][] = array(
		'id'              => 'img_lazy_bp_thumb',
		'type'            => 'radio-buttonset',
		'title'           => esc_html__( 'BuddyPress avatars placeholder', 'seeko' ),
		'default'         => 'thumb',
		'choices'         => [
			'thumb' => esc_html__( 'Small Thumb', 'seeko' ),
			'pixel' => esc_html__( '1x1 pixel', 'seeko' ),
		],
		'section'         => 'svq_section_performance',
		'description'     => esc_html__( 'What to load as the initial image source', 'seeko' ),
		'active_callback' => array(
			array(
				'setting'  => 'img_lazy',
				'operator' => '==',
				'value'    => '1',
			),
		),
	);

	return $sq;

}


//Change page layout to match theme options settings
add_filter( 'site_layout', 'svq_bp_change_layout' );

if ( ! function_exists( 'svq_bp_change_layout' ) ) {
	function svq_bp_change_layout( $layout ) {
		global $bp;

		if ( ! bp_is_blog_page() ) {

			$layout = svq_option( 'bp_layout', 'default' );
			if ( $layout == 'default' ) {
				$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );
			}

			//profile page
			if ( svq_option( 'bp_layout_profile', 'full' ) != 'default' && bp_is_user() ) {
				$layout = svq_option( 'bp_layout_profile', 'full' );
			} //members dir
			elseif ( svq_option( 'bp_layout_members_dir', 'full' ) != 'default'
			         && bp_is_current_component( $bp->members->slug )
			         && bp_is_directory()
			) {
				$layout = svq_option( 'bp_layout_members_dir', 'full' );
			} //groups dir
			elseif ( bp_is_active( 'groups' ) && svq_option( 'bp_layout_groups', 'default' ) != 'default'
			         && bp_is_current_component( $bp->groups->slug )
			) {
				$layout = svq_option( 'bp_layout_groups', 'default' );
			}

			//group page
			if ( bp_is_active( 'groups' ) && svq_option( 'bp_layout_group', 'full' ) != 'default' && bp_is_group() ) {
				$layout = svq_option( 'bp_layout_group', 'full' );
			} //activity page
			elseif ( svq_option( 'bp_layout_activity', 'default' ) != 'default'
			         && bp_is_current_component( $bp->activity->slug ) && ! bp_is_user()
			) {
				$layout = svq_option( 'bp_layout_activity', 'default' );
			} //register page
			elseif ( svq_option( 'bp_layout_register', 'full' ) != 'default'
			         && bp_is_register_page()
			) {
				$layout = svq_option( 'bp_layout_register', 'full' );
			}

		}

		return $layout;
	}
}

/**
 * Get BuddyPress chosen template
 *
 * @param string $area
 *
 * @return array|mixed|string
 */
function svq_bp_get_layout( $area = '' ) {

	$layout = svq_option( 'bp_layout', 'default' );

	if ( $layout == 'default' ) {
		$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );
	}

	if ( 'members_dir' == $area ) {
		if ( svq_option( 'bp_layout_members_dir', 'full' ) !== 'default' ) {
			$layout = svq_option( 'bp_layout_members_dir', 'full' );
		}
	} elseif ( 'groups_dir' == $area ) {
		if ( svq_option( 'bp_layout_groups', 'full' ) !== 'default' ) {
			$layout = svq_option( 'bp_layout_groups', 'full' );
		}
	} elseif ( 'member_profile' == $area ) {
		if ( svq_option( 'bp_layout_profile', 'full' ) !== 'default' ) {
			$layout = svq_option( 'bp_layout_profile', 'full' );
		}
	} elseif ( 'group_profile' == $area ) {
		if ( svq_option( 'bp_layout_group', 'full' ) !== 'default' ) {
			$layout = svq_option( 'bp_layout_group', 'full' );
		}
	}

	return $layout;
}

add_filter( 'bp_get_form_field_attributes', 'svq_bp_button_class', 10, 2 );
function svq_bp_button_class( $attributes = [], $name ) {
	if ( $name == 'submit' ) {
		if ( ! isset( $attributes['class'] ) ) {
			$attributes['class'] = 'btn btn-primary';
		} else {
			$attributes['class'] .= ' btn btn-primary';
		}
	}

	return $attributes;
}

add_filter( 'xprofile_filter_profile_group_tabs', function ( $tabs ) {
	foreach ( $tabs as $k => $tab ) {
		$tabs[ $k ] = str_replace( '<a', '<a class="btn btn-sm"', $tab );
	}

	return $tabs;
} );


add_filter( 'bp_get_button', function ( $button, $args ) {
	if ( isset( $args['svq-extra'] ) ) {
		$button = str_replace( 'href=', $args['svq-extra'] . ' href=', $button );
	}

	return $button;
}, 10, 2 );


/* Cover photo fix when deleting or updating cover */
add_action( 'bp_after_member_header', 'svq_bp_update_cover' );
add_action( 'bp_before_group_settings_cover_image', 'svq_bp_update_cover' );

function svq_bp_update_cover() {
	?>
	<script>
		window.addEventListener('DOMContentLoaded', function () {
			jQuery(document).ready(function () {
				jQuery(document).ajaxComplete(function (event, xhr, settings) {
					if (settings.data) {
						if (settings.data.indexOf("action=bp_cover_image_delete") != -1) {
							jQuery('.bp-member-layout').removeClass('bp-has-cover');
						}
					}
				});
				if (typeof(bp) !== 'undefined' && typeof(bp.Uploader) !== 'undefined' && typeof(bp.Uploader.filesQueue) !== 'undefined') {
					bp.Uploader.filesQueue.on('add', function () {
						jQuery('.bp-member-layout').addClass('bp-has-cover');
					});
				}
			});
		});
	</script>
	<?php

}

/* rtMedia images in header profile */
add_action( 'bp_before_member_header_meta', 'svq_bp_member_photos' );
function svq_bp_member_photos() {

	if ( ! class_exists( 'RTMedia' ) ) {
		return;
	}
	$output = '';

	global $wpdb;
	$user           = svq_get_user();
	$displayed_user = bp_displayed_user_id();
	$table_name     = $wpdb->base_prefix . "rt_rtm_media";
	$query_limit    = apply_filters( 'svq_rtmedia_photo_limit', 6 );
	if ( is_multisite() ) {
		$blog_id = get_current_blog_id();
	} else {
		$blog_id = 1;
	}

	$where = "SELECT * FROM {$table_name}";
	$where .= " WHERE {$table_name}.media_type = 'photo' AND media_author = $displayed_user AND blog_id = $blog_id";
	$where .= " AND ({$table_name}.privacy is NULL OR {$table_name}.privacy<=0";
	$where .= " AND {$table_name}.context = 'profile'";
	if ( $user ) {
		$where .= " OR {$table_name}.privacy=20";
		//if my profile
		if ( $user == $displayed_user || is_super_admin() ) {
			$where .= " OR {$table_name}.privacy >= 40";
		} else {
			if ( class_exists( 'BuddyPress' ) && bp_is_active( 'friends' ) ) {
				$friendship = new RTMediaFriends();
				//my friends
				$friends = $friendship->get_friends_cache( $user );
				//if displayed user is my friend -> view its pictures
				if ( in_array( $displayed_user, $friends ) ) {
					$where .= " OR {$table_name}.privacy=40";
				}
			}
		}
	}
	$where   .= ") ORDER BY media_id DESC LIMIT " . $query_limit;
	$my_rows = $wpdb->get_results( $where );
	$count   = 0;

	if ( $my_rows && count( $my_rows ) > 0 ) {

		$output .= '<h5>' . esc_html__( 'Photos', 'seeko' ) . '</h5>';
		$output .= '<ul id="item-thumbs" class="list-unstyled">';

		$results = count( $my_rows );

		global $rtMediaNav;
		if ( ! isset( $rtMediaNav ) ) {
			$rtMediaNav = new RTMediaNav();
		}
		$profile_counts = $rtMediaNav->actual_counts( bp_displayed_user_id() );
		$profile_counts = (int) rtmedia_number_to_human_readable( $profile_counts['total']['all'] );

		foreach ( $my_rows as $row ) {
			$count ++;
			$src = wp_get_attachment_image_src( $row->media_id, 'rt_media_thumbnail' );

			$hide_last = ( $count == $results ) && $profile_counts > $query_limit;
			$output    .= '<li class="rtmedia-list-media rtm-gallery-list"' . ( $hide_last ? ' style="display: none;"' : '' ) . '>';
			$output    .=
				'<figure class="img-dynamic aspect-ratio avatar-square">' .
				'<a href="' . esc_url( trailingslashit( trailingslashit( get_rtmedia_user_link( $displayed_user ) ) . RTMEDIA_MEDIA_SLUG . '/' . rtmedia_id( $row->media_id ) ) ) . '" class="img-card rtmedia-list-item-a">' .
				'<img src="' . $src[0] . '" alt="' . esc_attr( rtmedia_id( $row->media_id ) ) . '">' .
				'</a>' .
				'</figure>';
			$output    .= '</li>';

		}

		if ( $profile_counts > $query_limit ) {
			$output .= '<li>';
			$output .= '<a href="' . trailingslashit( get_rtmedia_user_link( $displayed_user ) ) . RTMEDIA_MEDIA_SLUG . '/" class="bp-more-photos">' .
			           sprintf( esc_html__( '%s more', 'seeko' ), '<span>' . ( $profile_counts - $query_limit + 1 ) . '</span>' ) .
			           '</a>';
			$output .= '</li>';
		}

		$output .= '</ul>';
	}
	$output_escaped = $output;

	// This variable has been safely escaped in the logic above
	echo $output_escaped; // WPCS: XSS OK.
}

if ( class_exists( 'BP_Match' ) ) {
	remove_action( 'bp_before_member_header_meta', [ sq_bp_matching()->public, 'compatibility_match' ] );
	add_action( 'bp_member_header_list_items', 'svq_bp_match_score', 20 );

	function svq_bp_match_score() {

		global $bp;
		if ( ! is_user_logged_in() || bp_is_my_profile() ) {
			return;
		}
		$match_score = sq_bp_matching()->public->compatibility_score( $bp->loggedin_user->id, bp_displayed_user_id() );

		if ( ! $match_score ) {
			return;
		}
		?>
		<div class="svq-match" title="<?php esc_attr_e( 'Compatibility', 'seeko' ); ?>">
			<span><?php echo esc_html( $match_score ); ?>%</span>
			<div class="progress">
				<div class="progress-bar bg-tertiary" role="progressbar"
				     style="width: <?php echo esc_attr( $match_score ); ?>%;" aria-valuenow="75"
				     aria-valuemin="0" aria-valuemax="100"></div>
			</div>

		</div>
		<?php
	}
}

function svq_get_user() {
	if ( is_user_logged_in() ) {
		$user = get_current_user_id();
	} else {
		$user = 0;
	}

	return $user;
}

function svq_bp_member_has_cover( $user_id = false ) {

	if ( ! $user_id ) {
		if ( bp_is_user() ) {
			$user_id = bp_displayed_user_id();
		} elseif ( bp_is_members_directory() ) {
			$user_id = bp_get_member_user_id();
		}
	}

	if ( ! $user_id ) {
		return false;
	}

	if ( version_compare( BP_VERSION, '2.4', '>=' ) && bp_displayed_user_use_cover_image_header() &&
	     bp_attachments_get_attachment( 'url', array(
		     'item_id'    => $user_id,
		     'type'       => 'cover-image',
		     'object_dir' => 'members'
	     ) )
	) {
		return true;
	}

	return false;
}

function svq_bp_group_has_cover( $group_id = false ) {
	if ( ! $group_id ) {
		if ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		} elseif ( bp_is_groups_directory() ) {
			$group_id = bp_get_group_id();
		}
	}
	if ( ! $group_id ) {
		return false;
	}


	if ( version_compare( BP_VERSION, '2.4', '>=' ) && bp_group_use_cover_image_header() &&
	     bp_attachments_get_attachment(
		     'url',
		     array( 'item_id' => $group_id, 'type' => 'cover-image', 'object_dir' => 'groups' )
	     )
	) {
		return true;
	}

	return false;
}


/* Notifications page */
add_filter( 'bp_get_the_notification_mark_read_link', function ( $retval ) {
	$retval = str_replace( 'mark-read', 'mark-read btn btn-xs', $retval );

	return $retval;
} );

add_filter( 'bp_get_the_notification_mark_unread_link', function ( $retval ) {
	$retval = str_replace( 'bp-tooltip', 'btn btn-xs', $retval );

	return $retval;
} );

add_filter( 'bp_get_the_notification_delete_link', function ( $retval, $user_id ) {

	$retval = sprintf( '<a href="%1$s" class="delete secondary confirm btn btn-xs">%2$s</a>', esc_url( bp_get_the_notification_delete_url( $user_id ) ), '<i class="icon icon-trash"></i>' );

	return $retval;
}, 10, 2 );


/* Groups */
add_action( 'bp_directory_groups_actions', function () {
	?>
	<div class="meta btn-tag btn-tag-sm">
		<i class="icon icon-members"></i>
		<span><?php echo (int) bp_get_group_member_count(); ?></span>
	</div>
	<?php
}, 8 );

add_filter( 'bp_get_group_join_button', function ( $button ) {

	$button['svq-extra'] = 'data-svq-title="' . esc_attr( $button['link_text'] ) . '"';
	if ( isset( $button['id'] ) && 'membership_requested' == $button['id'] ) {
		$button['svq-extra'] .= ' data-pending="' . esc_attr__( 'Request Sent', 'buddypress' ) . '"';
	}

	$button['link_text']  = '<span></span>';
	$button['link_class'] .= ' bp-btn btn-square btn';

	$button['link_class'] .= ' btn-sm';


	return $button;
} );


add_filter( 'bp_directory_members_search_form', function ( $search_form_html ) {
	$query_arg = bp_core_get_component_search_query_arg( 'members' );

	if ( ! empty( $_REQUEST[ $query_arg ] ) ) {
		$search_value = stripslashes( $_REQUEST[ $query_arg ] );
	} else {
		$search_value = bp_get_search_default_text( 'members' );
	}

	$search_form_html = '<form action="" method="get" id="search-members-form">
		<label for="members_search"></label>
		<input type="text" name="' . esc_attr( $query_arg ) . '" id="members_search" placeholder="' . esc_attr( $search_value ) . '" />
		<button type="submit" class="btn-primary" id="members_search_submit" name="members_search_submit"><i class="icon icon-search"></i></button>
	</form>';

	return $search_form_html;
}, 20 );


add_filter( 'body_class', 'svq_bp_classes' );

function svq_bp_classes( $classes ) {
	if ( svq_bp_is_single_page() ) {
		$classes[] = 'bp-single-page';
	}
	$thumb_size = svq_option( 'thumb_size', SVQ_FW::get_config( 'default_avatar_size' ) );
	$classes[]  = 'sq-' . $thumb_size . '-avatar';

	$thumb_style = svq_option( 'thumb_style', SVQ_FW::get_config( 'default_avatar_style' ) );
	if ( $thumb_size == 'small' && $thumb_style == 'round' ) {
		$classes[] = 'sq-round-avatar';
	}

	if ( bp_is_active( 'messages' ) && is_user_logged_in() ) {
		$classes[] = 'sq-messages-active';
	}

	return $classes;
}


function svq_bp_is_single_page() {
	if ( ! bp_is_directory() && ( ( bp_is_group() && ! bp_is_group_create() ) || ( bp_is_user() && ! bp_is_single_activity() ) ) ) {
		return true;
	}

	return false;
}


/***************************************************
 * :: Buddypress cover compatibility
 ***************************************************/


function svq_bp_cover_image_css( $settings = array() ) {
	/**
	 * If you are using a child theme, use bp-child-css
	 * as the theme handle
	 */
	$theme_handle = 'seeko';

	$settings['theme_handle'] = $theme_handle;
	$settings['width']        = 768;
	$settings['height']       = 400;

	/**
	 * Then you'll probably also need to use your own callback function
	 * @see the previous snippet
	 */
	$settings['callback'] = 'svq_bp_legacy_theme_cover_image';


	return $settings;
}

add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'svq_bp_cover_image_css', 10, 1 );
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'svq_bp_cover_image_css', 10, 1 );


/**
 * BP Legacy's callback for the cover image feature.
 *
 * @since  2.4.0
 *
 * @param  array $params the current component's feature parameters.
 *
 * @return array          an array to inform about the css handle to attach the css rules to
 */
function svq_bp_legacy_theme_cover_image( $params = array() ) {

	if ( empty( $params ) || ( isset( $params['cover_image'] ) && ! $params['cover_image'] ) ) {
		return;
	}


	$cover_image = 'background-image: url(' . $params['cover_image'] . '); ' .
	               'background-repeat: no-repeat; background-size: cover; background-position: center center !important;';

	return '
		/* Cover image */
		body.buddypress div#item-header #header-cover-image {
			' . $cover_image . '
		}';
}


/* Get User online */
if ( ! function_exists( 'svq_is_user_online' ) ):
	/**
	 * Check if a Buddypress member is online or not
	 * @global object $wpdb
	 *
	 * @param integer $user_id
	 * @param integer $time
	 *
	 * @return boolean
	 */
	function svq_is_user_online( $user_id, $time = 5 ) {
		global $wpdb;
		$sql        = $wpdb->prepare( "SELECT u.user_login FROM $wpdb->users u JOIN $wpdb->usermeta um ON um.user_id = u.ID
			WHERE u.ID = %d
			AND um.meta_key = 'last_activity'
			AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= UTC_TIMESTAMP()", $user_id, $time );
		$user_login = $wpdb->get_var( $sql );
		if ( isset( $user_login ) && $user_login != "" ) {
			return true;
		} else {
			return false;
		}
	}
endif;


if ( ! function_exists( 'svq_get_member_online_class' ) ) {
	function svq_get_member_online_class( $user_id ) {
		$output = '';
		if ( svq_is_user_online( $user_id ) ) {
			$output .= ' avatar-online';
		}

		return $output;
	}
}

if ( svq_option( 'img_lazy', 1 ) ) {
	add_filter( 'bp_core_fetch_avatar', 'svq_bp_make_lazy_core_avatar', 10, 2 );
}

add_filter( 'bp_activity_allowed_tags', function ( $allowedtags ) {
	return array_merge_recursive( array(
		'img' => array(
			'data-src' => array(),
		),
	), $allowedtags );

	return $allowedtags;
} );

/**
 * @param $avatar
 * @param $params
 *
 * @return mixed
 */
function svq_bp_make_lazy_core_avatar( $avatar, $params ) {

	//if lazy disabled return early
	if ( apply_filters( 'seeko_img_progressive_bail', false, $avatar, 'bp_avatar' ) == true ) {
		return $avatar;
	}

	if ( strpos( $params['class'], 'lazy-disabled' ) !== false ) {
		return $avatar;
	}

	if ( svq_option( 'img_lazy_bp_thumb', 'thumb' ) !== 'pixel' && $params['type'] == 'thumb' ) {
		return $avatar;
	}

	$classes = 'img-progressive progressive--not-loaded ';

	if ( svq_option( 'img_lazy_bp_thumb', 'thumb' ) == 'pixel' ) {
		$small_avatar = get_template_directory_uri() . '/assets/img/1x1.png';
	}

	if ( $params['object'] == 'user' ) {
		if ( ! isset( $small_avatar ) ) {
			$small_avatar = bp_core_fetch_avatar( array(
				'item_id' => $params['item_id'],
				'type'    => 'thumb',
				'html'    => false
			) );
		}
	} elseif ( $params['object'] == 'group' ) {
		if ( ! isset( $small_avatar ) ) {
			$small_avatar = bp_core_fetch_avatar( array(
				'item_id'    => $params['item_id'],
				'type'       => 'thumb',
				'html'       => false,
				'avatar_dir' => 'group-avatars',
				'object'     => 'group',
			) );
		}
	}

	$avatar = str_replace( 'src=', 'src=' . $small_avatar . ' data-src=', $avatar );
	$avatar = str_replace( 'class="', 'class="' . $classes, $avatar );

	return $avatar;

}

/**
 * @param bool $disable_lazy
 */
function svq_bp_displayed_avatar( $disable_lazy = false ) {
	$extra_params = '';
	$avatar_size  = svq_option( 'thumb_size', SVQ_FW::get_config( 'default_avatar_size' ) );
	if ( $avatar_size == 'small' ) {
		$extra_params .= '&width=200&height=200';
	}

	if ( $disable_lazy == true ) {
		$extra_params .= '&class=lazy-disabled';
	}

	bp_displayed_user_avatar( 'type=full' . $extra_params );
}

/**
 * @param bool $disable_lazy
 */
function svq_bp_loop_avatar( $disable_lazy = false ) {
	$extra_params = '';
	$avatar_size  = svq_option( 'thumb_size', SVQ_FW::get_config( 'default_avatar_size' ) );
	if ( $avatar_size == 'small' ) {
		$extra_params .= '&width=200&height=200';
	}
	if ( $disable_lazy == true ) {
		$extra_params .= '&class=lazy-disabled';
	}

	bp_member_avatar( 'type=full' . $extra_params );
}

/**
 * @param bool $disable_lazy
 */
function svq_bp_group_avatar( $disable_lazy = false ) {
	$extra_params = '';
	$avatar_size  = svq_option( 'thumb_size', SVQ_FW::get_config( 'default_avatar_size' ) );
	if ( $avatar_size == 'small' ) {
		$extra_params .= '&width=200&height=200';
	}

	if ( $disable_lazy == true ) {
		$extra_params .= '&class=lazy-disabled';
	}

	bp_group_avatar( 'type=full' . $extra_params );
}


$thumb_size = svq_option( 'thumb_size', SVQ_FW::get_config( 'default_avatar_size' ) );
if ( $thumb_size == 'small' ) {
	add_filter( 'svq_rtmedia_photo_limit', function () {
		return 4;
	} );
}


/***************************************************
 * :: Catch AJAX requests
 ***************************************************/

add_action( 'wp_ajax_svq_bp_ajax_call', 'svq_bp_ajax_call' );

function svq_bp_ajax_call() {

	$response = array();
	$response = apply_filters( 'svq_bp_ajax_call', $response );

	if ( ! empty( $response ) ) {
		echo json_encode( $response );
	}
	exit;
}


/***************************************************
 * :: Member Types
 ***************************************************/

//add_action( 'bp_members_directory_member_types', 'svq_bp_member_types_tabs' );
function svq_bp_member_types_tabs() {
	if ( ! bp_get_current_member_type() ) {
		$member_types = bp_get_member_types( array(), 'objects' );
		if ( $member_types ) {
			foreach ( $member_types as $member_type ) {
				if ( $member_type->has_directory == 1 ) {
					echo '<li id="members-' . esc_attr( $member_type->name ) . '" class="bp-member-type-filter">';
					echo '<a href="' . bp_get_members_directory_permalink() . 'type/' . $member_type->directory_slug . '/">' . sprintf( '%s <span>%d</span>', $member_type->labels['name'], svq_bp_count_member_types( $member_type->name ) ) . '</a>';
					echo '</li>';
				}
			}
		}
	}
}


add_filter( 'bp_modify_page_title', 'svq_bp_members_type_directory_page_title', 9, 4 );
function svq_bp_members_type_directory_page_title( $title, $original_title, $sep, $seplocation ) {
	$member_type = bp_get_current_member_type();
	if ( bp_is_directory() && $member_type ) {
		$member_type = bp_get_member_type_object( $member_type );
		if ( $member_type ) {
			global $post;
			$post->post_title = $member_type->labels['name'];
			$title            = $member_type->labels['name'] . " " . $sep . " " . $original_title;
		}
	}

	return $title;
}


add_filter( 'bp_get_total_member_count', 'svq_bp_get_total_member_count_member_type' );
function svq_bp_get_total_member_count_member_type() {
	$count       = bp_core_get_active_member_count();
	$member_type = bp_get_current_member_type();
	if ( bp_is_directory() && $member_type ) {
		$count = svq_bp_count_member_types( $member_type );
	}

	return $count;
}


add_filter( 'bp_get_total_friend_count', 'svq_bp_get_total_friend_count_member_type' );
function svq_bp_get_total_friend_count_member_type() {
	$user_id     = get_current_user_id();
	$count       = friends_get_total_friend_count( $user_id );
	$member_type = bp_get_current_member_type();
	if ( bp_is_directory() && $member_type ) {
		global $bp, $wpdb;
		$friends = $wpdb->get_results( "SELECT count(1) as count FROM {$bp->friends->table_name} bpf
        LEFT JOIN {$wpdb->term_relationships} tr ON (bpf.initiator_user_id = tr.object_id || bpf.friend_user_id = tr.object_id )
        LEFT JOIN {$wpdb->terms} t ON t.term_id = tr.term_taxonomy_id
        WHERE t.slug = '" . $member_type . "' AND (bpf.initiator_user_id = $user_id || bpf.friend_user_id = $user_id ) AND tr.object_id != $user_id AND bpf.is_confirmed = 1", ARRAY_A );
		$count   = 0;
		if ( isset( $friends['0']['count'] ) && is_numeric( $friends['0']['count'] ) ) {
			$count = $friends['0']['count'];
		}
	}

	return $count;
}


function svq_bp_count_member_types( $member_type = '' ) {
	if ( ! bp_is_root_blog() ) {
		switch_to_blog( bp_get_root_blog_id() );
	}
	global $wpdb;
	$sql           = array(
		'select' => "SELECT t.slug, tt.count FROM {$wpdb->term_taxonomy} tt LEFT JOIN {$wpdb->terms} t",
		'on'     => 'ON tt.term_id = t.term_id',
		'where'  => $wpdb->prepare( 'WHERE tt.taxonomy = %s', 'bp_member_type' ),
	);
	$members_count = $wpdb->get_results( join( ' ', $sql ) );
	$members_count = wp_filter_object_list( $members_count, array( 'slug' => $member_type ), 'and', 'count' );
	$members_count = array_values( $members_count );
	if ( isset( $members_count[0] ) && is_numeric( $members_count[0] ) ) {
		$members_count = $members_count[0];
	} else {
		$members_count = 0;
	}
	restore_current_blog();

	return $members_count;
}


add_filter( 'bp_before_has_members_parse_args', 'svq_bp_set_has_members_type_arg', 10, 1 );
function svq_bp_set_has_members_type_arg( $args ) {
	$member_type  = bp_get_current_member_type();
	$member_types = bp_get_member_types( array(), 'names' );
	if ( isset( $args['scope'] ) && ! isset( $args['member_type'] ) && in_array( $args['scope'], $member_types ) ) {
		if ( $member_type ) {
			unset( $args['scope'] );
		} else {
			$args['member_type'] = $args['scope'];
		}
	}

	return $args;
}


if ( ! function_exists( 'sq_bp_member_stats' ) ) {
	function sq_bp_member_stats( $field = false, $value = false, $online = false ) {
		global $wpdb;

		if ( $field && $value ) {
			$where = " WHERE field_id = '" . $field . "' AND value = '" . esc_sql( $value ) . "'";
		} else {
			$where = '';
		}
		$sql = "SELECT " . $wpdb->base_prefix . "bp_xprofile_data.user_id FROM " . $wpdb->base_prefix . "bp_xprofile_data
				JOIN " . $wpdb->base_prefix . "bp_xprofile_fields ON " . $wpdb->base_prefix . "bp_xprofile_data.field_id = " . $wpdb->base_prefix . "bp_xprofile_fields.id
				$where";

		$match_ids = $wpdb->get_col( $sql );

		if ( ! $online ) {
			return count( array_unique( $match_ids ) );
		}

		if ( ! $match_ids ) {
			$match_ids = array( 0 );
		}

		if ( ! empty( $match_ids ) ) {
			$include_members = '&include=' . join( ",", $match_ids );
		} else {
			$include_members = '';
		}

		$i = 0;
		if ( bp_has_members( 'user_id=0&type=online&per_page=999999999&populate_extras=0' . $include_members ) ) {
			while ( bp_members() ) {
				bp_the_member();
				$i ++;
			}
		}

		return apply_filters( 'sq_bp_member_stats', $i, $value );
	}

}

if ( ! function_exists( 'bp_get_online_users' ) ):
	/**
	 * Return Buddypress online users
	 * @global object $wpdb
	 *
	 * @param string $value
	 *
	 * @return integer
	 */
	function bp_get_online_users( $value = false, $field = false ) {
		global $wpdb;
		$match_ids = array();

		/* If we want to get specific members */
		if ( $value ) {

			if ( ! $field ) {
				$default_sex = get_profile_id_by_name( 'I am a' );
				$sex         = sq_option( 'bp_sex_field', $default_sex );
				if ( $sex == 0 ) {
					$sex = $default_sex;
				}
			} else {
				if ( is_numeric( $field ) ) {
					$sex = $field;
				} else {
					$sex = get_profile_id_by_name( $field );
				}
			}

			$where = " WHERE field_id = '" . $sex . "' AND value = '" . esc_sql( $value ) . "'";
			$sql   = "SELECT " . $wpdb->base_prefix . "bp_xprofile_data.user_id FROM " . $wpdb->base_prefix . "bp_xprofile_data
							$where";

			$match_ids = $wpdb->get_col( $sql );
			if ( ! $match_ids ) {
				$match_ids = array( 0 );
			}
		}
		$i = 0;

		if ( ! empty( $match_ids ) ) {
			$include_members = '&include=' . join( ",", $match_ids );
		} else {
			$include_members = '';
		}

		if ( bp_has_members( 'user_id=0&type=online&per_page=99999999&populate_extras=0' . $include_members ) ) :
			while ( bp_members() ) : bp_the_member();
				$i ++;
			endwhile;
		endif;

		return apply_filters( 'kleo_online_users_count', $i, $value );
	}
endif;


/** Buttons ***********************************************************/

if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

	// Messages button.
	if ( bp_is_active( 'messages' ) ) {
		add_action( 'bp_directory_members_actions', 'svq_bp_dir_send_private_message_button', 8 );
	}
}
function svq_bp_dir_send_private_message_button() {
	if ( bp_get_member_user_id() != bp_loggedin_user_id() ) {

		/* removed since it is used in bp_get_send_message_button_args */
		//add_filter( 'bp_get_send_private_message_link', 'seeko_filter_message_button_link', 1, 1 );

		add_filter( 'bp_get_send_message_button_args', 'svq_bp_private_msg_args', 1 );
		bp_send_message_button();
	}
}

add_action( 'init', function () {
	if ( bp_is_active( 'friends' ) ) {
		remove_action( 'bp_member_header_actions', 'bp_add_friend_button', 5 );
		add_action( 'bp_member_header_after_avatar', 'bp_add_friend_button' );
	}

	add_filter( 'bp_get_send_public_message_button', function ( $button ) {
		$button['link_class'] .= ' btn btn-sm';
		$button['link_text']  = '<span>' . esc_html__( 'Mention', 'seeko' ) . '</span>';

		return $button;
	} );

	if ( bp_is_active( 'messages' ) ) {
		remove_action( 'bp_member_header_actions', 'bp_send_private_message_button', 20 );
		add_action( 'bp_member_header_actions', 'bp_send_private_message_button', 1 );
		add_filter( 'bp_get_send_message_button_args', function ( $button ) {
			$button['link_class'] .= ' btn btn-sm';
			$button['link_text']  = '<span>' . esc_html__( 'Message', 'seeko' ) . '</span>';

			return $button;
		} );
	}

	if ( bp_is_active( 'groups' ) ) {
		remove_action( 'bp_group_header_actions', 'bp_group_join_button', 5 );
		add_action( 'bp_group_header_after_avatar', 'bp_group_join_button' );
	}

} );

/* Private message in Members directory loop */
/**
 * Override default BP private message button to work on Friends tab
 * @since 2.2
 *
 * @param array $btn
 *
 * @return array
 */
function svq_bp_private_msg_args( $btn ) {

	if ( ! is_user_logged_in() ) {
		return $btn;
	}

	$btn['link_href'] = seeko_filter_message_button_link();

	return $btn;
}

function seeko_filter_message_button_link( $link = '' ) {
	$bp_user_id = ( bp_get_member_user_id() ? bp_get_member_user_id() : bp_displayed_user_id() );
	$link       = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $bp_user_id ) );

	add_filter( 'bp_displayed_user_id', 'seeko_compat_message_directory' );
	$link = apply_filters( 'bp_get_send_private_message_link', $link );
	remove_filter( 'bp_displayed_user_id', 'seeko_compat_message_directory' );

	return $link;
}

function seeko_compat_message_directory( $id ) {
	if ( bp_is_friends_component() || $id == 0 ) {
		$id = bp_get_member_user_id();
	}

	return $id;
}

/* Get current Buddypress page */
function svq_bp_get_component_id() {
	$current_page_id = null;
	$page_array      = get_option( 'bp-pages' );

	if ( bp_is_register_page() ) { /* register page */
		$current_page_id = $page_array['register'];
	} elseif ( bp_is_members_component() || bp_is_user() ) { /* members component */
		$current_page_id = $page_array['members'];
	} elseif ( bp_is_activity_directory() ) { /* activity directory */
		$current_page_id = $page_array['activity'];
	} elseif ( bp_is_groups_directory() || bp_is_group_single() ) { /* groups directory */
		$current_page_id = $page_array['groups'];
	} elseif ( bp_is_activation_page() ) { /* activation page */
		$current_page_id = $page_array['activate'];
	}

	return $current_page_id;
}

if ( ! function_exists( 'svq_bp_fields_array' ) ) {
	function svq_bp_fields_array( $multi = true ) {

		$kleo_bp_textboxes = array();
		$kleo_bp_multi     = array();
		$kleo_bp_dateboxes = array();

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'xprofile' ) ) :
			if ( function_exists( 'bp_has_profile' ) ) :
				if ( bp_has_profile( 'hide_empty_fields=0' ) ) :
					while ( bp_profile_groups() ) :
						bp_the_profile_group();
						while ( bp_profile_fields() ) :
							bp_the_profile_field();


							switch ( bp_get_the_profile_field_type() ) {
								case 'datebox':
								case 'birthdate':
									$kleo_bp_dateboxes[ bp_get_the_profile_field_id() ] = bp_get_the_profile_field_name();
									break;
								case 'textbox':
								case 'selectbox':
								case 'radio':
									$kleo_bp_textboxes[ bp_get_the_profile_field_id() ] = bp_get_the_profile_field_name();
									break;

								case 'multiselectbox':
								case 'checkbox':
									$kleo_bp_multi[ bp_get_the_profile_field_id() ] = bp_get_the_profile_field_name();
									break;
							}


						endwhile;
					endwhile;
				endif;
			endif;
		endif;

		return $kleo_bp_textboxes + $kleo_bp_multi + $kleo_bp_dateboxes;
	}
}

function svq_bp_custom_ids( $field_id, $field_value = '' ) {

	if ( empty( $field_id ) ) {
		return '';
	}
	global $bp, $wpdb;

	$the_field = new BP_XProfile_Field( $field_id );

	$query = "SELECT user_id FROM " . $bp->profile->table_name_data . " WHERE field_id = " . $field_id;

	if ( $field_value != '' ) {
		if ( $the_field->type_obj->supports_multiple_defaults ) {
			$query .= " AND value LIKE '%" . $field_value . "%'";
		} else {
			$query .= " AND value = '" . $field_value . "'";
		}
	}

	$custom_ids = $wpdb->get_col( $query );

	if ( ! empty( $custom_ids ) ) {
		// convert the array to a csv string
		$custom_ids_str = '&include=' . implode( ",", $custom_ids );

		return $custom_ids_str;
	} else {
		return '&include=';
	}

}

add_filter( 'svq_elementor_is_full_page', function ( $response ) {
	$page_id = svq_bp_get_component_id();

	if ( $page_id &&
	     get_post_meta( $page_id, '_elementor_data', true ) &&
	     get_post_meta( $page_id, '_elementor_edit_mode', true ) ) {
		$response = true;
	}

	return $response;
} );

//Remove Facebook login button from BP register page
remove_action( 'bp_before_register_page', 'svq_fb_button' );


/***
 * Rewritten functions
 */


/**
 * Output the dropdown for bulk management of notifications.
 *
 * @since 2.2.0
 */
function svq_bp_notifications_bulk_management_dropdown() {
	?>
	<label class="bp-screen-reader-text" for="notification-select"><?php
		/* translators: accessibility text */
		esc_html_e( 'Select Bulk Action', 'buddypress' );
		?></label>
	<select name="notification_bulk_action" id="notification-select">
		<option value="" selected="selected"><?php esc_html_e( 'Bulk Actions', 'buddypress' ); ?></option>

		<?php if ( bp_is_current_action( 'unread' ) ) : ?>
			<option value="read"><?php esc_html_e( 'Mark read', 'buddypress' ); ?></option>
		<?php elseif ( bp_is_current_action( 'read' ) ) : ?>
			<option value="unread"><?php esc_html_e( 'Mark unread', 'buddypress' ); ?></option>
		<?php endif; ?>
		<option value="delete"><?php esc_html_e( 'Delete', 'buddypress' ); ?></option>
	</select>
	<input type="submit" id="notification-bulk-manage" class="button action btn-xs"
	       value="<?php esc_attr_e( 'Apply', 'buddypress' ); ?>">
	<?php
}

/**
 * Output the dropdown for bulk management of messages.
 *
 * @since 2.2.0
 */
function svq_bp_messages_bulk_management_dropdown() {
	?>
	<label class="bp-screen-reader-text" for="messages-select"><?php
		esc_html_e( 'Select Bulk Action', 'buddypress' );
		?></label>
	<select name="messages_bulk_action" id="messages-select">
		<option value="" selected="selected"><?php esc_html_e( 'Bulk Actions', 'buddypress' ); ?></option>
		<option value="read"><?php esc_html_e( 'Mark read', 'buddypress' ); ?></option>
		<option value="unread"><?php esc_html_e( 'Mark unread', 'buddypress' ); ?></option>
		<option value="delete"><?php esc_html_e( 'Delete', 'buddypress' ); ?></option>
		<?php
		/**
		 * Action to add additional options to the messages bulk management dropdown.
		 *
		 * @since 2.3.0
		 */
		do_action( 'bp_messages_bulk_management_dropdown' );
		?>
	</select>
	<input type="submit" id="messages-bulk-manage" class="button action btn-xs"
	       value="<?php esc_attr_e( 'Apply', 'buddypress' ); ?>">
	<?php
}

/* global search plugin */
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'bboss_global_search_ajax' ) {
		add_filter( 'seeko_img_progressive_bail', '__return_true', 12 );
	}
}