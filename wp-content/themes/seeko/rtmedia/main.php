<?php
/* * **************************************
 * Main.php
 *
 * The main template file, that loads the header, footer and sidebar
 * apart from loading the appropriate rtMedia template
 * *************************************** */
// by default it is not an ajax request
global $rt_ajax_request;
$rt_ajax_request = false;

//todo sanitize and fix $_SERVER variable usage
// check if it is an ajax request

$_rt_ajax_request = rtm_get_server_var( 'HTTP_X_REQUESTED_WITH', 'FILTER_SANITIZE_STRING' );
if ( 'xmlhttprequest' === strtolower( $_rt_ajax_request ) ) {
	$rt_ajax_request = true;
}
?>
<div id="buddypress">

<?php
//if it's not an ajax request, load headers
if (  $rt_ajax_request ) {
	rtmedia_load_template();
} else {
	// if this is a BuddyPress page, set template type to
	// buddypress to load appropriate headers
	if ( class_exists( 'BuddyPress' ) && ! bp_is_blog_page() && apply_filters( 'rtm_main_template_buddypress_enable', true ) ) {
		$template_type = 'buddypress';
	} else {
		$template_type = '';
	}

	if ( 'buddypress' === $template_type ) {
		//load buddypress markup
		if ( bp_displayed_user_id() ) {

			//if it is a buddypress member profile
			?>
			<?php
			$layout_class = svq_bp_member_has_cover() ? ' bp-has-cover' : '';
			?>
			<div class="bp-member-layout<?php echo esc_attr( $layout_class ); ?>">
				<?php do_action( 'bp_before_member_home_content' ); ?>
				<div class="container">
					<div <?php svq_main_row_class(); ?>>
						<div class="col-header col-12">
							<div id="item-header" role="complementary">

								<?php
								/**
								 * If the cover image feature is enabled, use a specific header
								 */
								if ( function_exists( 'bp_displayed_user_use_cover_image_header' ) && bp_displayed_user_use_cover_image_header() ) :
								bp_get_template_part( 'members/single/cover-image-header' );
								else :
									bp_get_template_part( 'members/single/member-header' );
									endif;
								?>

							</div><!--#item-header-->
						</div>
						<div <?php svq_main_col_class(); ?>>

							<div id="item-nav">
								<div class="container">
									<div class="row">
										<div class="col">
											<div class="col-inner">

												<?php svq_the_breadcrumb(); ?>

												<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
													<ul class="list-unstyled list-inline flex-menu flex-menu-overflow">
														<?php bp_get_displayed_user_nav(); ?>
														<?php do_action( 'bp_member_options_nav' ); ?>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div><!--#item-nav-->

							<div id="item-body" role="main">
									<div class="container">
										<div class="row">
											<div class="col-12">
												<div class="col-inner">

													<?php do_action( 'bp_before_member_body' ); ?>
													<?php do_action( 'bp_before_member_media' ); ?>
													<div class="item-list-tabs no-ajax" id="subnav">
														<ul>

														<?php rtmedia_sub_nav(); ?>

														<?php do_action( 'rtmedia_sub_nav' ); ?>

														</ul>
													</div><!-- .item-list-tabs -->

		<?php
		} else if ( bp_is_group() ) {

			//not a member profile, but a group
		$layout_class = svq_bp_group_has_cover() ? ' bp-has-cover' : '';
		?>

		<?php if ( bp_has_groups() ) : while ( bp_groups() ) :
				bp_the_group(); ?>

		<?php

		/**
		 * Fires before the display of the group home content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_before_group_home_content' ); ?>

		<div class="bp-member-layout <?php echo esc_attr( $layout_class ); ?>">
			<div class="container">

				<div <?php svq_main_row_class(); ?>>

					<div class="col-header col-12">

						<div id="item-header" role="complementary">

							<?php
							/**
							 * If the cover image feature is enabled, use a specific header
							 */
							if ( function_exists( 'bp_group_use_cover_image_header' ) && bp_group_use_cover_image_header() ) :
								bp_get_template_part( 'groups/single/cover-image-header' );
							else :
								bp_get_template_part( 'groups/single/group-header' );
							endif;
							?>

						</div><!--#item-header-->
					</div>
					<div <?php svq_main_col_class(); ?>>

						<div id="item-nav">
							<div class="container">
								<div class="row">
									<div class="col">
										<div class="col-inner">

											<?php svq_the_breadcrumb(); ?>

											<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
												<ul class="list-unstyled list-inline flex-menu flex-menu-overflow">

													<?php bp_get_options_nav(); ?>

													<?php do_action( 'bp_group_options_nav' ); ?>

												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><!-- #item-nav -->


						<div id="item-body">
								<div class="container">
									<div class="row">
										<div class="col-12">
											<div class="col-inner">

												<?php do_action( 'bp_before_group_body' ); ?>
												<?php do_action( 'bp_before_group_media' ); ?>
												<div class="item-list-tabs no-ajax" id="subnav">
													<ul>

														<?php rtmedia_sub_nav(); ?>

														<?php do_action( 'rtmedia_sub_nav' ); ?>

													</ul>
												</div><!-- .item-list-tabs -->
		<?php endwhile;
		endif; // group/profile if/else
		}
	} else { ////if BuddyPress
	?>
	<div id="item-body">
	<?php
	}

	// include the right rtMedia template
	rtmedia_load_template();

	if ( function_exists( 'bp_displayed_user_id' ) && 'buddypress' === $template_type && ( bp_displayed_user_id() || bp_is_group() ) ) {
		if ( bp_is_group() ) {
			do_action( 'bp_after_group_media' );
			do_action( 'bp_after_group_body' );
		}
		if ( bp_displayed_user_id() ) {
			do_action( 'bp_after_member_media' );
			do_action( 'bp_after_member_body' );
		}
	}
	?>

	<?php if ( 'buddypress' === $template_type ) { ?>
		</div><!--.col-inner-->
		</div><!--.col-lg-12-->
		</div><!--.row-->
		</div><!--.container-->
		</div><!--#item-body-->
		</div> <!--.col-main-->

		<?php
		/**
		 * After main content - action
		 */
		do_action('svq_after_content');
		?>

		</div> <!--.row-->
		</div> <!--.container-->
		</div> <!--.bp-member-layout-->

	<?php } else { ?>
		</div><!--#item-body-->
	<?php } ?>
	<?php
	if ( function_exists( 'bp_displayed_user_id' ) && 'buddypress' === $template_type && ( bp_displayed_user_id() || bp_is_group() ) ) {
		if ( bp_is_group() ) {
			do_action( 'bp_after_group_home_content' );
		}
		if ( bp_displayed_user_id() ) {
			do_action( 'bp_after_member_home_content' );
		}
	}
}
//close all markup
?>
	</div><!--#buddypress-->
<?php
// if ajax
