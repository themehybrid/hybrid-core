/* === Checkbox Multiple Control === */

wp.customize.controlConstructor['checkbox-multiple'] = wp.customize.Control.extend( {
	ready: function() {
		var control = this;

		this.container.on( 'change', 'input:checkbox',
			function() {

				// Get all of the checkbox values.
				var checkbox_values = jQuery( 'input[type="checkbox"]:checked', control.container ).map(
					function() {
						return this.value;
					}
				).get();

				// Set the value.
				if ( null === checkbox_values ) {
					control.setting.set( '' );
				} else {
					control.setting.set( checkbox_values );
				}
			}
		);
	}
} );

/* === Palette Control === */

wp.customize.controlConstructor['palette'] = wp.customize.Control.extend( {
	ready: function() {

		var control = this;

		jQuery( 'input:radio:checked', control.container ).parent( 'label' ).addClass( 'selected' );

		this.container.on( 'change', 'input:radio',
			function() {

				jQuery( 'label.selected', control.container ).removeClass( 'selected' );
				jQuery( this ).parent( 'label' ).addClass( 'selected' );

				control.setting.set( jQuery( this ).val() );
			}
		);
	}
} );

/* === Radio Image Control === */

wp.customize.controlConstructor['radio-image'] = wp.customize.Control.extend( {
	ready: function() {

		var control = this;

		// Check if the `buttonset()` function exists. If it does, use it for radio images.
		if ( jQuery.isFunction( jQuery.fn.buttonset ) ) {
			jQuery( '.buttonset', control.container ).buttonset();
		}

		this.container.on( 'change', 'input:radio',
			function() {
				control.setting.set( jQuery( this ).val() );
			}
		);
	}
} );

/* === Select Group Control === */

wp.customize.controlConstructor['select-group'] = wp.customize.Control.extend( {
	ready: function() {
		var control = this;

		this.container.on( 'change', 'select',
			function() {
				control.setting.set( jQuery( this ).val() );
			}
		);
	}
} );

/* === Select Multiple Control === */

wp.customize.controlConstructor['select-multiple'] = wp.customize.Control.extend( {
	ready: function() {
		var control = this;

		this.container.on( 'change', 'select',
			function() {
				var value = jQuery( this ).val();

				if ( null === value ) {
					control.setting.set( '' );
				} else {
					control.setting.set( value );
				}
			}
		);
	}
} );