<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class VW_Education_Academy_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'vw-education-academy' ),
				'family'      => esc_html__( 'Font Family', 'vw-education-academy' ),
				'size'        => esc_html__( 'Font Size',   'vw-education-academy' ),
				'weight'      => esc_html__( 'Font Weight', 'vw-education-academy' ),
				'style'       => esc_html__( 'Font Style',  'vw-education-academy' ),
				'line_height' => esc_html__( 'Line Height', 'vw-education-academy' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'vw-education-academy' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'vw-education-academy-ctypo-customize-controls' );
		wp_enqueue_style(  'vw-education-academy-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'vw-education-academy' ),
        'Abril Fatface' => __( 'Abril Fatface', 'vw-education-academy' ),
        'Acme' => __( 'Acme', 'vw-education-academy' ),
        'Anton' => __( 'Anton', 'vw-education-academy' ),
        'Architects Daughter' => __( 'Architects Daughter', 'vw-education-academy' ),
        'Arimo' => __( 'Arimo', 'vw-education-academy' ),
        'Arsenal' => __( 'Arsenal', 'vw-education-academy' ),
        'Arvo' => __( 'Arvo', 'vw-education-academy' ),
        'Alegreya' => __( 'Alegreya', 'vw-education-academy' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'vw-education-academy' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'vw-education-academy' ),
        'Bangers' => __( 'Bangers', 'vw-education-academy' ),
        'Boogaloo' => __( 'Boogaloo', 'vw-education-academy' ),
        'Bad Script' => __( 'Bad Script', 'vw-education-academy' ),
        'Bitter' => __( 'Bitter', 'vw-education-academy' ),
        'Bree Serif' => __( 'Bree Serif', 'vw-education-academy' ),
        'BenchNine' => __( 'BenchNine', 'vw-education-academy' ),
        'Cabin' => __( 'Cabin', 'vw-education-academy' ),
        'Cardo' => __( 'Cardo', 'vw-education-academy' ),
        'Courgette' => __( 'Courgette', 'vw-education-academy' ),
        'Cherry Swash' => __( 'Cherry Swash', 'vw-education-academy' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'vw-education-academy' ),
        'Crimson Text' => __( 'Crimson Text', 'vw-education-academy' ),
        'Cuprum' => __( 'Cuprum', 'vw-education-academy' ),
        'Cookie' => __( 'Cookie', 'vw-education-academy' ),
        'Chewy' => __( 'Chewy', 'vw-education-academy' ),
        'Days One' => __( 'Days One', 'vw-education-academy' ),
        'Dosis' => __( 'Dosis', 'vw-education-academy' ),
        'Droid Sans' => __( 'Droid Sans', 'vw-education-academy' ),
        'Economica' => __( 'Economica', 'vw-education-academy' ),
        'Fredoka One' => __( 'Fredoka One', 'vw-education-academy' ),
        'Fjalla One' => __( 'Fjalla One', 'vw-education-academy' ),
        'Francois One' => __( 'Francois One', 'vw-education-academy' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'vw-education-academy' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'vw-education-academy' ),
        'Great Vibes' => __( 'Great Vibes', 'vw-education-academy' ),
        'Handlee' => __( 'Handlee', 'vw-education-academy' ),
        'Hammersmith One' => __( 'Hammersmith One', 'vw-education-academy' ),
        'Inconsolata' => __( 'Inconsolata', 'vw-education-academy' ),
        'Indie Flower' => __( 'Indie Flower', 'vw-education-academy' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'vw-education-academy' ),
        'Julius Sans One' => __( 'Julius Sans One', 'vw-education-academy' ),
        'Josefin Slab' => __( 'Josefin Slab', 'vw-education-academy' ),
        'Josefin Sans' => __( 'Josefin Sans', 'vw-education-academy' ),
        'Kanit' => __( 'Kanit', 'vw-education-academy' ),
        'Lobster' => __( 'Lobster', 'vw-education-academy' ),
        'Lato' => __( 'Lato', 'vw-education-academy' ),
        'Lora' => __( 'Lora', 'vw-education-academy' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'vw-education-academy' ),
        'Lobster Two' => __( 'Lobster Two', 'vw-education-academy' ),
        'Merriweather' => __( 'Merriweather', 'vw-education-academy' ),
        'Monda' => __( 'Monda', 'vw-education-academy' ),
        'Montserrat' => __( 'Montserrat', 'vw-education-academy' ),
        'Muli' => __( 'Muli', 'vw-education-academy' ),
        'Marck Script' => __( 'Marck Script', 'vw-education-academy' ),
        'Noto Serif' => __( 'Noto Serif', 'vw-education-academy' ),
        'Open Sans' => __( 'Open Sans', 'vw-education-academy' ),
        'Overpass' => __( 'Overpass', 'vw-education-academy' ),
        'Overpass Mono' => __( 'Overpass Mono', 'vw-education-academy' ),
        'Oxygen' => __( 'Oxygen', 'vw-education-academy' ),
        'Orbitron' => __( 'Orbitron', 'vw-education-academy' ),
        'Patua One' => __( 'Patua One', 'vw-education-academy' ),
        'Pacifico' => __( 'Pacifico', 'vw-education-academy' ),
        'Padauk' => __( 'Padauk', 'vw-education-academy' ),
        'Playball' => __( 'Playball', 'vw-education-academy' ),
        'Playfair Display' => __( 'Playfair Display', 'vw-education-academy' ),
        'PT Sans' => __( 'PT Sans', 'vw-education-academy' ),
        'Philosopher' => __( 'Philosopher', 'vw-education-academy' ),
        'Permanent Marker' => __( 'Permanent Marker', 'vw-education-academy' ),
        'Poiret One' => __( 'Poiret One', 'vw-education-academy' ),
        'Quicksand' => __( 'Quicksand', 'vw-education-academy' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'vw-education-academy' ),
        'Raleway' => __( 'Raleway', 'vw-education-academy' ),
        'Rubik' => __( 'Rubik', 'vw-education-academy' ),
        'Rokkitt' => __( 'Rokkitt', 'vw-education-academy' ),
        'Russo One' => __( 'Russo One', 'vw-education-academy' ),
        'Righteous' => __( 'Righteous', 'vw-education-academy' ),
        'Slabo' => __( 'Slabo', 'vw-education-academy' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'vw-education-academy' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'vw-education-academy'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'vw-education-academy' ),
        'Sacramento' => __( 'Sacramento', 'vw-education-academy' ),
        'Shrikhand' => __( 'Shrikhand', 'vw-education-academy' ),
        'Tangerine' => __( 'Tangerine', 'vw-education-academy' ),
        'Ubuntu' => __( 'Ubuntu', 'vw-education-academy' ),
        'VT323' => __( 'VT323', 'vw-education-academy' ),
        'Varela Round' => __( 'Varela Round', 'vw-education-academy' ),
        'Vampiro One' => __( 'Vampiro One', 'vw-education-academy' ),
        'Vollkorn' => __( 'Vollkorn', 'vw-education-academy' ),
        'Volkhov' => __( 'Volkhov', 'vw-education-academy' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'vw-education-academy' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'vw-education-academy' ),
			'100' => esc_html__( 'Thin',       'vw-education-academy' ),
			'300' => esc_html__( 'Light',      'vw-education-academy' ),
			'400' => esc_html__( 'Normal',     'vw-education-academy' ),
			'500' => esc_html__( 'Medium',     'vw-education-academy' ),
			'700' => esc_html__( 'Bold',       'vw-education-academy' ),
			'900' => esc_html__( 'Ultra Bold', 'vw-education-academy' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'normal'  => esc_html__( 'Normal', 'vw-education-academy' ),
			'italic'  => esc_html__( 'Italic', 'vw-education-academy' ),
			'oblique' => esc_html__( 'Oblique', 'vw-education-academy' )
		);
	}
}
