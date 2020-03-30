<?php 
global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user;


$pmpro_levels = pmpro_getAllLevels(false, true);
$pmpro_level_order = pmpro_getOption('level_order');

if(!empty($pmpro_level_order))
{
	$order = explode(',',$pmpro_level_order);

	//reorder array
	$reordered_levels = array();
	foreach($order as $level_id) {
		foreach($pmpro_levels as $key=>$level) {
			if($level_id == $level->id)
				$reordered_levels[] = $pmpro_levels[$key];
		}
	}

	$pmpro_levels = $reordered_levels;
}

$pmpro_levels = apply_filters( "pmpro_levels_array", $pmpro_levels );

/* SEEKO ADDED */
$levels_no   = count( $pmpro_levels );
$levels_no   = ( $levels_no == 0 ) ? 1 : $levels_no;
$level_cols = 12 / $levels_no;
$good_cols =[ 1, 2, 3, 4, 6, 12];

if ( ! in_array( $level_cols, $good_cols ) ) {
	$level_cols = 3;
}
$level_cols = apply_filters( 'seeko_pmpro_level_columns', $level_cols );
$seeko_options = get_option('seeko_pmpro');
if ( ! $seeko_options ) {
	$seeko_options = [];
}
/* END SEEKO ADDED */

if($pmpro_msg)
{
?>
	<div class="pmpro_message <?php echo esc_attr( $pmpro_msgt ); ?>"><?php echo wp_kses_data( $pmpro_msg ); ?></div>
	<?php
}
?>
<div id="pmpro_levels_table" class="pmpro_checkout row">

	<?php	
	$count = 0;
	foreach($pmpro_levels as $level)
	{
		if(isset($current_user->membership_level->ID)) {
		  $current_level = ( $current_user->membership_level->ID == $level->id );
		} else {
		  $current_level = false;
		}

		$card_classes = 'pricing-card';

		$popular = false;
		if ( isset( $seeko_options[ $level->id ] ) && $seeko_options[ $level->id ]['popular'] == 'yes' ) {
		  $popular = true;
		  $card_classes .= ' pricing-popular';
		}
		$style = false;
		if ( isset( $seeko_options[ $level->id ] ) && $seeko_options[ $level->id ]['color'] != 'normal' ) {
			$card_classes .= ' pricing-' . $seeko_options[ $level->id ]['color'];
		}

	?>

		<div class="col-md-6 col-lg-<?php echo esc_attr( $level_cols ); ?>">
			<div class="<?php echo esc_attr( $card_classes ); ?>">
				<h3 <?php if( $popular ) { echo 'data-popular="popular plan"'; } ?> class="pricing-title">
					<?php
					$level_name = $current_level ? "<strong>{$level->name}</strong>" : $level->name;
					echo wp_kses_post( $level_name );
					?>
				</h3>
				<div class="pricing-description">

					<?php
					$description = $level->description;
					if ( $description ) {
						echo SVQ_Pmpro::instance()->list_from_description( $description );
					}

					do_action('seeko-pmpro-description');
					?>

				</div>

				<h4 class="pricing-price">
					<?php
					if(pmpro_isLevelFree($level)) {
						$cost_text = "<strong>" . esc_html__( "Free", 'paid-memberships-pro' ) . "</strong>";
					} else {
						$cost_text = pmpro_getLevelCost( $level, true, true );

						if ( pmpro_formatPrice( $level->initial_payment ) == pmpro_formatPrice( $level->billing_amount ) ) {
							$cost_text = preg_replace( '/' . pmpro_formatPrice( $level->initial_payment ) . '/', '', $cost_text, 1 );
							$cost_text = '<strong>'. pmpro_formatPrice( $level->initial_payment ) . '</strong>'. strip_tags( $cost_text );
						}

					}
					$expiration_text = pmpro_getLevelExpiration($level);

					if( ! empty( $cost_text ) ) {
						echo wp_kses_post( $cost_text );
					}
					?>
				</h4>

				<?php
				if( ! empty( $expiration_text ) )
					echo '<p class="pricing-desc">' .wp_kses_post( $expiration_text ) . '</p>';
				?>
				<div class="btn-pricing">
					<?php if(empty($current_user->membership_level->ID)) { ?>
						<a class="btn" href="<?php echo pmpro_url( "checkout", "?level=" . $level->id, "https"); ?>"><?php esc_html_e('Select', 'paid-memberships-pro' );?></a>
					<?php } elseif ( !$current_level ) { ?>
						<a class="btn" href="<?php echo pmpro_url( "checkout", "?level=" . $level->id, "https"); ?>"><?php esc_html_e('Select', 'paid-memberships-pro' );?></a>
					<?php } elseif($current_level) { ?>

						<?php
						//if it's a one-time-payment level, offer a link to renew
						if( pmpro_isLevelExpiringSoon( $current_user->membership_level) && $current_user->membership_level->allow_signups ) {
							?>
							<a class="btn" href="<?php echo pmpro_url( "checkout", "?level=" . $level->id, "https" );?>"><?php esc_html_e('Renew', 'paid-memberships-pro' );?></a>
							<?php
						} else {
							?>
							<a class="btn disabled" href="<?php echo pmpro_url( "account" ); ?>"><?php esc_html_e('Your&nbsp;Level', 'paid-memberships-pro' );?></a>
							<?php
						}
						?>

					<?php } ?>
				</div>
			</div>

		</div>

	<?php
	}
	?>

</div>
<nav id="nav-below" class="navigation" role="navigation">
	<div class="nav-previous aligncenter mt-3">
		<?php if(!empty($current_user->membership_level->ID)) { ?>
			<a href="<?php echo esc_url( pmpro_url("account") );?>" class="btn btn-sm" id="pmpro_levels-return-account"><?php _e('&larr; Return to Your Account', 'paid-memberships-pro' );?></a>
		<?php } else { ?>
			<a href="<?php echo esc_url( home_url( '/' ) )?>" class="btn btn-sm" id="pmpro_levels-return-home"><?php _e('&larr; Return to Home', 'paid-memberships-pro' );?></a>
		<?php } ?>
	</div>
</nav>
