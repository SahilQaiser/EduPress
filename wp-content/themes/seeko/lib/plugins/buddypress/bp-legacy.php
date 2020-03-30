<?php
class SVQ_Buddypress_Legacy {

	private $version;

	public function __construct() {

		$this->version = bp_get_version();

		/** Scripts ***********************************************************/
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) ); // Enqueue theme CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 ); // Enqueue theme JS

		add_action( 'wp_enqueue_scripts', function() {
			wp_dequeue_script( 'bp-legacy-js' );
			wp_deregister_script( 'bp-legacy-js' );
		}); // Dequeue theme JS

		add_action ( 'wp_ajax_post_update', [ $this, 'post_update' ], 9 );
		add_action ( 'wp_ajax_nopriv_post_update', [ $this, 'post_update' ], 9 );

		//Favorite / Unfavorite
		add_action ( 'wp_ajax_activity_mark_fav', [ $this, 'mark_activity_favorite' ], 9 );
		add_action ( 'wp_ajax_nopriv_activity_mark_fav', [ $this, 'mark_activity_favorite' ], 9 );
		add_action ( 'wp_ajax_activity_mark_unfav', [ $this, 'unmark_activity_favorite' ], 9 );
		add_action ( 'wp_ajax_nopriv_activity_mark_unfav', [ $this, 'unmark_activity_favorite' ], 9 );

		// addremove_friend
		add_action ( 'wp_ajax_addremove_friend', [ $this, 'ajax_addremove_friend' ], 9 );
		add_action ( 'wp_ajax_nopriv_addremove_friend', [ $this, 'ajax_addremove_friend' ], 9 );

		// joinleave_group
		add_action ( 'wp_ajax_joinleave_group', [ $this, 'ajax_joinleave_group' ], 9 );
		add_action ( 'wp_ajax_nopriv_joinleave_group', [ $this, 'ajax_joinleave_group' ], 9 );

		add_filter( 'bp_get_add_friend_button', [ $this, 'bp_get_add_friend_button' ] );

	}

	/**
	 * Load the theme CSS
	 *
	 * @since 1.7.0
	 * @since 2.3.0 Support custom CSS file named after the current theme or parent theme.
	 *
	 */
	public function enqueue_styles() {

		$min = bp_core_get_minified_asset_suffix();

		// Locate the BP stylesheet.
		$ltr = $this->locate_asset_in_stack( "buddypress{$min}.css",     'css' );

		wp_deregister_style( 'bp-legacy-css' );
		wp_dequeue_style( 'bp-legacy-css' );

		// LTR.
		if ( ! is_rtl() && isset( $ltr['location'], $ltr['handle'] ) ) {

			if ( $min ) {
				$ltr['location'] = str_replace( '.css', '.min.css', $ltr['location'] );
			}
			wp_enqueue_style( $ltr['handle'], $ltr['location'], array(), $this->version, 'screen' );
		}

		// RTL.
		if ( is_rtl() ) {
			$rtl = $this->locate_asset_in_stack( "buddypress-rtl{$min}.css", 'css' );

			if ( isset( $rtl['location'], $rtl['handle'] ) ) {
				$rtl['handle'] = str_replace( '-css', '-css-rtl', $rtl['handle'] );  // Backwards compatibility.
				wp_enqueue_style( $rtl['handle'], $rtl['location'], array(), $this->version, 'screen' );

				if ( $min ) {
					wp_style_add_data( $rtl['handle'], 'suffix', $min );
				}
			}
		}

	}

	/**
	 * Enqueue the required JavaScript files
	 *
	 * @since 1.7.0
	 */
	public function enqueue_scripts() {
		$min = bp_core_get_minified_asset_suffix();

		// Locate the BP JS file.
		$asset = $this->locate_asset_in_stack( "buddypress{$min}.js", 'js' );

		// Enqueue the global JS, if found - AJAX will not work
		// without it.
		if ( isset( $asset['location'], $asset['handle'] ) ) {

			wp_dequeue_script( $asset['handle'] );
			wp_deregister_script( $asset['handle'] );

			if ( $min ) {
				$asset['location'] = str_replace( '.js', '.min.js', $asset['location'] );
			}

			wp_enqueue_script( $asset['handle'], $asset['location'], bp_core_get_js_dependencies(), $this->version );
		}

		/**
		 * Filters whether directory filter settings ('scope', etc) should be stored in a persistent cookie.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $store_filter_settings Whether to store settings. Defaults to true for logged-in users.
		 */
		$store_filter_settings = apply_filters( 'bp_legacy_store_filter_settings', is_user_logged_in() );

		/**
		 * Filters core JavaScript strings for internationalization before AJAX usage.
		 *
		 * @since 2.0.0
		 *
		 * @param array $value Array of key/value pairs for AJAX usage.
		 */
		$params = apply_filters( 'bp_core_get_js_strings', array(
			// Strings for display.
			'accepted'            => __( 'Accepted', 'buddypress' ),
			'close'               => __( 'Close', 'buddypress' ),
			'comments'            => __( 'comments', 'buddypress' ),
			'leave_group_confirm' => __( 'Are you sure you want to leave this group?', 'buddypress' ),
			'mark_as_fav'	      => __( 'Favorite', 'buddypress' ),
			'my_favs'             => __( 'My Favorites', 'buddypress' ),
			'rejected'            => __( 'Rejected', 'buddypress' ),
			'remove_fav'	      => __( 'Remove Favorite', 'buddypress' ),
			'show_all'            => __( 'Show all', 'buddypress' ),
			'show_all_comments'   => __( 'Show all comments for this thread', 'buddypress' ),
			'show_x_comments'     => __( 'Show all comments (%d)', 'buddypress' ),
			'unsaved_changes'     => __( 'Your profile has unsaved changes. If you leave the page, the changes will be lost.', 'buddypress' ),
			'view'                => __( 'View', 'buddypress' ),

			// Settings.
			'store_filter_settings' => $store_filter_settings,
		) );
		wp_localize_script( $asset['handle'], 'BP_DTheme', $params );

		// Maybe enqueue comment reply JS.
		if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Maybe enqueue password verify JS (register page or user settings page).
		if ( bp_is_register_page() || ( function_exists( 'bp_is_user_settings_general' ) && bp_is_user_settings_general() ) ) {

			// Locate the Register Page JS file.
			$asset = $this->locate_asset_in_stack( "password-verify{$min}.js", 'js', 'bp-legacy-password-verify' );

			$dependencies = array_merge( bp_core_get_js_dependencies(), array(
				'password-strength-meter',
			) );

			// Enqueue script.
			wp_enqueue_script( $asset['handle'] . '-password-verify', $asset['location'], $dependencies, $this->version);
		}

		// Star private messages.
		if ( bp_is_active( 'messages', 'star' ) && bp_is_user_messages() ) {
			wp_localize_script( $asset['handle'], 'BP_PM_Star', array(
				'strings' => array(
					'text_unstar'  => __( 'Unstar', 'buddypress' ),
					'text_star'    => __( 'Star', 'buddypress' ),
					'title_unstar' => __( 'Starred', 'buddypress' ),
					'title_star'   => __( 'Not starred', 'buddypress' ),
					'title_unstar_thread' => __( 'Remove all starred messages in this thread', 'buddypress' ),
					'title_star_thread'   => __( 'Star the first message in this thread', 'buddypress' ),
				),
				'is_single_thread' => (int) bp_is_messages_conversation(),
				'star_counter'     => 0,
				'unstar_counter'   => 0
			) );
		}
	}

	/**
	 * Get the URL and handle of a web-accessible CSS or JS asset
	 *
	 * We provide two levels of customizability with respect to where CSS
	 * and JS files can be stored: (1) the child theme/parent theme/theme
	 * compat hierarchy, and (2) the "template stack" of /buddypress/css/,
	 * /community/css/, and /css/. In this way, CSS and JS assets can be
	 * overloaded, and default versions provided, in exactly the same way
	 * as corresponding PHP templates.
	 *
	 * We are duplicating some of the logic that is currently found in
	 * bp_locate_template() and the _template_stack() functions. Those
	 * functions were built with PHP templates in mind, and will require
	 * refactoring in order to provide "stack" functionality for assets
	 * that must be accessible both using file_exists() (the file path)
	 * and at a public URI.
	 *
	 * This method is marked private, with the understanding that the
	 * implementation is subject to change or removal in an upcoming
	 * release, in favor of a unified _template_stack() system. Plugin
	 * and theme authors should not attempt to use what follows.
	 *
	 * @since 1.8.0
	 * @param string $file A filename like buddypress.css.
	 * @param string $type Optional. Either "js" or "css" (the default).
	 * @param string $script_handle Optional. If set, used as the script name in `wp_enqueue_script`.
	 * @return array An array of data for the wp_enqueue_* function:
	 *   'handle' (eg 'bp-child-css') and a 'location' (the URI of the
	 *   asset)
	 */
	private function locate_asset_in_stack( $file, $type = 'css', $script_handle = '' ) {
		$locations = array();

		// Ensure the assets can be located when running from /src/.
		if ( defined( 'BP_SOURCE_SUBDIRECTORY' ) && BP_SOURCE_SUBDIRECTORY === 'src' ) {
			$file = str_replace( '.min', '', $file );
		}

		// No need to check child if template == stylesheet.
		if ( is_child_theme() ) {
			$locations[] = array(
				'type' => 'bp-child',
				'dir'  => get_stylesheet_directory(),
				'uri'  => get_stylesheet_directory_uri(),
				'file' => $file,
			);

			$locations[] = array(
				'type' => 'bp-child',
				'dir'  => get_stylesheet_directory(),
				'uri'  => get_stylesheet_directory_uri(),
				'file' => str_replace( '.min', '', $file ),
			);
		}

		$locations[] = array(
			'type' => 'bp-parent',
			'dir'  => get_template_directory(),
			'uri'  => get_template_directory_uri(),
			'file' => str_replace( '.min', '', $file ),
		);

		$locations[] = array(
			'type' => 'bp-legacy',
			'dir'  => bp_get_theme_compat_dir(),
			'uri'  => bp_get_theme_compat_url(),
			'file' => $file,
		);

		// Subdirectories within the top-level $locations directories.
		$subdirs = array(
			'buddypress/' . bp_get_theme_package_id() . '/' . $type,
			'community/' . $type,
			$type,
		);

		$retval = array();

		foreach ( $locations as $location ) {
			foreach ( $subdirs as $subdir ) {
				if ( file_exists( trailingslashit( $location['dir'] ) . trailingslashit( $subdir ) . $location['file'] ) ) {
					$retval['location'] = trailingslashit( $location['uri'] ) . trailingslashit( $subdir ) . $location['file'];
					$retval['handle']   = ( $script_handle ) ? $script_handle : "{$location['type']}-{$type}";

					break 2;
				}
			}
		}

		return $retval;
	}

	public function post_update() {
		$bp = buddypress();

		if ( ! bp_is_post_request() ) {
			return;
		}

		// Check the nonce.
		check_admin_referer( 'post_update', '_wpnonce_post_update' );

		if ( ! is_user_logged_in() )
			exit( '-1' );

		if ( empty( $_POST['content'] ) )
			exit( '-1<div id="message" class="error alert alert-warning bp-ajax-message"><p>' . __( 'Please enter some content to post.', 'buddypress' ) . '</p></div>' );

		$activity_id = 0;
		$item_id     = 0;
		$object      = '';


		// Try to get the item id from posted variables.
		if ( ! empty( $_POST['item_id'] ) ) {
			$item_id = (int) $_POST['item_id'];
		}

		// Try to get the object from posted variables.
		if ( ! empty( $_POST['object'] ) ) {
			$object  = sanitize_key( $_POST['object'] );

			// If the object is not set and we're in a group, set the item id and the object
		} elseif ( bp_is_group() ) {
			$item_id = bp_get_current_group_id();
			$object = 'groups';
		}

		if ( ! $object && bp_is_active( 'activity' ) ) {
			$activity_id = bp_activity_post_update( array( 'content' => $_POST['content'], 'error_type' => 'wp_error' ) );

		} elseif ( 'groups' === $object ) {
			if ( $item_id && bp_is_active( 'groups' ) )
				$activity_id = groups_post_update( array( 'content' => $_POST['content'], 'group_id' => $item_id, 'error_type' => 'wp_error' ) );

		} else {

			/** This filter is documented in bp-activity/bp-activity-actions.php */
			$activity_id = apply_filters( 'bp_activity_custom_update', false, $object, $item_id, $_POST['content'] );
		}

		if ( false === $activity_id ) {
			exit( '-1<div id="message" class="error bp-ajax-message"><p>' . __( 'There was a problem posting your update. Please try again.', 'buddypress' ) . '</p></div>' );
		} elseif ( is_wp_error( $activity_id ) && $activity_id->get_error_code() ) {
			exit( '-1<div id="message" class="error bp-ajax-message"><p>' . $activity_id->get_error_message() . '</p></div>' );
		}

		$last_recorded = ! empty( $_POST['since'] ) ? date( 'Y-m-d H:i:s', intval( $_POST['since'] ) ) : 0;
		if ( $last_recorded ) {
			$activity_args = array( 'since' => $last_recorded );
			$bp->activity->last_recorded = $last_recorded;
			add_filter( 'bp_get_activity_css_class', 'bp_activity_newest_class', 10, 1 );
		} else {
			$activity_args = array( 'include' => $activity_id );
		}

		if ( bp_has_activities ( $activity_args ) ) {
			while ( bp_activities() ) {
				bp_the_activity();
				bp_get_template_part( 'activity/entry' );
			}
		}

		if ( ! empty( $last_recorded ) ) {
			remove_filter( 'bp_get_activity_css_class', 'bp_activity_newest_class', 10 );
		}

		exit;
	}

	/**
	 * Mark an activity as a favourite via a POST request.
	 *
	 * @since 1.2.0
	 *
	 * @return string|null HTML
	 */
	function mark_activity_favorite() {
		// Bail if not a POST action.
		if ( ! bp_is_post_request() ) {
			return;
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			return;
		}

		// Either the 'mark' or 'unmark' nonce is accepted, for backward compatibility.
		$nonce = wp_unslash( $_POST['nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'mark_favorite' ) && ! wp_verify_nonce( $nonce, 'unmark_favorite' ) ) {
			return;
		}

		if ( bp_activity_add_user_favorite( $_POST['id'] ) )
			echo '';
		else
			echo '';

		exit;
	}

	/**
	 * Un-favourite an activity via a POST request.
	 *
	 * @since 1.2.0
	 *
	 * @return string|null HTML
	 */
	function unmark_activity_favorite() {
		if ( ! bp_is_post_request() ) {
			return;
		}

		if ( ! isset( $_POST['nonce'] ) ) {
			return;
		}

		// Either the 'mark' or 'unmark' nonce is accepted, for backward compatibility.
		$nonce = wp_unslash( $_POST['nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'mark_favorite' ) && ! wp_verify_nonce( $nonce, 'unmark_favorite' ) ) {
			return;
		}

		if ( bp_activity_remove_user_favorite( $_POST['id'] ) )
			echo '';
		else
			echo '';

		exit;
	}

	/**
	 * Friend/un-friend a user via a POST request.
	 *
	 * @since 1.2.0
	 *
	 * @return string|null HTML
	 */
	function ajax_addremove_friend() {
		if ( ! bp_is_post_request() ) {
			return;
		}

		// Cast fid as an integer.
		$friend_id = (int) $_POST['fid'];

		$user = get_user_by( 'id', $friend_id );
		if ( ! $user ) {
			die( __( 'No member found by that ID.', 'buddypress' ) );
		}

		$extra_class = ' btn-sm';

		// Trying to cancel friendship.
		if ( 'is_friend' == BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $friend_id ) ) {
			check_ajax_referer( 'friends_remove_friend' );

			if ( ! friends_remove_friend( bp_loggedin_user_id(), $friend_id ) ) {
				echo esc_html__( 'Friendship could not be canceled.', 'buddypress' );
			} else {
				echo '<a id="friend-' . esc_attr( $friend_id ) . '" 
			class="friendship-button not_friends add bp-btn btn-square btn' . esc_attr( $extra_class ) . '" rel="add" 
			href="' . wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $friend_id, 'friends_add_friend' ) . '" 
			data-svq-title="'. esc_attr__( 'Add Friend', 'buddypress' ) .'"><span></span></a>';
			}

			// Trying to request friendship.
		} elseif ( 'not_friends' == BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $friend_id ) ) {
			check_ajax_referer( 'friends_add_friend' );

			if ( ! friends_add_friend( bp_loggedin_user_id(), $friend_id ) ) {
				echo __(' Friendship could not be requested.', 'buddypress' );
			} else {
				echo '<a id="friend-' . esc_attr( $friend_id ) . '" class="remove friendship-button pending_friend requested bp-btn btn-square btn' . esc_attr( $extra_class ) . '" rel="remove" href="' . wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests/cancel/' . $friend_id . '/', 'friends_withdraw_friendship' ) . '" data-svq-title="'. esc_attr__( 'Cancel Friendship Request', 'buddypress' ) .'"><span></span></a>';
			}

			// Trying to cancel pending request.
		} elseif ( 'pending' == BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $friend_id ) ) {
			check_ajax_referer( 'friends_withdraw_friendship' );

			if ( friends_withdraw_friendship( bp_loggedin_user_id(), $friend_id ) ) {
				echo '<a id="friend-' . esc_attr( $friend_id ) . '" class="friendship-button not_friends add bp-btn btn-square btn' . esc_attr( $extra_class ) . '" rel="add" href="' . esc_url( wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $friend_id, 'friends_add_friend' ) ) . '" data-svq-title="'. esc_attr__( 'Add Friend', 'buddypress' ) .'"><span></span></a>';
			} else {
				echo esc_html__("Friendship request could not be cancelled.", 'buddypress');
			}

			// Request already pending.
		} else {
			echo esc_html__( 'Request Pending', 'buddypress' );
		}

		exit;
	}

	/**
	 * Join or leave a group when clicking the "join/leave" button via a POST request.
	 *
	 * @since 1.2.0
	 *
	 * @return string|null HTML
	 */
	function ajax_joinleave_group() {
		if ( ! bp_is_post_request() ) {
			return;
		}

		// Cast gid as integer.
		$group_id = (int) $_POST['gid'];

		if ( groups_is_user_banned( bp_loggedin_user_id(), $group_id ) )
			return;

		if ( ! $group = groups_get_group( $group_id ) )
			return;

		if ( ! groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
			if ( bp_current_user_can( 'groups_join_group', array( 'group_id' => $group->id ) ) ) {
				check_ajax_referer( 'groups_join_group' );

				if ( ! groups_join_group( $group->id ) ) {
					esc_html_e( 'Error joining group', 'buddypress' );
				} else {
					echo '<a id="group-' . esc_attr( $group->id ) . '" class="group-button leave-group bp-btn btn-square btn btn-sm" rel="leave" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ) . '" data-svq-title="' . esc_attr__( 'Leave Group', 'buddypress' ) . '"><span></span></a>';
				}

			} elseif ( bp_current_user_can( 'groups_request_membership', array( 'group_id' => $group->id ) ) ) {

				// If the user has already been invited, then this is
				// an Accept Invitation button.
				if ( groups_check_user_has_invite( bp_loggedin_user_id(), $group->id ) ) {
					check_ajax_referer( 'groups_accept_invite' );

					if ( ! groups_accept_invite( bp_loggedin_user_id(), $group->id ) ) {
						esc_html_e( 'Error requesting membership', 'buddypress' );
					} else {
						echo '<a id="group-' . esc_attr( $group->id ) . '" class="group-button leave-group bp-btn btn-square btn btn-sm" rel="leave" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ) . '" data-svq-title="' . esc_attr__( 'Leave Group', 'buddypress' ) . '"><span></span></a>';
					}

					// Otherwise, it's a Request Membership button.
				} else {
					check_ajax_referer( 'groups_request_membership' );

					if ( ! groups_send_membership_request( bp_loggedin_user_id(), $group->id ) ) {
						esc_html_e( 'Error requesting membership', 'buddypress' );
					} else {
						echo '<a id="group-' . esc_attr( $group->id ) . '" class="group-button disabled pending membership-requested bp-btn btn-square btn btn-sm" rel="membership-requested" href="' . bp_get_group_permalink( $group ) . '" data-svq-title="' . esc_attr__( 'Request Sent', 'buddypress' ) . '"><span></span></a>';
					}
				}
			}

		} else {
			check_ajax_referer( 'groups_leave_group' );

			if ( ! groups_leave_group( $group->id ) ) {
				esc_html_e( 'Error leaving group', 'buddypress' );
			} elseif ( bp_current_user_can( 'groups_join_group', array( 'group_id' => $group->id ) ) ) {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="group-button join-group bp-btn btn-square btn btn-sm" rel="join" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'join', 'groups_join_group' ) . '" data-svq-title="' . esc_attr__( 'Join Group', 'buddypress' ) . '"><span></span></a>';
			} elseif ( bp_current_user_can( 'groups_request_membership', array( 'group_id' => $group->id ) ) ) {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="group-button request-membership bp-btn btn-square btn btn-sm" rel="join" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'request-membership', 'groups_request_membership' ) . '" data-svq-title="' . esc_attr__( 'Request Membership', 'buddypress' ) . '"><span></span></a>';
			}
		}

		exit;
	}

	/**
	 * @param array $button
	 *
	 * @return array
	 */
	public function bp_get_add_friend_button( $button = [] ) {

		if ( isset( $button['link_text'] ) ) {
			$button['svq-extra'] = 'data-svq-title="' . esc_attr( $button['link_text'] ) . '"';
			if ( isset( $button['id'] ) && 'pending' == $button['id'] ) {
				$button['svq-extra'] .= ' data-pending="' .  esc_attr__( 'Pending friend request', 'seeko' ) . '"';
			}
		}

		$button['link_text']  = '<span></span>';
		$button['link_class'] = ( isset( $button['link_class'] ) ? $button['link_class']: '' ) . ' bp-btn btn-square btn';

		$button['link_class'] .= ' btn-sm';

		return $button;
	}

}

new SVQ_Buddypress_Legacy();