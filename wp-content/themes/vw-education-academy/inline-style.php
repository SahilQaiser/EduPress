<?php
	
	/*---------------------------Highlight color-------------------*/

	$vw_education_academy_first_color = get_theme_mod('vw_education_academy_first_color');

	$custom_css = '';

	if($vw_education_academy_first_color != false){
		$custom_css .='.search-box i, #slider .carousel-control-prev-icon:hover, #slider .carousel-control-next-icon:hover, #slider .view-more:hover, .view-more:hover, .footer .tagcloud a:hover, .sidebar .custom-social-icons i, .footer .custom-social-icons i, .scrollup i, .footer-2, .home-page-header, .pagination span, .pagination a, .sidebar .tagcloud a:hover, nav.woocommerce-MyAccount-navigation ul li, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, input[type="submit"], #comments input[type="submit"].submit, .main-navigation a:hover, #comments a.comment-reply-link, .toggle-nav i, .sidebar .widget_price_filter .ui-slider .ui-slider-range, .sidebar .widget_price_filter .ui-slider .ui-slider-handle, .sidebar .woocommerce-product-search button, .footer .widget_price_filter .ui-slider .ui-slider-range, .footer .widget_price_filter .ui-slider .ui-slider-handle, .footer .woocommerce-product-search button{';
			$custom_css .='background-color: '.esc_html($vw_education_academy_first_color).';';
		$custom_css .='}';
	}
	if($vw_education_academy_first_color != false){
		$custom_css .='a, .footer .custom-social-icons i:hover, .footer li a:hover, .post-main-box:hover h2, #about-section span i, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title, .main-navigation ul.sub-menu a:hover, .entry-content a, .sidebar .textwidget p a, .textwidget p a, #comments p a, .slider .inner_carousel p a{';
			$custom_css .='color: '.esc_html($vw_education_academy_first_color).';';
		$custom_css .='}';
	}
	if($vw_education_academy_first_color != false){
		$custom_css .='#slider .view-more:hover, .view-more:hover, #comments input[type="submit"]:hover{';
			$custom_css .='border-color: '.esc_html($vw_education_academy_first_color).';';
		$custom_css .='}';
	}
	if($vw_education_academy_first_color != false){
		$custom_css .='#about-section hr, .post-info hr, .main-navigation ul ul{';
			$custom_css .='border-top-color: '.esc_html($vw_education_academy_first_color).';';
		$custom_css .='}';
	}
	if($vw_education_academy_first_color != false){
		$custom_css .='.main-navigation ul ul, .header-fixed{';
			$custom_css .='border-bottom-color: '.esc_html($vw_education_academy_first_color).';';
		$custom_css .='}';
	}
	if($vw_education_academy_first_color != false){
		$custom_css .='.post-main-box, .sidebar .widget{
		box-shadow: 0px 15px 10px -15px '.esc_html($vw_education_academy_first_color).';
		}';
	}

	$custom_css .='@media screen and (max-width:1000px) {';
		if($vw_education_academy_first_color != false){
			$custom_css .='.main-navigation ul li a:hover{
			color:'.esc_html($vw_education_academy_first_color).'!important;
			}';
		}
	$custom_css .='}';

	/*---------------------------Width Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_education_academy_width_option','Full Width');
    if($theme_lay == 'Boxed'){
		$custom_css .='body{';
			$custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$custom_css .='}';
		$custom_css .='.custom-social-icons{';
			$custom_css .='text-align: left;';
		$custom_css .='}';
		$custom_css .='#slider .carousel-caption{';
			$custom_css .='top: 55%;';
		$custom_css .='}';
	}else if($theme_lay == 'Wide Width'){
		$custom_css .='body{';
			$custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$custom_css .='}';
		$custom_css .='#slider .carousel-caption{';
			$custom_css .='top: 55%;';
		$custom_css .='}';
	}else if($theme_lay == 'Full Width'){
		$custom_css .='body{';
			$custom_css .='max-width: 100%;';
		$custom_css .='}';
	}

	/*--------------------------- Slider Opacity -------------------*/

	$theme_lay = get_theme_mod( 'vw_education_academy_slider_opacity_color','0.5');
	if($theme_lay == '0'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0';
		$custom_css .='}';
		}else if($theme_lay == '0.1'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.1';
		$custom_css .='}';
		}else if($theme_lay == '0.2'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.2';
		$custom_css .='}';
		}else if($theme_lay == '0.3'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.3';
		$custom_css .='}';
		}else if($theme_lay == '0.4'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.4';
		$custom_css .='}';
		}else if($theme_lay == '0.5'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.5';
		$custom_css .='}';
		}else if($theme_lay == '0.6'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.6';
		$custom_css .='}';
		}else if($theme_lay == '0.7'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.7';
		$custom_css .='}';
		}else if($theme_lay == '0.8'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.8';
		$custom_css .='}';
		}else if($theme_lay == '0.9'){
		$custom_css .='#slider img{';
			$custom_css .='opacity:0.9';
		$custom_css .='}';
		}

	/*---------------------------Slider Content Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_education_academy_slider_content_option','Center');
    if($theme_lay == 'Left'){
		$custom_css .='#slider .carousel-caption, #slider .inner_carousel{';
			$custom_css .='text-align:left; left:15%; right:45%;';
		$custom_css .='}';
	}else if($theme_lay == 'Center'){
		$custom_css .='#slider .carousel-caption, #slider .inner_carousel{';
			$custom_css .='text-align:center; left:25%; right:25%;';
		$custom_css .='}';
	}else if($theme_lay == 'Right'){
		$custom_css .='#slider .carousel-caption, #slider .inner_carousel{';
			$custom_css .='text-align:right; left:45%; right:15%;';
		$custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_education_academy_blog_layout_option','Default');
    if($theme_lay == 'Default'){
		$custom_css .='.post-main-box{';
			$custom_css .='';
		$custom_css .='}';
	}else if($theme_lay == 'Center'){
		$custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn{';
			$custom_css .='text-align:center;';
		$custom_css .='}';
		$custom_css .='.post-info{';
			$custom_css .='margin-top:10px;';
		$custom_css .='}';
		$custom_css .='.post-info hr{';
			$custom_css .='margin:15px auto;';
		$custom_css .='}';
	}else if($theme_lay == 'Left'){
		$custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services p{';
			$custom_css .='text-align:Left;';
		$custom_css .='}';
		$custom_css .='.post-info hr{';
			$custom_css .='margin-bottom:10px;';
		$custom_css .='}';
	}

	/*------------------------------Responsive Media -----------------------*/

	$stickyheader = get_theme_mod( 'vw_education_academy_resp_slider_hide_show',true);
    if($stickyheader == true){
    	$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='#slider{';
			$custom_css .='display:block;';
		$custom_css .='} }';
	}else if($stickyheader == false){
		$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='#slider{';
			$custom_css .='display:none;';
		$custom_css .='} }';
	}

	$metabox = get_theme_mod( 'vw_education_academy_metabox_hide_show',true);
    if($metabox == true){
    	$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='.post-info{';
			$custom_css .='display:block;';
		$custom_css .='} }';
	}else if($metabox == false){
		$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='.post-info{';
			$custom_css .='display:none;';
		$custom_css .='} }';
	}

	$sidebar = get_theme_mod( 'vw_education_academy_sidebar_hide_show',true);
    if($sidebar == true){
    	$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='.sidebar{';
			$custom_css .='display:block;';
		$custom_css .='} }';
	}else if($sidebar == false){
		$custom_css .='@media screen and (max-width:575px) {';
		$custom_css .='.sidebar{';
			$custom_css .='display:none;';
		$custom_css .='} }';
	}

	/*------------------ Search Settings -----------------*/
	
	$vw_education_academy_search_padding_top_bottom = get_theme_mod('vw_education_academy_search_padding_top_bottom');
	$vw_education_academy_search_padding_left_right = get_theme_mod('vw_education_academy_search_padding_left_right');
	$vw_education_academy_search_font_size = get_theme_mod('vw_education_academy_search_font_size');
	$vw_education_academy_search_border_radius = get_theme_mod('vw_education_academy_search_border_radius');
	if($vw_education_academy_search_padding_top_bottom != false || $vw_education_academy_search_padding_left_right != false || $vw_education_academy_search_font_size != false || $vw_education_academy_search_border_radius != false){
		$custom_css .='.search-box i{';
			$custom_css .='padding-top: '.esc_html($vw_education_academy_search_padding_top_bottom).'; padding-bottom: '.esc_html($vw_education_academy_search_padding_top_bottom).';padding-left: '.esc_html($vw_education_academy_search_padding_left_right).';padding-right: '.esc_html($vw_education_academy_search_padding_left_right).';font-size: '.esc_html($vw_education_academy_search_font_size).';border-radius: '.esc_html($vw_education_academy_search_border_radius).'px;';
		$custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$vw_education_academy_button_padding_top_bottom = get_theme_mod('vw_education_academy_button_padding_top_bottom');
	$vw_education_academy_button_padding_left_right = get_theme_mod('vw_education_academy_button_padding_left_right');
	if($vw_education_academy_button_padding_top_bottom != false || $vw_education_academy_button_padding_left_right != false){
		$custom_css .='.post-main-box .view-more{';
			$custom_css .='padding-top: '.esc_html($vw_education_academy_button_padding_top_bottom).'; padding-bottom: '.esc_html($vw_education_academy_button_padding_top_bottom).';padding-left: '.esc_html($vw_education_academy_button_padding_left_right).';padding-right: '.esc_html($vw_education_academy_button_padding_left_right).';';
		$custom_css .='}';
	}

	$vw_education_academy_button_border_radius = get_theme_mod('vw_education_academy_button_border_radius');
	if($vw_education_academy_button_border_radius != false){
		$custom_css .='.post-main-box .view-more{';
			$custom_css .='border-radius: '.esc_html($vw_education_academy_button_border_radius).'px;';
		$custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$vw_education_academy_copyright_alingment = get_theme_mod('vw_education_academy_copyright_alingment');
	if($vw_education_academy_copyright_alingment != false){
		$custom_css .='.copyright p{';
			$custom_css .='text-align: '.esc_html($vw_education_academy_copyright_alingment).';';
		$custom_css .='}';
	}

	$vw_education_academy_copyright_padding_top_bottom = get_theme_mod('vw_education_academy_copyright_padding_top_bottom');
	if($vw_education_academy_copyright_padding_top_bottom != false){
		$custom_css .='.footer-2{';
			$custom_css .='padding-top: '.esc_html($vw_education_academy_copyright_padding_top_bottom).'; padding-bottom: '.esc_html($vw_education_academy_copyright_padding_top_bottom).';';
		$custom_css .='}';
	}