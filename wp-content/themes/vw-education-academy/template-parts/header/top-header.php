<?php
/**
 * The template part for top header
 *
 * @package VW Education Academy 
 * @subpackage vw_education_academy
 * @since VW Education Academy 1.0
 */
?>
<div id="topbar">
  <div class="container">
    <div class="row">
      <div class="col-lg-2 offset-lg-3 col-md-4">
        <?php if( get_theme_mod( 'vw_education_academy_header_call') != '') { ?>
          <span><i class="<?php echo esc_html(get_theme_mod('vw_education_academy_call_icon','fas fa-phone')); ?>"></i><?php echo esc_html(get_theme_mod('vw_education_academy_header_call',''));?></span>
        <?php }?>
      </div>
      <div class="col-lg-3 col-md-4">
        <?php if( get_theme_mod( 'vw_education_academy_header_email') != '') { ?>
          <span><i class="<?php echo esc_html(get_theme_mod('vw_education_academy_email_icon','fas fa-envelope-open')); ?>"></i><?php echo esc_html(get_theme_mod('vw_education_academy_header_email',''));?></span>
        <?php }?>
      </div>
      <div class="col-lg-4 col-md-4">
        <?php dynamic_sidebar('social-links'); ?>
      </div>
    </div>
  </div>
</div>