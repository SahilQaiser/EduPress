<?php
class SVQ_Buddypress_Nouveau {

	public function __construct() {

		add_action( 'bp_nouveau_get_loop_classes', [ $this, 'bp_nouveau_get_loop_classes' ], 10, 2 );
		add_action( 'bp_nouveau_get_directory_type_navs_class', [ $this, 'bp_nouveau_get_directory_type_navs_class' ], 10 );
		//add_action( '', [ $this, '' ] );
	}

	public function bp_nouveau_get_loop_classes( $classes, $component ) {


		$classes[] = 'list-unstyled';
		$classes[] = 'row';

		return $classes;
	}
	public function bp_nouveau_get_directory_type_navs_class( $classes ) {


		$classes[] = 'item-list-tabs';

		return $classes;
	}

}

new SVQ_Buddypress_Nouveau();