<?php
/**
 * Shortcodes bundled for use with themes.  These shortcodes are not meant to be used with the post content 
 * editor.  Their purpose is to make it easier for users to filter hooks without having to know too much PHP code
 * and to provide access to specific functionality in other (non-post content) shortcode-aware areas.  Note that 
 * some shortcodes are specific to posts and comments and would be useless outside of the post and comment 
 * loops.  To use the shortcodes, a theme must register support for 'hybrid-core-shortcodes'.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register shortcodes. */
add_action( 'init', 'hybrid_add_shortcodes' );

/**
 * Creates new shortcodes for use in any shortcode-ready area.  This function uses the add_shortcode() 
 * function to register new shortcodes with WordPress.
 *
 * @since 0.8.0
 * @access public
 * @uses add_shortcode() to create new shortcodes.
 * @link http://codex.wordpress.org/Shortcode_API
 * @return void
 */
function hybrid_add_shortcodes() {

	/* Add theme-specific shortcodes. */
	add_shortcode( 'the-year',      'hybrid_the_year_shortcode' );
	add_shortcode( 'site-link',     'hybrid_site_link_shortcode' );
	add_shortcode( 'wp-link',       'hybrid_wp_link_shortcode' );
	add_shortcode( 'theme-link',    'hybrid_theme_link_shortcode' );
	add_shortcode( 'child-link',    'hybrid_child_link_shortcode' );

	/* Only register deprected shortcodes if the theme supports deprecated functions. */
	if ( current_theme_supports( 'hybrid-core-deprecated' ) ) {

		/* Theme shortcodes. */
		add_shortcode( 'loginout-link', 'hybrid_loginout_link_shortcode' );
		add_shortcode( 'query-counter', 'hybrid_query_counter_shortcode' );
		add_shortcode( 'nav-menu',      'hybrid_nav_menu_shortcode' );

		/* Add entry-specific shortcodes. */
		add_shortcode( 'entry-title',         'hybrid_entry_title_shortcode' );
		add_shortcode( 'entry-author',        'hybrid_entry_author_shortcode' );
		add_shortcode( 'entry-terms',         'hybrid_entry_terms_shortcode' );
		add_shortcode( 'entry-comments-link', 'hybrid_entry_comments_link_shortcode' );
		add_shortcode( 'entry-published',     'hybrid_entry_published_shortcode' );
		add_shortcode( 'entry-edit-link',     'hybrid_entry_edit_link_shortcode' );
		add_shortcode( 'entry-shortlink',     'hybrid_entry_shortlink_shortcode' );
		add_shortcode( 'entry-permalink',     'hybrid_entry_permalink_shortcode' );
		add_shortcode( 'post-format-link',    'hybrid_post_format_link_shortcode' );

		/* Add comment-specific shortcodes. */
		add_shortcode( 'comment-published',  'hybrid_comment_published_shortcode' );
		add_shortcode( 'comment-author',     'hybrid_comment_author_shortcode' );
		add_shortcode( 'comment-edit-link',  'hybrid_comment_edit_link_shortcode' );
		add_shortcode( 'comment-reply-link', 'hybrid_comment_reply_link_shortcode' );
		add_shortcode( 'comment-permalink',  'hybrid_comment_permalink_shortcode' );
	}
}

/**
 * Shortcode to display the current year.
 *
 * @since 0.6.0
 * @access public
 * @uses date() Gets the current year.
 * @return string
 */
function hybrid_the_year_shortcode() {
	return date_i18n( 'Y' );
}

/**
 * Shortcode to display a link back to the site.
 *
 * @since 0.6.0
 * @access public
 * @uses get_bloginfo() Gets information about the install.
 * @return string
 */
function hybrid_site_link_shortcode() {
	return hybrid_get_site_link();
}

/**
 * Shortcode to display a link to WordPress.org.
 *
 * @since 0.6.0
 * @access public
 * @return string
 */
function hybrid_wp_link_shortcode() {
	return hybrid_get_wp_link();
}

/**
 * Shortcode to display a link to the parent theme page.
 *
 * @since 0.6.0
 * @access public
 * @uses get_theme_data() Gets theme (parent theme) information.
 * @return string
 */
function hybrid_theme_link_shortcode() {
	return hybrid_get_theme_link();
}

/**
 * Shortcode to display a link to the child theme's page.
 *
 * @since 0.6.0
 * @access public
 * @uses get_theme_data() Gets theme (child theme) information.
 * @return string
 */
function hybrid_child_link_shortcode() {
	return hybrid_get_child_theme_link();
}
