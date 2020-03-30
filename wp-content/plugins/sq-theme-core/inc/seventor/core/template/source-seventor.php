<?php
namespace Seventor\Core\Template;

use Seventor\Core\Template\Module;
use Elementor\TemplateLibrary\Source_Base;
use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

/**
 * Elementor template library remote source.
 *
 * Elementor template library remote source handler class is responsible for
 * handling remote templates from Elementor.com servers.
 *
 * @since 1.0.0
 */
class Source_Seventor extends Source_Base {

	public function get_id() {
		return 'seventor';
	}

	public function get_title() {
		return __( 'SeventhQueen', 'sq-theme-core' );
	}

	public function register_data() {}

	public function get_items( $args = [] ) {
		$templates_data = Module::get_templates();

		$templates = [];

		foreach ( $templates_data as $template_data ) {
			$templates[] = $this->get_item( $template_data );
		}

		return $templates;
	}

	public function get_item( $template_data ) {
		$favorite_templates = $this->get_user_meta( 'favorites' );

		return [
			'template_id' => 'seventor_' . $template_data['id'],
			'source' => 'remote',
			'type' => $template_data['type'],
			'subtype' => $template_data['subtype'],
			'title' => '7thQueen - ' . $template_data['title'], // Prepend name for searchable string
			'thumbnail' => $template_data['thumbnail'],
			'date' => $template_data['tmpl_created'],
			'author' => $template_data['author'],
			'tags' => $template_data['tags'],
			'isPro' => 0,
			'popularityIndex' => (int) $template_data['popularity_index'],
			'trendIndex' => (int) $template_data['trend_index'],
			'hasPageSettings' => ( '1' === $template_data['has_page_settings'] ),
			'url' => $template_data['url'],
			'favorite' => ! empty( $favorite_templates[ $template_data['id'] ] ),
		];
	}

	public function save_item( $template_data ) {
		return false;
	}

	public function update_item( $new_data ) {
		return false;
	}

	public function delete_template( $template_id ) {
		return false;
	}

	public function export_template( $template_id ) {
		return false;
	}

	public function get_data( array $args, $context = 'display' ) {
		$data = Module::get_template_content( $args['template_id'] );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id  = $_POST['editor_post_id']; // phpcs:ignore
		$document = Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		return $data;
	}
}
