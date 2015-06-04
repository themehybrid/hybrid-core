jQuery( document ).ready( function() {

	/* === Color Palette Control === */

	jQuery( '.customize-control-palette input:radio:checked' ).parent( 'label' ).addClass( 'selected' );

	jQuery( '.customize-control-palette input:radio' ).change(
		function() {

			// Switch the `.selected` class on the label wrapping the selected radio input.
			jQuery( this ).parents( '.customize-control-palette' ).find( 'label.selected' ).removeClass( 'selected' );
			jQuery( this ).parent( 'label' ).addClass( 'selected' );

			// Get the name of the setting.
			var setting = jQuery( this ).attr( 'data-customize-setting-link' );

			// Get the value of the currently-checked radio input.
			var palette = jQuery( this ).val();

			// Set the new value.
			wp.customize( setting, function( obj ) {

				obj.set( palette );
			} );
		}
	);

	/* === Radio Image Control === */

	if ( jQuery.isFunction( jQuery.fn.buttonset ) ) {
		jQuery( '.customize-control-radio-image .buttonset' ).buttonset();
	}

	/* === Checkbox Multiple Control === */

	jQuery( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on(
		'change',
		function() {

			checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
				function() {
					return this.value;
				}
			).get().join( ',' );

			jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
		}
	);

} ); // jQuery( document ).ready