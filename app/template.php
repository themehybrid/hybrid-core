<?php
/**
 * Functions for loading template parts.  These functions are helper functions or more flexible
 * functions than what core WordPress currently offers with template part loading.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Template part getter function.  This is more robust than the existing core
 * WordPress template function while being compatible with its hooks.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $slug
 * @param  string  $name
 * @return void
 */
function hybrid_get_template_part( $slug, $name = '' ) {

	do_action( "get_template_part_{$slug}", $slug, $name ); // Core WP hook.

	$templates = array();

	if ( $name ) {
		$templates[] = "{$slug}-{$name}.php";
		$templates[] = "{$slug}/{$name}.php";
	}

	$templates[] = "{$slug}.php";
	$templates[] = "{$slug}/{$slug}.php";

	// Allow devs to filter the hierarchy before attempting to locate.
	$templates = apply_filters( "hybrid_{$slug}_template_hierarchy", $templates, $name );

	// Locate template. Allow devs to filter the final template.
	$template = apply_filters( "hybrid_{$slug}_template", locate_template( $templates ), $name );

	// If a template is found, include it.
	if ( $template )
		include( $template );
}

/**
 * A function for loading a menu template.  This works similar to the WordPress `get_*()` template functions.
 * It's purpose is for loading a menu template part.  This function looks for menu templates within the
 * `menu` sub-folder or the root theme folder.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_menu( $name = '' ) {

	hybrid_get_template_part( 'menu', $name );
}

/**
 * This is a replacement function for the WordPress `get_header()` function. The reason for this function
 * over the core function is because the core function does not provide the functionality needed to properly
 * implement what's needed, particularly the ability to add header templates to a sub-directory.
 * Technically, there's a workaround for that using the `get_header` hook, but it requires keeping a
 * an empty `header.php` template in the theme's root, which will get loaded every time a header template
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only**
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_header()` function.  It does so in two ways:
 * 1) The `get_header` hook is properly fired and 2) The core naming convention of header templates
 * (`header-$name.php` and `header.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_header( $name = '' ) {

	do_action( 'get_header', $name ); // Core WordPress hook

	hybrid_get_template_part( 'header', $name );
}

/**
 * This is a replacement function for the WordPress `get_footer()` function. The reason for this function
 * over the core function is because the core function does not provide the functionality needed to properly
 * implement what's needed, particularly the ability to add footer templates to a sub-directory.
 * Technically, there's a workaround for that using the `get_footer` hook, but it requires keeping a
 * an empty `footer.php` template in the theme's root, which will get loaded every time a footer template
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only**
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_footer()` function.  It does so in two ways:
 * 1) The `get_footer` hook is properly fired and 2) The core naming convention of footer templates
 * (`footer-$name.php` and `footer.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_footer( $name = '' ) {

	do_action( 'get_footer', $name ); // Core WordPress hook

	hybrid_get_template_part( 'footer', $name );
}

/**
 * This is a replacement function for the WordPress `get_sidebar()` function. The reason for this function
 * over the core function is because the core function does not provide the functionality needed to properly
 * implement what's needed, particularly the ability to add sidebar templates to a sub-directory.
 * Technically, there's a workaround for that using the `get_sidebar` hook, but it requires keeping a
 * an empty `sidebar.php` template in the theme's root, which will get loaded every time a sidebar template
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only**
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_sidebar()` function.  It does so in two ways:
 * 1) The `get_sidebar` hook is properly fired and 2) The core naming convention of sidebar templates
 * (`sidebar-$name.php` and `sidebar.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_sidebar( $name = '' ) {

	do_action( 'get_sidebar', $name ); // Core WordPress hook

	hybrid_get_template_part( 'sidebar', $name );
}

/**
 * Loads a post content template based off the post type and/or the post format.  This functionality is
 * not feasible with the WordPress get_template_part() function, so we have to rely on some custom logic
 * and locate_template().
 *
 * Note that using this function assumes that you're creating a content template to handle attachments.
 * The `prepend_attachment()` filter must be removed since we're bypassing the WP template hierarchy
 * and focusing on templates specific to the content.
 *
 * @since  1.6.0
 * @access public
 * @return string
 */
function hybrid_get_content_template() {

	// Set up an empty array and get the post type.
	$templates = array();

	// Assume the theme developer is creating an attachment template.
	if ( 'attachment' === get_post_type() )
		remove_filter( 'the_content', 'prepend_attachment' );

	// Loop through hierarchy and add templates.
	foreach ( hybrid_get_content_hierarchy() as $hier ) {
		$templates[] = "content-{$hier}.php";
		$templates[] = "content/{$hier}.php";
	}

	// Fallback 'content.php' template.
	$templates[] = 'content.php';
	$templates[] = 'content/content.php';

	// Apply filters to the templates array.
	$templates = apply_filters( 'hybrid_content_template_hierarchy', $templates );

	// Locate the template.
	$template = apply_filters( 'hybrid_content_template', locate_template( $templates ), $templates );

	// If template is found, include it.
	if ( $template )
		include( $template );
}

/**
 * Gets the embed template used for embedding posts from the site.
 *
 * @since  4.0.0
 * @access public
 * @return void
 */
function hybrid_get_embed_template() {

	// Set up an empty array and get the post type.
	$templates = array();

	// Assume the theme developer is creating an attachment template.
	if ( 'attachment' === get_post_type() ) {
		remove_filter( 'the_content',       'prepend_attachment'          );
		remove_filter( 'the_excerpt_embed', 'wp_embed_excerpt_attachment' );
	}

	// Loop through hierarchy and add templates.
	foreach ( hybrid_get_content_hierarchy() as $hier ) {
		$templates[] = "embed-{$hier}.php";
		$templates[] = "embed/{$hier}.php";
	}

	// Fallback 'embed/content.php' template.
	$templates[] = 'embed/content.php';

	// Apply filters to the templates array.
	$templates = apply_filters( 'hybrid_embed_template_hierarchy', $templates );

	// Locate the template.
	$template = apply_filters( 'hybrid_embed_template', locate_template( $templates ), $templates );

	// If template is found, include it.
	if ( $template )
		include( $template );
}

/**
 * Creates a hierarchy based on the current post.  For use with content-specific templates.
 *
 * @since  4.0.0
 * @access public
 * @return array
 */
function hybrid_get_content_hierarchy() {

	// Set up an empty array and get the post type.
	$hierarchy = array();
	$post_type = get_post_type();

	// If attachment, add attachment type templates.
	if ( 'attachment' === $post_type ) {

		$type    = hybrid_get_attachment_type();
		$subtype = hybrid_get_attachment_subtype();

		if ( $subtype ) {
			$hierarchy[] = "attachment-{$type}-{$subtype}";
			$hierarchy[] = "attachment-{$subtype}";
		}

		$hierarchy[] = "attachment-{$type}";
	}


	// If the post type supports 'post-formats', get the template based on the format.
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get the post format.
		$post_format = get_post_format() ? get_post_format() : 'standard';

		// Template based off post type and post format.
		$hierarchy[] = "{$post_type}-{$post_format}";

		// Template based off the post format.
		$hierarchy[] = $post_format;
	}

	// Template based off the post type.
	$hierarchy[] = $post_type;

	return $hierarchy;
}
