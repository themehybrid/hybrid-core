( function( $, api ) {

	/* === Checkbox Multiple Control === */

	api.controlConstructor['checkbox-multiple'] = api.Control.extend( {
		ready: function() {
			var control = this;

			$( 'input:checkbox', control.container ).change(
				function() {

					// Get all of the checkbox values.
					var checkbox_values = $( 'input[type="checkbox"]:checked', control.container ).map(
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
			$( 'input:radio:checked', control.container ).parent( 'label' ).addClass( 'selected' );

			$( 'input:radio', control.container ).change(
				function() {

					// Removes the `.selected` class from other labels and adds it to the new one.
					$( 'label.selected', control.container ).removeClass( 'selected' );
					$( this ).parent( 'label' ).addClass( 'selected' );

					control.setting.set( $( this ).val() );
				}
			);
		}
	} );

	/* === Radio Image Control === */

	api.controlConstructor['radio-image'] = api.Control.extend( {
		ready: function() {
			var control = this;

			$( 'input:radio', control.container ).change(
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		}
	} );

	/* === Select Group Control === */

	api.controlConstructor['select-group'] = api.Control.extend( {
		ready: function() {
			var control = this;

			$( 'select', control.container ).change(
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		}
	} );

	/* === Select Multiple Control === */

	api.controlConstructor['select-multiple'] = api.Control.extend( {
		ready: function() {
			var control = this;

			$( 'select', control.container ).change(
				function() {
					var value = $( this ).val();

					if ( null === value ) {
						control.setting.set( '' );
					} else {
						control.setting.set( value );
					}
				}
			);
		}
	} );

} )( jQuery, wp.customize );
