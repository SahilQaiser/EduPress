( function( api ) {

	// Extends our custom "vw-education-academy" section.
	api.sectionConstructor['vw-education-academy'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );