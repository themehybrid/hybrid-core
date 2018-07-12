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
		'functions-filters',
		'functions-fonts',
		'functions-formats',
		'functions-head',
		'functions-helpers',
		'functions-lang',
		'functions-utility',
		'template-general',

		'attr/template-attr',
		'comment/template-comment',
		'media/template-media',
		'media-grabber/template-media-grabber',
		'media-meta/template-media-meta',
		'menu/template-menu',
		'pagination/template-pagination',
		'post/template-post',
		'sidebar/template-sidebar',
		'site/template-site',
		'template/functions-template',
		'theme/template-theme',
		'view/template-view'
	]
);
