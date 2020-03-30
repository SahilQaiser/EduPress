<?php
/**
 * Class BP_Verified_Member
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-verified-member/inc
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Verified_Member
 */
class BP_Verified_Member {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'bp-verified-member', false, 'bp-verified-member/languages' );

		add_filter( 'plugin_action_links_' . BP_VERIFIED_MEMBER_PLUGIN_BASENAME, array( $this, 'add_plugin_page_settings_link' ), 10, 1 );

		add_filter( 'wp_enqueue_scripts',                     array( $this, 'enqueue_scripts'                                 ), 10, 0 );
		add_filter( 'bp_get_displayed_user_mentionname',      array( $this, 'profile_display_username_verified_badge'         ), 10, 1 );
		add_filter( 'bp_get_displayed_user_username',         array( $this, 'profile_display_username_verified_badge'         ), 10, 1 );
		add_filter( 'bp_displayed_user_fullname',             array( $this, 'profile_display_fullname_verified_badge'         ), 10, 1 );
		add_filter( 'bp_get_send_public_message_link',        array( $this, 'remove_verified_badge_from_link'                 ), 10, 1 );
		add_filter( 'widget_title',                           array( $this, 'remove_verified_badge_from_widget_title'         ), 10, 3 );
		add_filter( 'bp_get_activity_action',                 array( $this, 'activity_display_verified_badge'                 ), 10, 2 );
		add_filter( 'bp_activity_comment_name',               array( $this, 'activity_comment_display_verified_badge'         ), 10, 1 );
		add_filter( 'bp_nouveau_get_activity_comment_action', array( $this, 'nouveau_activity_comment_display_verified_badge' ), 10, 1 );
		add_filter( 'bp_get_group_member_link',               array( $this, 'members_lists_display_verified_badge'            ), 10, 1 );
		add_filter( 'bp_get_group_invite_user_link',          array( $this, 'members_lists_display_verified_badge'            ), 10, 1 );
		add_filter( 'bp_get_member_class',                    array( $this, 'member_directory_add_verified_class'             ), 10, 1 );
		add_filter( 'aa_user_name_template',                  array( $this, 'author_avatars_display_verified_badge'           ), 10, 3 );
		add_filter( 'get_comment_author',                     array( $this, 'comment_display_verified_badge'                  ), 10, 2 );
		add_filter( 'bbp_get_topic_author_links',             array( $this, 'bbp_topic_display_verified_badge'                ), 20, 3 );
		add_filter( 'bbp_get_reply_author_links',             array( $this, 'bbp_reply_display_verified_badge'                ), 20, 3 );
		add_filter( 'bbp_get_author_links',                   array( $this, 'bbp_reply_display_verified_badge'                ), 20, 3 );
	}

	/**
	 * Add plugin settings link in plugin page
	 *
	 * @param array $links The plugin page links for this plugin
	 *
	 * @return array The modified list of links for this plugin
	 */
	public function add_plugin_page_settings_link( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=bp-verified-member' ) ) . '">' . esc_html__( 'Settings', 'bp-verified-member' ) . '</a>';
		return $links;
	}

	/**
	 * Enqueue plugin scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'bp-verified-member-style', BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/css/style.css', array(), BP_VERIFIED_MEMBER_VERSION );

		global $bp_verified_member_admin;
		$badge_color = $bp_verified_member_admin->settings->get_option( 'badge_color' );

		$badge_color_css = "
			.bp-verified-badge,
			.bp-verified-member .member-name-item > a:after,
			.bp-verified-member .item-title > a:after,
			.bp-verified-member > .author > a:after,
			.bp-verified-member .member-name > a:after {
				background-color: $badge_color;
			}
		";

		wp_add_inline_style( 'bp-verified-member-style', $badge_color_css );
	}

	/**
	 * Display the verified badge on user profile.
	 *
	 * @param string $username Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function profile_display_username_verified_badge( $username ) {
		$user_id = bp_displayed_user_id();

		global $bp_verified_member_admin;

		if ( ! bp_is_user() || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_profile_username' ) ) ) {
			return $username;
		}

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $username;
		}

		$badge = $this->get_verified_badge();

		return $username . $badge;
	}

	/**
	 * Display the verified badge on user profile.
	 *
	 * @param string $username Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function profile_display_fullname_verified_badge( $username ) {
		// Prevent badge from breaking <link> and <title> tags in page header
		if ( doing_action( 'bp_head' ) || doing_action( 'document_title_parts' ) ) {
			return $username;
		}

		$user_id = bp_displayed_user_id();

		global $bp_verified_member_admin;

		if ( ! bp_is_user() || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_profile_fullname' ) ) ) {
			return $username;
		}

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $username;
		}

		$badge = $this->get_verified_badge();

		return $username . $badge;
	}

	/**
	 * Remove the verified badge HTML from link
	 *
	 * @param string $link The link that needs badge removal
	 *
	 * @return string The modified link
	 */
	public function remove_verified_badge_from_link( $link ) {
		$badge = $this->get_verified_badge();

		return str_replace( urlencode( $badge ), '', $link );
	}

	/**
	 * Remove the verified badge HTML from the widget title
	 *
	 * @param string $title The widget title that needs badge removal
	 * @param string $instance The widget instance
	 * @param string $id_base The widget base id
	 *
	 * @return string The modified widget title
	 */
	public function remove_verified_badge_from_widget_title( $title, $instance = false, $id_base = false ) {

		if ( $id_base !== 'bp_core_friends_widget' || empty( $instance ) ) {
			return $title;
		}

		$badge = $this->get_verified_badge();

		return str_replace( esc_html( $badge ), '', $title );
	}

	/**
	 * Display the verified badge in activities.
	 *
	 * @param string   $activity_action Activity action text.
	 * @param stdClass $activity Activity object.
	 *
	 * @return string Modified activity action.
	 */
	public function activity_display_verified_badge( $activity_action, $activity = false ) {
		global $bp_verified_member_admin;

		if ( ! bp_is_active( 'activity' ) || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_activity_stream' ) ) || empty( $activity ) ) {
			return $activity_action;
		}

		$user_id = $activity->user_id;

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $activity_action;
		}

		$userlink = bp_core_get_userlink( $user_id );

		$profile_link      = trailingslashit( bp_core_get_user_domain( $activity->user_id ) . bp_get_profile_slug() );
		$user_profile_link = '<a href="' . $profile_link . '">' . bp_core_get_user_displayname( $activity->user_id ) . '</a>';

		$badge = $this->get_verified_badge();

		$activity_action = str_replace( $userlink, $userlink . $badge, $activity_action );
		$activity_action = str_replace( $user_profile_link, $user_profile_link . $badge, $activity_action );

		return $activity_action;
	}

	/**
	 * Display verified badge in activities comments.
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function activity_comment_display_verified_badge( $name ) {
		global $activities_template, $bp_verified_member_admin;

		if ( ! bp_is_active( 'activity' ) || empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_activity_stream' ) ) ) {
			return $name;
		}

		$user_id = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment->user_id : $activities_template->activity->user_id;

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $name;
		}

		$badge = $this->get_verified_badge();

		return $name . $badge;
	}

	/**
	 * Display verified badge in activities with BP Nouveau template pack.
	 *
	 * @param string $action Activity action text.
	 *
	 * @return string Modified activity action.
	 */
	public function nouveau_activity_comment_display_verified_badge( $action ) {
		if ( ! bp_is_active( 'activity' ) ) {
			return $action;
		}

		$badge = $this->get_verified_badge();

		// Unescape the verified badge in bp_nouveau
		$action = str_replace( esc_html( $badge ), $badge, $action );

		return $action;
	}

	/**
	 * Display verified badge in member lists.
	 *
	 * @param string $name Username of the member.
	 *
	 * @return string Modified username.
	 */
	public function members_lists_display_verified_badge( $name ) {
		global $members_template;

		if ( ! $members_template->member ) {
			return $name;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $name;
		}

		$user_id = isset( $members_template->members ) ? $members_template->member->id : false;

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $name;
		}

		$badge = $this->get_verified_badge();

		return $name . $badge;
	}

	/**
	 * Add verified class for each verified member in the directory.
	 *
	 * @param array $classes Classes that will be output in the member container.
	 *
	 * @return array Modified classes array.
	 */
	public function member_directory_add_verified_class( $classes ) {
		global $bp_verified_member_admin, $members_template;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $classes;
		}

		$user_id = $members_template->member->id;

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $classes;
		}

		$classes[] = 'bp-verified-member';

		return $classes;
	}

	/**
	 * Display verified badge in member lists.
	 *
	 * @param string $name_html Username of the member wrapped in html.
	 * @param string $name Username of the member.
	 * @param stdClass $user User object
	 *
	 * @return string Modified username.
	 */
	public function author_avatars_display_verified_badge( $name_html, $name = '', $user = false ) {
		if ( empty( $user ) ) {
			return $name_html;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_members_lists' ) ) ) {
			return $name_html;
		}

		$user_id = $user->user_id;

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $name_html;
		}

		$badge = $this->get_verified_badge();

		return sprintf( $name_html, '%s' . $badge );
	}

	/**
	 * Display the verified badge in wp comments
	 *
	 * @param string $comment_author The comment author name to display
	 * @param int $comment_id The ID of the comment
	 *
	 * @return string The comment author name
	 */
	public function comment_display_verified_badge( $comment_author, $comment_id ) {
		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_wp_comments' ) ) ) {
			return $comment_author;
		}

		$comment = get_comment( $comment_id );

		if ( empty( $comment->user_id ) ) {
			return $comment_author;
		}

		if ( empty( get_user_meta( $comment->user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $comment_author;
		}

		$badge = $this->get_verified_badge();

		return $comment_author . $badge;
	}

	/**
	 * Display the verified badge in bbpress topics
	 *
	 * @param string $display_name The author name to display
	 * @param int $post_id The id of the topic
	 *
	 * @return string The author name to display
	 */
	public function bbp_topic_display_verified_badge( $author_links, $parsed_args, $args ) {
		if ( $parsed_args['type'] === 'avatar' ) {
			return $author_links;
		}

		$topic_id = is_numeric( $args )
			? bbp_get_reply_id( $args )
			: bbp_get_reply_id( $parsed_args['post_id'] );

		if ( bbp_is_topic_anonymous( $topic_id ) ) {
			return $author_links;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bbp_topics' ) ) ) {
			return $author_links;
		}

		$user_id = bbp_get_topic_author_id( $topic_id );

		if ( empty( $user_id ) ) {
			return $author_links;
		}

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $author_links;
		}

		$badge = $this->get_verified_badge();

		$author_links[] .= $badge;

		return $author_links;
	}

	/**
	 * Display the verified badge in bbpress replies
	 *
	 * @param string $display_name The author name to display
	 * @param int $post_id The id of the reply
	 *
	 * @return string The author name to display
	 */
	public function bbp_reply_display_verified_badge( $author_links, $parsed_args, $args ) {
		if ( $parsed_args['type'] === 'avatar' ) {
			return $author_links;
		}

		$reply_id = is_numeric( $args )
			? bbp_get_reply_id( $args )
			: bbp_get_reply_id( $parsed_args['post_id'] );

		if ( bbp_is_reply_anonymous( $reply_id ) ) {
			return $author_links;
		}

		global $bp_verified_member_admin;

		if ( empty( $bp_verified_member_admin->settings->get_option( 'display_badge_in_bbp_replies' ) ) ) {
			return $author_links;
		}

		$user_id = bbp_get_reply_author_id( $reply_id );

		if ( empty( $user_id ) ) {
			return $author_links;
		}

		if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
			return $author_links;
		}

		$badge = $this->get_verified_badge();

		$author_links[] .= $badge;

		return $author_links;
	}

	/**
	 * Get the verified badge HTML.
	 *
	 * @return string The badge HTML.
	 */
	public function get_verified_badge() {
		return apply_filters( 'bp_verified_member_verified_badge', '<span class="bp-verified-badge"></span>' );
	}
}
