<?php
/**
 * Defines many of the action hooks avialable throughout the theme. Rather than apply the action
 * hook directly in template files, most have accompanying functions that serve as wrappers, which
 * allows for changes in the future.
 *
 * Most action hooks will use the do_atomic() function, which creates contextual action hooks.
 *
 * Action hooks should be defined and named by the order called (generated) relative to a theme element.
 * Most 'before' action hooks appear just inside of the opening XHTML element it's named after.
 * Most 'after' action hooks appear just inside of the closing XHTML element it's named after.
 * @link http://themehybrid.com/themes/hybrid/hooks/actions
 *
 * @package Hybrid
 * @subpackage Functions
 */

/**
 * Before HTML.  Loaded just after <body> but before any content is displayed.
 * @since 0.3.2
 */
function hybrid_before_html() {
	do_atomic( 'before_html' );
}

/**
 * After HTML.
 * Loaded just before </body> and after all content.
 * @since 0.3.2
 */
function hybrid_after_html() {
	do_atomic( 'after_html' );
}

/**
 * Added to the header before wp_head().
 * @since 0.1
 */
function hybrid_head() {
	do_atomic( 'head' );
}

/**
 * Before the header.
 * @since 0.1
 */
function hybrid_before_header() {
	do_atomic( 'before_header' );
}

/**
 * Header.
 * @since 0.1
 */
function hybrid_header() {
	do_atomic( 'header' );
}

/**
 * After the header.
 * @since 0.1
 */
function hybrid_after_header() {
	do_atomic( 'after_header' );
}

/**
 * Before primary menu.
 * @since 0.8
 */
function hybrid_before_primary_menu() {
	do_atomic( 'before_primary_menu' );
}

/**
 * After primary menu.
 * @since 0.8
 */
function hybrid_after_primary_menu() {
	do_atomic( 'after_primary_menu' );
}

/**
 * Before the container.
 * @since 0.1
 */
function hybrid_before_container() {
	do_atomic( 'before_container' );
}

/**
 * Before the content.
 * @since 0.1
 */
function hybrid_before_content() {
	do_atomic( 'before_content' );
}

/**
 * After the content.
 * @since 0.1
 */
function hybrid_after_content() {
	do_atomic( 'after_content' );
}

/**
 * Before each entry.
 * @since 0.5
 */
function hybrid_before_entry() {
	do_atomic( 'before_entry' );
}

/**
 * After each entry.
 * @since 0.5
 */
function hybrid_after_entry() {
	do_atomic( 'after_entry' );
}

/**
 * After singular views but before the comments template.
 * @since 0.7
 */
function hybrid_after_singular() {
	if ( is_singular( 'post' ) && !is_attachment() )
		do_action( 'hybrid_after_single' ); // Deprecated
	elseif ( is_page() )
		do_action( 'hybrid_after_page' ); // Deprecated

	do_atomic( 'after_singular' );
}

/**
 * Before the primary widget area content.  Only called if Primary is active.
 * @since 0.1
 */
function hybrid_before_primary() {
	do_atomic( 'before_primary' );
}

/**
 * After the primary widget area content.  Only called if Primary is active.
 * @since 0.1
 */
function hybrid_after_primary() {
	do_atomic( 'after_primary' );
}

/**
 * Before the secondary widget area.  Only called if Secondary is active.
 * @since 0.2
 */
function hybrid_before_secondary() {
	do_atomic( 'before_secondary' );
}

/**
 * After the secondary widget area.  Only called if Secondary is active.
 * @since 0.2
 */
function hybrid_after_secondary() {
	do_atomic( 'after_secondary' );
}

/**
 * Before the subsidiary widget area.  Only called if Subsidiary is active.
 * @since 0.3.1
 */
function hybrid_before_subsidiary() {
	do_atomic( 'before_subsidiary' );
}

/**
 * After the subsidiary widget area.  Only called if Subsidiary is active.
 * @since 0.3.1
 */
function hybrid_after_subsidiary() {
	do_atomic( 'after_subsidiary' );
}

/**
 * After the container area.
 * @since 0.1
 */
function hybrid_after_container() {
	do_atomic( 'after_container' );
}

/**
 * Before the footer.
 * @since 0.1
 */
function hybrid_before_footer() {
	do_atomic( 'before_footer' );
}

/**
 * The footer.
 * @since 0.1
 */
function hybrid_footer() {
	do_atomic( 'footer' );
}

/**
 * After the footer.
 * @since 0.1
 */
function hybrid_after_footer() {
	do_atomic( 'after_footer' );
}

/**
 * Fires before each comment's information.
 * @since 0.5
 */
function hybrid_before_comment() {
	do_atomic( 'before_comment' );
}

/**
 * Fires after each comment's information.
 * @since 0.5
 */
function hybrid_after_comment() {
	do_atomic( 'after_comment' );
}

/**
 * Fires before the comment list.
 * @since 0.6
 */
function hybrid_before_comment_list() {
	do_atomic( 'before_comment_list' );
}

/**
 * Fires after the comment list.
 * @since 0.6
 */
function hybrid_after_comment_list() {
	do_atomic( 'after_comment_list' );
}

?>