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
require_once( 'functions-filters.php'               );
require_once( 'functions-helpers.php'               );
require_once( 'Attr/functions-attr.php'             );
require_once( 'Comment/functions-comment.php'       );
require_once( 'Lang/functions-lang.php'             );
require_once( 'Media/functions-media.php'           );
require_once( 'Menu/functions-menu.php'             );
require_once( 'Pagination/functions-pagination.php' );
require_once( 'Post/functions-post.php'             );
require_once( 'Sidebar/functions-sidebar.php'       );
require_once( 'Site/functions-site.php'             );
require_once( 'Template/functions-template.php'     );
require_once( 'Theme/functions-theme.php'           );
require_once( 'View/functions-view.php'             );
