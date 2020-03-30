<?php
/**
 * This class is a common class for all the group controls.
 *
 * @package Seeko\Customizer
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Group control base class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Base_Group_Control extends Seeko_Customizer_Base_Control {

	/**
	 * Fields for this control.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $fields = [];

	/**
	 * Exclude fields for this control.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $exclude = [];

	/**
	 * Include fields for this control.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $include = [];

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		// Set the fields for this control.
		$this->set_fields();

		// Include fields.
		$this->include_fields();

		// Exclude fields.
		$this->exclude_fields();

		// Get fields.
		$this->json['fields'] = $this->get_fields();
	}

	/**
	 * Set the fields for this control.
	 *
	 * @since 1.0.0
	 */
	protected function set_fields() {}

	/**
	 * Add field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property Property name of field.
	 * @param array  $args Field arguments.
	 */
	final protected function add_field( $property, $args ) {
		$this->fields[ $property ] = array_merge( $args, [
			'id'         => sprintf( '%1$s_%2$s', $this->id, $property ),
			'property'   => $property,
			'responsive' => $this->responsive && isset( $args['responsive'] ) ? true : false,
		] );
	}

	/**
	 * Update field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id ID of the field.
	 * @param array  $args Arguments of the field.
	 */
	final protected function update_field( $id = '', $args ) {
		if ( ! isset( $this->fields[ $id ] ) ) {
			return;
		}

		$field = array_merge( $this->fields[ $id ], $args );

		$this->fields[ $id ] = $field;
	}

	/**
	 * Get the fields for this control.
	 *
	 * @since 1.0.0
	 */
	final protected function get_fields() {
		foreach ( $this->fields as $property => $field ) {
			if ( ! $field['responsive'] ) {
				$this->fields[ $property ]['value'] = $this->get_control_value( $property, $field );
			}

			if ( $field['responsive'] ) {
				$this->fields[ $property ]['value'] = $this->get_responsive_control_value( $property, $field );
			}
		}

		return $this->fields;
	}

	/**
	 * Include fields.
	 *
	 * @since 1.0.0
	 */
	protected function include_fields() {}

	/**
	 * Exclude fields.
	 *
	 * @since 1.0.0
	 */
	protected function exclude_fields() {
		if ( empty( $this->exclude ) ) {
			return;
		}

		$exclude = $this->exclude;

		$this->fields = array_filter( $this->fields, function( $field ) use ( $exclude ) {
			return ! in_array( $field['property'], $exclude, true );
		} );
	}

	/**
	 * Get the control value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $property Field property name.
	 * @param array $field Control field.
	 *
	 * @return array Property value.
	 */
	final protected function get_control_value( $property, $field ) {
		if ( $this->responsive && isset( $this->json['value']['desktop'][ $property ] ) ) {
			return $this->json['value']['desktop'][ $property ];
		}

		if ( isset( $this->json['value'][ $property ] ) ) {
			return $this->json['value'][ $property ];
		}

		if ( isset( $field['default'] ) ) {
			return $field['default'];
		}

		return null;
	}

	/**
	 * Get the responsive control value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $property Field property name.
	 * @param array $field Control field.
	 *
	 * @return array Property value.
	 */
	final protected function get_responsive_control_value( $property, $field ) {
		$value = [];

		foreach ( Seeko_Customizer::$responsive_devices as $device => $media_query ) {
			if ( isset( $this->json['value'][ $device ][ $property ] ) ) {
				$value[ $device ] = $this->json['value'][ $device ][ $property ];
				continue;
			}

			if ( isset( $field['default'][ $device ] ) ) {
				$value[ $device ] = $field['default'][ $device ];
				continue;
			}
		}

		return $value;
	}

	/**
	 * Render the control's static content.
	 *
	 * @since 1.0.0
	 */
	final protected function render_content() {
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo $this->label; // WPCS: XSS ok. ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; // WPCS: XSS ok. ?></span>
		<?php endif; ?>
		<ul class="seeko-group-controls seeko-row"></ul>
		<?php
	}

	/**
	 * Don't render a JS template for this.
	 *
	 * @since 1.0.0
	 */
	final protected function content_template() {}
}
