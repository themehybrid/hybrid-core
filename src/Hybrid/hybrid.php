<?php
/**
 * Hybrid Core - A WordPress theme development framework.
 *
 * Hybrid Core is a framework for developing WordPress themes.  The framework
 * allows theme developers to quickly build themes without having to handle all
 * of the "logic" behind the theme or having to code complex functionality for
 * features that are often needed in themes.  The framework does these things
 * for developers to allow them to get back to what matters the most:  developing
 * and designing themes. Themes handle all the markup, style, and scripts while
 * the framework handles the logic.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St,
 * Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   HybridCore
 * @version   5.0.0-dev
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Define the directory path to the framework. This shouldn't need changing
# unless doing something really out there or just for clarity.
if ( ! defined( 'HYBRID_DIR' ) ) {

	define( 'HYBRID_DIR', __DIR__ );
}

# Define the directory URI to the framework. The assumption is that the framework
# is loaded from the parent theme folder. If not, this definitely needs to be
# defined for scripts/styles.
if ( ! defined( 'HYBRID_URI' ) ) {

	define( 'HYBRID_URI', str_replace(
		get_template_directory(),
		get_template_directory_uri(),
		wp_normalize_path( __DIR__ )
	) );
}
