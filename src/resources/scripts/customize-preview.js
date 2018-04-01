jQuery( document ).ready( function() {

	wp.customize(
		'theme_layout',
		function( value ) {
			value.bind(
				function( to ) {
					var classes = jQuery( 'body' ).attr( 'class' ).replace( /\slayout-[a-zA-Z0-9_-]*/g, '' );
					jQuery( 'body' ).attr( 'class', classes ).addClass( 'layout-' + to );
				}
			);
		}
	);

} ); // jQuery( document ).ready
