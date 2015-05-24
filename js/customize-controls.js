jQuery( document ).ready( function() {

	/* === Color Palette Control === */

	jQuery( '.customize-control-palette input:radio:checked' ).parent( 'label' ).addClass( 'selected' );

	jQuery( '.customize-control-palette input:radio' ).change(
		function() {
			jQuery( this ).parents( '.customize-control-palette' ).find( 'label.selected' ).removeClass( 'selected' );
			jQuery( this ).parent( 'label' ).addClass( 'selected' );
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