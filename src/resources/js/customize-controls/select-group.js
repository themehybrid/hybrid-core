/**
 * Select group customize control script.
 *
 * This script is required for the `Hybrid\Customize\Controls\SelectGroup`
 * customize control to work.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

wp.customize.controlConstructor['hybrid-select-group'] = wp.customize.Control.extend( {
	ready: function() {

		let control = this;
		let select  = document.querySelector( control.selector + ' select' );

		select.onchange = function() {
			control.setting.set( this.value );
		}
	}
} );
