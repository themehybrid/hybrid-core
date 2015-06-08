( function( api ) {

	/* === Checkbox Multiple Control === */

	api.controlConstructor['checkbox-multiple'] = api.Control.extend( {
		ready: function() {
			var control = this;

			control.container.on( 'change', 'input:checkbox',
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

	api.controlConstructor['palette'] = api.Control.extend( {
		ready: function() {
			var control = this;

			// Adds a `.selected` class to the label of checked inputs.
			jQuery( 'input:radio:checked', control.container ).parent( 'label' ).addClass( 'selected' );

			control.container.on( 'change', 'input:radio',
				function() {

					// Removes the `.selected` class from other labels and adds it to the new one.
					jQuery( 'label.selected', control.container ).removeClass( 'selected' );
					jQuery( this ).parent( 'label' ).addClass( 'selected' );

					control.setting.set( jQuery( this ).val() );
				}
			);
		}
	} );

	/* === Radio Image Control === */

	api.controlConstructor['radio-image'] = api.Control.extend( {
		ready: function() {
			var control = this;

			control.container.on( 'change', 'input:radio',
				function() {
					control.setting.set( jQuery( this ).val() );
				}
			);
		}
	} );

	/* === Select Group Control === */

	api.controlConstructor['select-group'] = api.Control.extend( {
		ready: function() {
			var control = this;

			control.container.on( 'change', 'select',
				function() {
					control.setting.set( jQuery( this ).val() );
				}
			);
		}
	} );

	/* === Select Multiple Control === */

	api.controlConstructor['select-multiple'] = api.Control.extend( {
		ready: function() {
			var control = this;

			control.container.on( 'change', 'select',
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

} )( wp.customize );
