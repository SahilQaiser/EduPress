<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeekoBpRegisterAccount extends Widget_Base {
	
	public function get_name() {
		return 'bp-register-account';
	}
	public function get_title() {
		return esc_html__( 'BP Register - Account info', 'seeko' );
	}
	public function get_icon() {
		return 'eicon-lock-user';
	}
	
	public function get_categories() {
		return [ 'seeko-elements' ];
	}
	
	protected function _register_controls() {
	}
	protected function render() {
		if (function_exists('bp_is_active')) {
			bp_get_template_part( 'members/register/basic-account' );
		} else {
			esc_html_e('You need BuddyPress plugin to be active!', 'seeko');
		}
	}
	
}
