<div class="svq-login-wrap">

    <div class="login-form-wrapper">
       
        <div class="svq-pop-title-wrap alternate-color">
            <h3 class="svq-pop-title"><?php esc_html_e( "Forgot your details?", "seeko" ); ?></h3>
        </div>
        
        <?php do_action( 'svq_before_lostpass_form' );?>
        
        <form name="forgot_form" action="<?php echo esc_ulr( home_url( 'wp-login.php' ) );?>" method="post" class="svq-forgot-form svq-form-signin">
            <?php wp_nonce_field( 'svq-ajax-lost-pass-nonce', 'security-lost-pass' ); ?>
            
            <span class="login-input-wrapper">
                <input type="text" id="forgot-email" required name="email" class="form-control login-field">
                <label class="login-label">
                    <span class="login-label-content"><?php esc_html_e("Username or Email",'seeko');?></span>
                </label>
            </span>
            
            <div class="svq-lost-result svq-result"></div>
            
            <button class="btn btn-lg btn-default btn-block login-button" type="submit"><?php esc_html_e( "Reset Password", "seeko" ); ?></button>
            
            <a href="#svq-login-modal" class="show-login svq-other-action"><?php esc_html_e( 'I remember my details', 'seeko' );?></a>
            
        </form>
        
    </div>

</div>

