<?php

$config = array(
	'theme_name' => 'Seeko',
	'theme_lower' => 'seeko',
	'slug' => 'sq-panel',
	'item_id' => SVQ_FW::get_config( 'item_id' ),
	'purchase_link' => 'http://bit.ly/seekowp',
);

$config['priority_addons'] = [
	'sq-theme-core',
	];

if ( function_exists( 'stax_fs' ) && stax_fs()->is_premium() && ! defined( 'STAX_DEV' ) ) {

	$config['priority_addons'][] = 'stax-premium';
} else {
	$config['priority_addons'][] = 'stax';
}

$config['priority_addons'] = $config['priority_addons'] + [
	'seeko-search',
	'sq-likes',
	'sq-login-booster',
	'buddypress',
	'bp-matching',
	'elementor',
];

SQ_Panel::getInstance( $config );


/* demo import */
$remote_path = 'http://updates.seventhqueen.com/file_sources/seeko/demo/';

$demo_config = array(
	'theme_name' => 'Seeko',
	'theme_lower' => 'seeko',
);

$demos  = [];

$general_bp_fields = [
	array(
		'field_group_id' => '1',
		'name'           => 'Country',
		'can_delete'     => 1,
		'is_required'    => false,
		'type'           => 'selectbox',
		'old_id'           => '40',
		'options'        => array(
			"Afghanistan",
			"Albania",
			"Algeria",
			"Andorra",
			"Angola",
			"Antigua and Barbuda",
			"Argentina",
			"Armenia",
			"Australia",
			"Austria",
			"Azerbaijan",
			"Bahamas",
			"Bahrain",
			"Bangladesh",
			"Barbados",
			"Belarus",
			"Belgium",
			"Belize",
			"Benin",
			"Bhutan",
			"Bolivia",
			"Bosnia and Herzegovina",
			"Botswana",
			"Brazil",
			"Brunei",
			"Bulgaria",
			"Burkina Faso",
			"Burundi",
			"Cambodia",
			"Cameroon",
			"Canada",
			"Cape Verde",
			"Central African Republic",
			"Chad",
			"Chile",
			"China",
			"Colombi",
			"Comoros",
			"Congo (Brazzaville)",
			"Congo",
			"Costa Rica",
			"Cote d'Ivoire",
			"Croatia",
			"Cuba",
			"Cyprus",
			"Czech Republic",
			"Denmark",
			"Djibouti",
			"Dominica",
			"Dominican Republic",
			"East Timor (Timor Timur)",
			"Ecuador",
			"Egypt",
			"El Salvador",
			"Equatorial Guinea",
			"Eritrea",
			"Estonia",
			"Ethiopia",
			"Fiji",
			"Finland",
			"France",
			"Gabon",
			"Gambia, The",
			"Georgia",
			"Germany",
			"Ghana",
			"Greece",
			"Grenada",
			"Guatemala",
			"Guinea",
			"Guinea-Bissau",
			"Guyana",
			"Haiti",
			"Honduras",
			"Hungary",
			"Iceland",
			"India",
			"Indonesia",
			"Iran",
			"Iraq",
			"Ireland",
			"Israel",
			"Italy",
			"Jamaica",
			"Japan",
			"Jordan",
			"Kazakhstan",
			"Kenya",
			"Kiribati",
			"Korea, North",
			"Korea, South",
			"Kuwait",
			"Kyrgyzstan",
			"Laos",
			"Latvia",
			"Lebanon",
			"Lesotho",
			"Liberia",
			"Libya",
			"Liechtenstein",
			"Lithuania",
			"Luxembourg",
			"Macedonia",
			"Madagascar",
			"Malawi",
			"Malaysia",
			"Maldives",
			"Mali",
			"Malta",
			"Marshall Islands",
			"Mauritania",
			"Mauritius",
			"Mexico",
			"Micronesia",
			"Moldova",
			"Monaco",
			"Mongolia",
			"Morocco",
			"Mozambique",
			"Myanmar",
			"Namibia",
			"Nauru",
			"Nepal",
			"Netherlands",
			"New Zealand",
			"Nicaragua",
			"Niger",
			"Nigeria",
			"Norway",
			"Oman",
			"Pakistan",
			"Palau",
			"Panama",
			"Papua New Guinea",
			"Paraguay",
			"Peru",
			"Philippines",
			"Poland",
			"Portugal",
			"Qatar",
			"Romania",
			"Russia",
			"Rwanda",
			"Saint Kitts and Nevis",
			"Saint Lucia",
			"Saint Vincent",
			"Samoa",
			"San Marino",
			"Sao Tome and Principe",
			"Saudi Arabia",
			"Senegal",
			"Serbia and Montenegro",
			"Seychelles",
			"Sierra Leone",
			"Singapore",
			"Slovakia",
			"Slovenia",
			"Solomon Islands",
			"Somalia",
			"South Africa",
			"Spain",
			"Sri Lanka",
			"Sudan",
			"Suriname",
			"Swaziland",
			"Sweden",
			"Switzerland",
			"Syria",
			"Taiwan",
			"Tajikistan",
			"Tanzania",
			"Thailand",
			"Togo",
			"Tonga",
			"Trinidad and Tobago",
			"Tunisia",
			"Turkey",
			"Turkmenistan",
			"Tuvalu",
			"Uganda",
			"Ukraine",
			"United Arab Emirates",
			"United Kingdom",
			"United States",
			"Uruguay",
			"Uzbekistan",
			"Vanuatu",
			"Vatican City",
			"Venezuela",
			"Vietnam",
			"Yemen",
			"Zambia",
			"Zimbabwe"
		),
	),
	array(
		'field_group_id' => '1',
		'name'           => 'City',
		'can_delete'     => 1,
		'is_required'    => false,
		'type'           => 'textbox',
		'old_id'           => '39',
	),
];
$bp_fields = array(
	array(
		'field_group_id' => '1',
		'name'           => 'Birthday',
		'can_delete'     => 1,
		'is_required'    => false,
		'type'           => 'datebox',
		'old_id'           => '24',
	),
	array(
		'field_group_id' => '1',
		'name'           => 'I am a',
		'can_delete'     => 1,
		'is_required'    => true,
		'type'           => 'selectbox',
		'old_id'           => '25',
		'options'        => array(
			'Man',
			'Woman',
		),
	),
	array(
		'field_group_id' => '1',
		'name'           => 'Looking for a',
		'can_delete'     => 1,
		'is_required'    => true,
		'type'           => 'selectbox',
		'old_id'           => '28',
		'options'        => array(
			'Woman',
			'Man',
		),
	),
	array(
		'field_group_id' => '1',
		'name'           => 'Marital status',
		'can_delete'     => 1,
		'is_required'    => false,
		'type'           => 'selectbox',
		'old_id'           => '31',
		'options'        => array(
			esc_html__( 'Single', 'seeko' ),
			esc_html__( 'Living together', 'seeko' ),
			esc_html__( 'Married', 'seeko' ),
			esc_html__('Separated', 'seeko' ),
			esc_html__( 'Divorced', 'seeko' ),
			esc_html__( 'Widowed', 'seeko' ),
			esc_html__( 'Prefer not to say', 'seeko' ),
		),
	),
);
$bp_fields = array_merge( $bp_fields, $general_bp_fields );
$plugins = [
	'sq-theme-core',
	'elementor',
	'seeko-search',
	'buddypress',
	'paid-memberships-pro',
	'bp-matching',
	'sq-likes',
	'sq-login-booster',
	'contact-form-7',
	'sq-rainbow-categories'
];
if ( function_exists( 'stax_fs' ) && stax_fs()->is_premium() && ! defined( 'STAX_DEV' ) ) {
	$plugins[] = 'stax-premium';
} else {
	$plugins[] = 'stax';
}

$demos['dating']          = array(
	'name'      => 'Dating Site',
	'slug'      => 'dating',
	'img'       => $remote_path . 'dating/preview.jpg',
	'page'      => 'content/pages',
	'widgets' => 'widget_data',
	'bp_fields'  => $bp_fields,
	'pmpro'  => 'pmpro',
	'stax'     =>  $remote_path . 'dating/stax-header.json',
	'extra'     => array(
		array(
			'id'      => 'posts',
			'name'    => 'Import Posts',
			'data'    => 'content/posts',
			'checked' => true,
		),
		array(
			'id'      => 'menu',
			'name'    => 'Import Menu',
			'slug'    => 'menu-1',
			'data'    => 'content/menus',
			'checked' => true,
		),
		array(
			'id'      => 'seeko-forms',
			'name'    => 'Import Search forms',
			'data'    => 'content/seeko-forms',
			'checked' => true,
		),
		array(
			'id'      => 'elementor',
			'name'    => 'Import Elementor Templates',
			'data'    => 'content/elementor',
			'checked' => true,
		),
		array(
			'id'      => 'cf7',
			'name'    => 'Import Contact form',
			'data'    => 'content/cf7',
			'checked' => true,
		),
	),
	'attach'    => 'yes',
	'plugins'   => $plugins,
	//'revslider' => '',
	'link'      => 'https://seeko.seventhqueen.com/dating/',
);


$bp_fields = [
	array(
		'field_group_id' => '1',
		'name'           => 'Member Type',
		'can_delete'     => 1,
		'is_required'    => true,
		'type'           => 'radio',
		'old_id'           => '438',
		'options'        => array(
			'Mentor',
			'Mentee',
		),
	),
	array(
		'field_group_id' => '1',
		'name'           => 'Area of expertise',
		'can_delete'     => 1,
		'is_required'    => true,
		'type'           => 'multiselectbox',
		'old_id'           => '321',
		'options'        => array(
			'Architectural Designer',
			'Art Director',
			'Artist',
			'Brand Manager',
			'Chief Creative Officer',
			'Cinematographer',
			'Communication Design',
			'Copywriter',
			'Creative Director',
			'Creative Producer',
			'Creative Strategist',
			'Creative Writer',
			'Design Director',
			'Digital Designer',
			'Experience Designer',
			'Frontend Developer',
			'Graphic Designer',
			'Head of Design',
			'Head of Product',
			'Interactive Designer',
			'Marketer',
			'Multimedia Designer',
			'Photographer',
			'Product Designer',
			'Software Engineer',
			'Typeface Designer',
			'UX/UI Designer',
			'Web Designer',
			'Writer',
		),
	),
];
$bp_fields = array_merge( $bp_fields, $general_bp_fields );
$plugins = [
	'sq-theme-core',
	'elementor',
	'seeko-search',
	'buddypress',
	'sq-likes',
	'sq-rainbow-categories'
];
if ( function_exists( 'stax_fs' ) && stax_fs()->is_premium() && ! defined( 'STAX_DEV' ) ) {
	$plugins[] = 'stax-premium';
} else {
	$plugins[] = 'stax';
}

$demos['mentoring']          = array(
	'name'      => 'Mentoring Site',
	'slug'      => 'mentoring',
	'img'       => $remote_path . 'mentoring/preview.jpg',
	'page'      => 'content-mentoring/pages',
	'options'   => 'mentoring',
	'bp_fields'  => $bp_fields,
	'stax'     =>  $remote_path . 'mentoring/stax-header.json',
	'extra'     => array(
		array(
			'id'      => 'posts',
			'name'    => 'Import Posts',
			'data'    => 'content-mentoring/posts',
			'checked' => true,
		),
		array(
			'id'      => 'menu',
			'name'    => 'Import Menu',
			'slug'    => 'mentoring',
			'data'    => 'content-mentoring/menus',
			'checked' => true,
		),
		array(
			'id'      => 'seeko-forms',
			'name'    => 'Import Search forms',
			'data'    => 'content-mentoring/seeko-forms',
			'checked' => true,
		),
		array(
			'id'      => 'elementor',
			'name'    => 'Import Elementor Templates',
			'data'    => 'content-mentoring/elementor',
			'checked' => true,
		),
	),
	'attach'    => 'yes',
	'plugins'   => $plugins,
	'link'      => 'https://seeko.seventhqueen.com/mentoring/',
);

add_action( 'svq_after_import', function( $set_data, $selected_options ) {
	if ( $set_data['slug'] == 'mentoring' ) {
		if (  isset( $selected_options['import_options'] ) ) {
			$footer_id = get_page_by_path( 'footer-mentoring', OBJECT, 'elementor_library' );
			if ( $footer_id ) {
				set_theme_mod( 'footer_el_tpl', $footer_id->ID );
			}

		}
	}

}, 10, 2 );

$demo_config['data_set'] = $demos;

SVQImport::getInstance( $demo_config );
