<?php
namespace Stax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {

	add_filter( 'stax_compatible_themes', function( $themes = [] ) {
		$themes['seeko'] = [
			'header' => [
				'tag' => '#header',
				'front_actions' => function() {
					remove_action( 'svq_header', 'svq_show_header' );
					add_action( 'svq_header', function() {
						echo '<div id="header" class="stax-loaded">';
						Plugin::instance()->the_zone_html( 'header' );
						echo '</div>';
					});

				}
			],
		];
		return $themes;
	});
} );

if ( stax_fs()->can_use_premium_code() ) {
	return;
}

add_filter( 'stax_section_properties', function( $section ) {
	if ( in_array( $section['name'], [ 'resized-logo-section',  'transparent-logo-section' ] ) ) {
		$section['free'] = true;
	}

	return $section;
});

add_filter('stax_section_fields_before_add', function ( $fields, $section ) {
	if( $section->name == 'resized-logo-section' ) {
		$fields = [
			new EditorSectionField(
				[
					'label' => '',
					'name'  => 'img_resized_upload_field',
					'type'  => EditorSectionField::FIELD_IMAGE,
					'value' => ''
				]
			)
		];
	} elseif( $section->name == 'transparent-logo-section' ) {
		$fields = [
			new EditorSectionField(
				[
					'label' => '',
					'name'  => 'img_transparent_upload_field',
					'type'  => EditorSectionField::FIELD_IMAGE,
					'value' => ''
				]
			)
		];
	}

	return $fields;
}, 10, 2);


add_action( 'stax_section_after_add_field', function( $field, $section ) {

	if ( ! $field ) {
		return;
	}
	if ( $field->name == 'logo_normal_height_field' ) {

		$section->fields[] = new EditorSectionField(
			[
				'label'       => 'Logo on resized header',
				'name'        => 'logo_resize_separator',
				'only'        => 'header',
				'type'        => EditorSectionField::FIELD_SEPARATOR,
				'value'       => '',
				'editorClass' => [
					'padding-m',
				],
			]
		);

		$section->fields[] = new EditorSectionField(
			[
				'label'    => 'Image height',
				'name'     => 'logo_resize_height_field',
				'only'     => 'header',
				'type'     => EditorSectionField::FIELD_INPUT_NUMBER,
				'value'    => '',
				'units'    => [
					[
						'type'   => 'px',
						'active' => true,
					],
				],
				'selector' => [
					'.header-section.is-sticky.is-resized {{SELECTOR}} img' => [
						'height: {{VALUE}}{{UNIT}}',
						'max-height: initial !important',
					],
				]
			]
		);
	} elseif ( $field->name == 'resize_separator' ) {

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Enable Resize',
				'name'       => 'resize_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_SWITCH,
				'value'      => [
					[
						'label'   => '',
						'value'   => 'header-resize',
						'checked' => false,
						'trigger' => [
							'section' => [],
							'field'   => [
								'resize_height_field',
								'resize_offset_field'
							]
						]
					]
				],
				'tooltip'    => 'Resize header at scroll to maximize view page area.'
			]
		);

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Height',
				'name'       => 'resize_height_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_INPUT_NUMBER,
				'value'      => '60',
				'units'      => [
					[
						'type'   => 'px',
						'active' => true
					]
				],
				'selector'   => [
					'{{SELECTOR}}.is-sticky.is-resized'                    => 'height: {{VALUE}}{{UNIT}}',
					'.header-section{{SELECTOR}}.is-sticky.is-resized img' => 'max-height: {{VALUE}}{{UNIT}}'
				]
			]
		);

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Offset',
				'name'       => 'resize_offset_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_INPUT_NUMBER,
				'value'      => '0',
				'units'      => [
					[
						'type'   => 'px',
						'active' => true
					]
				],
				'tooltip'    => 'Number of pixels to scroll until it resizes.'
			]
		);
	} elseif ( $field->name == 'slide_separator' ) {

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Enable Hide & Reveal',
				'name'       => 'slide_up_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_SWITCH,
				'value'      => [
					[
						'label'   => '',
						'value'   => 'header-slide-up',
						'checked' => false,
						'trigger' => [
							'section' => [],
							'field'   => [
								'slide_up_offset_field'
							]
						]
					]
				],
				'tooltip'    => 'With Hide & Reveal feature you can toggle header while scrolling the page.'
			]
		);

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Offset',
				'name'       => 'slide_up_offset_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_INPUT_NUMBER,
				'value'      => '0',
				'units'      => [
					[
						'type'   => 'px',
						'active' => true
					]
				],
				'tooltip'    => 'Stay a bit until it is hides.'
			]
		);

	} elseif ( $field->name == 'transparent_separator' ) {

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Enable Transparent',
				'name'       => 'transparent_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_SWITCH,
				'value'      => [
					[
						'label'   => '',
						'value'   => 'header-transparent is-transparent is-sticky',
						'checked' => false,
						'trigger' => [
							'section' => [],
							'field'   => [
								'transparent_offset_field'
							]
						]
					]
				]
			]
		);

		$section->fields[] = new EditorSectionField(
			[
				'label'      => 'Top offset',
				'name'       => 'transparent_offset_field',
				'visibility' => false,
				'type'       => EditorSectionField::FIELD_INPUT_NUMBER,
				'value'      => '100'
			]
		);
	}

}, 10, 2 );

add_filter( 'stax_section_add_field', function( $field, $section ) {
	$fields = [];
	$fields['resize_field_go_pro'] = [];
	$fields['slide_up_field_go_pro'] = [];
	$fields['transparent_field_go_pro'] = [];

	if ( isset( $fields[ $field->name ] ) ) {
		if ($field->type == EditorSectionField::FIELD_GO_PRO ) {
			return null;
		}
		$field = $fields[ $field->name ];
	}

	return $field;

}, 10, 2 );
