<?php

/**
 * Elementor & Seeko integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// This file is pretty much a boilerplate WordPress plugin.
// It does very little except including wp-widget.php

class SQElementorWidgets {

	private static $instance = null;

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function init() {
		add_theme_support( 'svq_elementor_footer' );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );

		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'admin_styles' ] );

		add_action( 'elementor/documents/register_controls', [ $this, 'register_template_control' ] );

		add_action( 'customize_save_after', [ $this, 'update_color_scheme' ] );

		add_filter( 'svq_main_section_class', [ $this, 'change_main_class' ] );

		add_filter( 'svq_page_title_wrap_class', [ $this, 'change_page_title_wrap' ], 10, 2 );

		if ( isset( $_GET['elementor_library'] ) ) {

			add_filter( 'template_include', function ( $template ) {
				$new_tpl = locate_template( 'page-templates/blank.php' );
				if ( $new_tpl ) {
					return $new_tpl;
				}

				return $template;
			} );
		}

		if ( isset( $_GET['elementor-preview'] ) ) {

			add_filter( 'option_bp-pages', function ( $value ) {
				$value['register'] = - 1;

				return $value;
			} );
		}

		add_action( 'svq_footer', [ $this, 'replace_footer' ], 9 );

		add_action( 'elementor/element/column/section_style/before_section_end', [
			$this,
			'custom_column_field'
		], 10, 2 );
		add_action( 'elementor/element/heading/section_title/before_section_end', [
			$this,
			'custom_heading_field'
		], 10, 2 );

		add_action( 'elementor/controls/controls_registered', [ $this, 'modify_icons' ] );
		add_filter( 'elementor/widget/render_content', [ $this, 'content_change' ], 10, 2 );

		add_action( 'elementor/frontend/before_render', [ $this, 'front_custom_changes' ] );
		add_filter( 'elementor/widget/before_render_content', [ $this, 'widgets_custom_changes' ] );

		add_filter( 'elementor/widget/print_template', [ $this, 'heading_js_content' ], 10, 2 );

		//only on edit screen
		if ( defined( 'ELEMENTOR_VERSION' ) && isset( $_GET['elementor-preview'] ) ) {
			add_action( 'wp_footer', [ $this, 'wp_footer' ] );
		}

		//in editor wrapper
		if ( is_admin() && isset( $_GET['action'] ) && $_GET['action'] == 'elementor' ) {
			add_action( 'admin_print_footer_scripts', [ $this, 'admin_editor_footer' ] );
		}

		/* Elementor Pro */
		add_action( 'elementor/theme/register_locations', [ $this, 'register_elementor_locations' ] );

		add_filter( 'wp_parse_str', function ( $array ) {
			if ( isset( $array['utm_campaign'] ) && 'gopro' == $array['utm_campaign'] ) {
				$array['ref'] = '1518';
			}

			return $array;
		}, 99999 );
	}

	public function replace_footer() {
		$footer_id      = null;
		$footer_display = svq_option( 'footer_display', 'theme' );

		if ( $footer_display === 'elementor' ) {
			$footer_id = svq_option( 'footer_el_tpl', '' );
		}

		if ( $footer_id !== null ) {
			remove_action( 'svq_footer', 'svq_show_footer' );

			add_action( 'svq_footer', function () use ( $footer_id ) {
				$valid_footer = false;
				if ( $footer_id > 0 && get_post( $footer_id ) ) {
					$valid_footer = true;
				}

				if ( ! $valid_footer ) {
					if ( current_user_can( 'manage_options' ) ) {
						echo '<div class="container"><div class="alert alert-info" role="alert">';
						echo wp_kses_post( sprintf( __( 'Please <a href="%s">select a footer template!</a>', 'seeko' ), admin_url( urlencode( 'customize.php?autofocus[section]=svq_section_footer' ) ) ) );
						echo '</div></div>';
					}
				} else {
					echo '<div class="footer-elementor-tpl">';
					if ( isset( $_GET['elementor-preview'] ) ) {
						if ( $footer_id !== $_GET['elementor-preview'] ) {
							$link = admin_url( 'post.php?post=' . $footer_id . '&action=elementor' );
							echo sprintf( '<a onclick="window.open(\'%s\', \'_blank\')" class="svq-hover-edit-notify" href="#" target="_blank">Edit Footer template</a>', esc_url( $link ) );
						}
					}
					svq_elementor_the_content( $footer_id );

					echo '</div>';
				}

			} );
		}
	}

	/**
	 * @param $elementor_theme_manager
	 */
	public function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location(
			'header',
			[
				'hook'         => 'svq_header',
				'remove_hooks' => [ 'svq_show_header' ],
			]
		);
		$elementor_theme_manager->register_location(
			'footer',
			[
				'hook'         => 'svq_footer',
				'remove_hooks' => [ 'svq_show_footer' ],
			]
		);
		$elementor_theme_manager->register_location(
			'single',
			[
				'hook'         => 'svq_single',
				'remove_hooks' => [ 'svq_show_single' ],
			]
		);
		$elementor_theme_manager->register_location(
			'archive',
			[
				'hook'         => 'svq_archive',
				'remove_hooks' => [ 'svq_show_archive' ],
			]
		);
	}


	/**
	 * Front-end changes based on custom settings
	 *
	 * @param \Elementor\Widget_Base $element
	 */
	function front_custom_changes( $element ) {

		// Get the settings
		$settings = $element->get_settings();

		//Check if we are on a column
		if ( 'column' === $element->get_name() ) {

			// Adding our type as a class to the button
			if ( $settings['image_outside'] == 'left' ) {
				$element->add_render_attribute( '_wrapper', 'class', 'sko-bg-outside-left' );
			} elseif ( $settings['image_outside'] == 'right' ) {
				$element->add_render_attribute( '_wrapper', 'class', 'sko-bg-outside-right' );
			}
		}
	}

	/**
	 * Front-end changes based on custom settings for widgets
	 *
	 * @param \Elementor\Widget_Base $element
	 */
	public function widgets_custom_changes( $element ) {
		// Get the settings
		$settings = $element->get_settings();

		//Check if we are on a column
		if ( 'heading' === $element->get_name() ) {
			if ( $settings['size'] == 'display' ) {
				$element->add_render_attribute( 'title', 'class', 'display' );
			}
		}

	}

	/**
	 * Adding column field
	 *
	 * @param \Elementor\Widget_Base $column
	 * @param array $args
	 */
	function custom_column_field( $column, $args ) {
		$column->add_control( 'image_outside',
			[
				'label'     => esc_html__( 'Image outside container', 'elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					'no'    => 'No',
					'left'  => 'Left side',
					'right' => 'Right side',
				),
				'condition' => [
					'background_background' => [ 'classic' ],
				],
			]
		);
	}

	/**
	 * Adding heading field
	 *
	 * @param \Elementor\Widget_Base $heading
	 * @param array $args
	 */
	function custom_heading_field( $heading, $args ) {
		//$heading->remove_control( 'size' );
		$heading->update_control(
			'size',
			[
				'label'   => esc_html__( 'Size', 'elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'elementor' ),
					'small'   => esc_html__( 'Small', 'elementor' ),
					'medium'  => esc_html__( 'Medium', 'elementor' ),
					'large'   => esc_html__( 'Large', 'elementor' ),
					'xl'      => esc_html__( 'XL', 'elementor' ),
					'xxl'     => esc_html__( 'XXL', 'elementor' ),
					'display' => esc_html__( 'Display', 'seeko' ),
				],
			]
		);
	}

	/**
	 * Change icon tag with dual icon if it is the case
	 *
	 * @param string $content
	 * @param $widget \Elementor\Widget_Base
	 *
	 * @return string
	 */
	public function content_change( $content, $widget ) {

		$settings = $widget->get_settings();
		if ( isset( $settings['icon'] ) ) {
			$icon_dual = $settings['icon'];
			if ( strpos( $icon_dual, 'icon-dual-' ) !== false ) {
				$content = preg_replace( '/<i .*? icon-dual-.*?\/i>/', '<span class="sko-icon">' . sks_generate_dual_icon( $icon_dual ) . '</span>', $content );
			}
		}

		return $content;

	}

	/**
	 * Add our icon sets
	 *
	 * @param $controls_registry
	 */
	function modify_icons( $controls_registry ) {
		// Get existing icons
		$icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
		// Append new icons
		$new_icons = array_merge(
			sks_get_dual_icons_array( '', 'icon ' ),
			$icons
		);
		$new_icons = array_merge(
			svq_icons_array( '', 'icon ' ),
			$new_icons
		);
		// Then we set a new list of icons as the options of the icon control
		$controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
	}


	/**
	 * Adding heading field
	 *
	 * @param $content
	 * @param \Elementor\Widget_Base $widget
	 *
	 * @return string
	 */
	public function heading_js_content( $content, $widget ) {
		if ( $widget->get_name() == 'heading' ) {
			$before  = "if (settings.size == 'display') {view.addRenderAttribute( 'title', 'class', [ 'display' ] );}";
			$content = str_replace( "view.addInlineEditingAttributes( 'title' );", $before . "\nview.addInlineEditingAttributes( 'title' );", $content );
		}

		return $content;
	}


	public function register_template_control( $document, $control_id = 'template' ) {

		if ( ! $document instanceof \Elementor\Core\DocumentTypes\Post && ! $document instanceof \Elementor\Modules\Library\Documents\Page ) {
			return;
		}

		if ( ! \Elementor\Utils::is_cpt_custom_templates_supported() ) {
			return;
		}

		$document->start_injection( [
			'of'       => 'post_status',
			'fallback' => [
				'of' => 'post_title',
			],
		] );

		$document->add_control(
			'seeko_page_settings_sep',
			[
				'type'  => \Elementor\Controls_Manager::DIVIDER,
				'style' => 'thick',
				'label' => 'Test'
			]
		);

		$document->add_control(
			'seeko_page_settings_title',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<strong>' . esc_html__( 'SEEKO Settings', 'seeko' ) . '</strong>',
			]
		);

		$document->add_control(
			'svq_header',
			[
				'label'        => esc_html__( 'Hide Header', 'seeko' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'seeko' ),
				'label_on'     => esc_html__( 'On', 'seeko' ),
				'default'      => '',
				'return_value' => '1',
				'selectors'    => [
					'#header' => 'display: none',
				]
			]
		);

		$document->add_control(
			'svq_title_breadcrumb',
			[
				'label'        => esc_html__( 'Hide Title + Breadcrumb', 'seeko' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'seeko' ),
				'label_on'     => esc_html__( 'On', 'seeko' ),
				'default'      => '',
				'return_value' => '1',
				'selectors'    => [
					'.svq-page-header' => 'display: none',
				]
			]
		);

		$document->add_control(
			'svq_breadcrumb',
			[
				'label'        => esc_html__( 'Hide Breadcrumb', 'seeko' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'seeko' ),
				'label_on'     => esc_html__( 'On', 'seeko' ),
				'default'      => '',
				'return_value' => '1',
				'selectors'    => [
					'.svq-breadcrumb-wrap, .svq-breadcrumb' => 'display: none',
				]
			]
		);

		$document->end_injection();
	}

	public function update_color_scheme() {
		$default_colors       = SVQ_FW::get_config( 'colors' );
		$default_site_options = SVQ_FW::get_config( 'styling_variables' );

		update_option( 'elementor_scheme_color', [
				1 => svq_option( 'body-color', $default_site_options['body-color']['default'] ), //texts
				2 => svq_option( 'secondary', $default_colors['secondary']['default'] ), //secondary
				3 => svq_option( 'body-color', $default_site_options['body-color']['default'] ), //texts
				4 => svq_option( 'primary', $default_colors['primary']['default'] ) //primary
			]
		);
	}

	public function get_elements() {
		$elements                   = [];
		$elements['button']         = [
			'name'  => 'seeko-button',
			'cat'   => 'seeko-elements',
			'class' => 'SeekoButton',
		];
		$elements['posts-carousel'] = [
			'name'  => 'seeko-posts-carousel',
			'cat'   => 'seeko-elements',
			'class' => 'SeekoPostsCarousel',
		];
		$elements['posts-grid']     = [
			'name'  => 'seeko-posts-grid',
			'cat'   => 'seeko-elements',
			'class' => 'SeekoPostsGrid',
		];
		$elements['gallery']        = [
			'name'  => 'seeko-gallery',
			'cat'   => 'seeko-elements',
			'class' => 'SeekoGallery',
		];
		$elements['tabs']           = [
			'name'  => 'seeko-tabs',
			'cat'   => 'seeko-elements',
			'class' => 'SeekoTabs',
		];
		if ( function_exists( 'bp_is_active' ) ) {
			$elements += [
				'bp-counter'           => [
					'name'  => 'counterbp',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoBpCounter',
				],
				'members-carousel'     => [
					'name'  => 'members-carousel',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoMembersCarousel',
				],
				'members-grid'         => [
					'name'  => 'members-grid',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoMembersGrid',
				],
				'groups-carousel'      => [
					'name'  => 'groups-carousel',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoGroupsCarousel',
				],
				'groups-grid'          => [
					'name'  => 'groups-grid',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoGroupsGrid',
				],
				'bp-register-account'  => [
					'name'  => 'bp-register-account',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoBpRegisterAccount',
				],
				'bp-register-xprofile' => [
					'name'  => 'bp-register-xprofile',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoBpRegisterXProfile',
				],
				'bp-register-blog'     => [
					'name'  => 'bp-register-blog',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoBpRegisterBlog',
				],
				'bp-register-buttons'  => [
					'name'  => 'bp-register-buttons',
					'cat'   => 'seeko-elements',
					'class' => 'SeekoBpRegisterButtons',
				],
			];
		}


		return $elements;
	}

	public function get_tpl_path( $name ) {
		$widget_file   = 'overrides/elementor/' . $name . '.php';
		$template_file = locate_template( $widget_file );
		if ( ! $template_file || ! is_readable( $template_file ) ) {
			$template_file = dirname( __FILE__ ) . '/templates/' . $name . '.php';
		}
		if ( $template_file && is_readable( $template_file ) ) {
			return $template_file;
		}

		return false;
	}

	public function widgets_registered() {

		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			// get our own widgets up and running:
			if ( class_exists( 'Elementor\Plugin' ) ) {
				if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
					$elementor = Elementor\Plugin::instance();

					$elementor->elements_manager->add_category(
						'seeko-elements',
						[
							'title' => 'SEEKO',
							'icon'  => 'fa fa-plug'
						]
					);

					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

							$elements = $this->get_elements();
							foreach ( $elements as $k => $element ) {
								if ( $template_file = $this->get_tpl_path( $k ) ) {

									require_once $template_file;
									$class_name = 'Elementor\\' . $element['class'];
									Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class_name );
								}
							}
						}
					}
				}
			}
		}
	}

	public function admin_styles() {
		wp_register_style( 'svq-admin-icons', svq_get_font_icons_path(), false, '1.0', 'all' );
		wp_enqueue_style( 'svq-admin-icons' );
	}

	public function change_page_title_wrap( $class = '', $page_id = 0 ) {

		if ( $this->is_full_and_built_with( $page_id ) ) {
			$class .= ' ' . SVQ_FW::get_config( 'container_class' );
		}

		return $class;
	}

	private function is_full_and_built_with( $page_id ) {
		$response = false;

		$layout = SVQ_FW::get_config( 'current_layout' );
		if ( $layout == 'full' ) {
			if ( $page_id && is_page() ) {
				$response = svq_elementor_built_with_it( $page_id );
			}
			$response = apply_filters( 'svq_elementor_is_full_page', $response );
		}

		return $response;
	}


	public function change_main_class( $classes ) {

		$post_id = get_the_ID();
		if ( $this->is_full_and_built_with( $post_id ) ) {
			$classes = [ 'content-wrapper' ];
		}

		return $classes;
	}

	public function admin_editor_footer() {
		?>
		<style>
			.elementor-device-desktop #elementor-preview-responsive-wrapper {
				min-width: 1150px !important;
			}
		</style>
		<?php
	}

	/**
	 * Loads only in edit mode
	 */
	public function wp_footer() {
		?>
		<script>
			jQuery(function ($) {
				// Add space for Elementor Menu Anchor link
				if (window.elementorFrontend) {
					elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
						if (jQuery($scope).find('.svq-sh-carousel')) {
							setTimeout(function () {
								SQ.main.slickCarousel();
								SQ.main.registerLazy();
							}, 200);
						}
					});
				}
			});
		</script>
		<?php
	}
}

SQElementorWidgets::get_instance()->init();

function svq_elementor_the_content( $page_id ) {
	if ( defined( 'ELEMENTOR_PATH' ) ) {
		echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_id );
	} else {
		$page = get_post( $page_id );
		echo apply_filters( 'the_content', $page->post_content );
	}
}

function svq_elementor_built_with_it( $page_id = null ) {
	if ( ! $page_id ) {
		return false;
	}

	if ( get_post_meta( $page_id, '_elementor_data', true ) && get_post_meta( $page_id, '_elementor_edit_mode', true ) ) {
		return true;
	}

	return false;
}
