<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeekoBpRegisterButtons extends Widget_Base {
	
	public function get_name() {
		return 'bp-register-buttons';
	}
	public function get_title() {
		return __( 'BP Register - Submit button', 'seeko' );
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
			bp_get_template_part( 'members/register/submit-buttons' );
		} else {
			esc_html_e('You need BuddyPress plugin to be active!', 'seeko');
		}
	}
	
}
