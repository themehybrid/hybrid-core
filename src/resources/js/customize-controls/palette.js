/**
 * Palette customize control script.
 *
 * This script is required for the `Hybrid\Customize\Controls\Palette` customize
 * control to work.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

wp.customize.controlConstructor['hybrid-palette'] = wp.customize.Control.extend( {
	ready: function() {

		let control = this;
		let inputs  = document.querySelectorAll(
			control.selector + ' input[type="radio"]'
		);

		// Loops through the radio inputs. If the input is checked, add
		// the `.selected` class to the parent `<label>` element.
		for ( var i = 0; i < inputs.length; i++ ) {

			if ( inputs[ i ].checked ) {
				inputs[ i ].parentNode.classList.add( 'is-selected' );
			}

			inputs[ i ].onchange = function() {

				for ( var i = 0; i < inputs.length; i++ ) {

					if ( inputs[ i ].parentNode.classList.contains( 'is-selected' ) ) {
						inputs[ i ].parentNode.classList.remove( 'is-selected' );
					}
				}

				if ( this.checked ) {
					this.parentNode.classList.add( 'is-selected' );
				}

				control.setting.set( this.value );
			}
		}
	}
} );
