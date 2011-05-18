<?php
/**
 * Lowercase p, Dangit! - Fixes the capital_P_dangit() WordPress function.
 *
 * In WordPress 3.0, the capital_P_dangit() function was added to the WordPress core source code, effectively 
 * breaking blogs and published content.  This script removes this filter completely and gives control of content 
 * back to the users of the WordPress software.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package LowercasePDangit
 * @version 0.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2011, Justin Tadlock
 * @link http://justintadlock.com/archives/2010/07/08/lowercase-p-dangit
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Remove the capital_P_dangit() filter from post titles. */
remove_filter( 'the_title', 'capital_P_dangit', 11 );

/* Remove the capital_P_dangit() filter from post content. */
remove_filter( 'the_content', 'capital_P_dangit', 11 );

/* Remove the capital_P_dangit() filter from comment text. */
remove_filter( 'comment_text', 'capital_P_dangit', 31 );

/* We'll go ahead and take care of this for bbPress too. */
remove_filter( 'bbp_get_reply_content', 'capital_P_dangit' );
remove_filter( 'bbp_get_topic_content', 'capital_P_dangit' );

?>