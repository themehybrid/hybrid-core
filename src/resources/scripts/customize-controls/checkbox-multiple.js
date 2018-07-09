/**
 * Checkbox multiple customize control script.
 *
 * This script is required for the `Hybrid\Customize\Controls\CheckboxMultiple`
 * customize control to work.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

wp.customize.controlConstructor['hybrid-checkbox-multiple'] = wp.customize.Control.extend( {
	ready: function() {

		let control    = this;
		let checkboxes = document.querySelectorAll(
			control.selector + ' input[type="checkbox"]'
		);

		// Loops through all of the control's checkboxes and adds an
		// onchange event to update the setting whenever the checkbox
		// checked state changes.
		for ( var i = 0; i < checkboxes.length; i++ ) {

			checkboxes[ i ].onchange = function( event ) {

				let checked = [];

				// Loop through the checkboxes and add any
				// that are checked to our array.
				for ( var i = 0; i < checkboxes.length; i++ ) {

					if ( checkboxes[ i ].checked ) {
						checked.push( checkboxes[ i ].value );
					}
				}

				// Set the value for the control based on
				// the checkboxes that are checked.
				control.setting.set( checked ? checked : '' );
			};
		}
	}
} );
