/**
 * Radio image customize control script.
 *
 * This script is required for the `Hybrid\Customize\Controls\RadioImage`
 * customize control to work.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

wp.customize.controlConstructor['hybrid-radio-image'] = wp.customize.Control.extend( {
	ready: function() {

		let control = this;
		let inputs  = document.querySelectorAll(
			control.selector + ' input[type="radio"]'
		);

		for ( var i = 0; i < inputs.length; i++ ) {

			inputs[ i ].onchange = function() {

				control.setting.set( this.value );
			}
		}
	}
} );
