<?php
/**
 * Functions files bootstrap.
 *
 * This file loads all of our functions files necessary for using the framework.
 * Note that we are not loading class files.  Those are loaded via the autoloader
 * in `bootstrap-autoload.php`.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Autoloads our custom functions files that are not loaded via the class loader.
array_map(
	function( $file ) {
		require_once( "{$file}.php" );
	},
	[
		'functions-assets',
		'functions-attr',
		'functions-context',
		'functions-deprecated',
		'functions-filters',
		'functions-fonts',
		'functions-formats',
		'functions-head',
		'functions-helpers',
		'functions-lang',
		'functions-templates',
		'functions-utility',
		'template-comments',
		'template-general',
		'template-media',
		'template-menus',
		'template-post',
		'template-sidebars',
		'template-view'
	]
);
