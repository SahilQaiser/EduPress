<?php

add_filter( 'user_contactmethods', 'svq_update_contactmethods', 10);
function svq_update_contactmethods($contact_methods) {
	// Add Twitter.
	$contact_methods['profession'] = esc_html__( 'Profession title', 'seeko' );
	$contact_methods['twitter'] = esc_html__( 'Twitter username (without @)', 'seeko' );
	// Add Facebook.
	$contact_methods['facebook'] = esc_html__( 'Facebook profile URL', 'seeko' );

	// Add LinkedIn.
	$contact_methods['linkedin'] = esc_html__( 'LinkedIn URL', 'seeko' );

	return $contact_methods;
}
