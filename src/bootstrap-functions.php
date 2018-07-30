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
require_once( 'attr/functions-attr.php'             );
require_once( 'comment/functions-comment.php'       );
require_once( 'lang/functions-lang.php'             );
require_once( 'media/functions-media.php'           );
require_once( 'menu/functions-menu.php'             );
require_once( 'pagination/functions-pagination.php' );
require_once( 'post/functions-post.php'             );
require_once( 'sidebar/functions-sidebar.php'       );
require_once( 'site/functions-site.php'             );
require_once( 'template/functions-template.php'     );
require_once( 'theme/functions-theme.php'           );
require_once( 'view/functions-view.php'             );
