<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add extra support for post types. */
add_action( 'init', 'hybrid_add_post_type_support' );

/* Add extra file headers for themes. */
add_filter( 'extra_theme_headers', 'hybrid_extra_theme_headers' );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 0.8.0
 * @access private
 * @return void
 */
function hybrid_add_post_type_support() {

	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );

	/* Add support for trackbacks to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'trackbacks' ) );
}

/**
 * Creates custom theme headers.  This is the information shown in the header block of a theme's 'style.css' 
 * file.  Themes are not required to use this information, but the framework does make use of the data for 
 * displaying additional information to the theme user.
 *
 * @since 1.2.0
 * @access private
 * @link http://codex.wordpress.org/Theme_Review#Licensing
 * @param array $headers Array of extra headers added by plugins/themes.
 * @return array $headers
 */
function hybrid_extra_theme_headers( $headers ) {

	/* Add support for 'Template Version'. This is for use in child themes to note the version of the parent theme. */
	if ( !in_array( 'Template Version', $headers ) )
		$headers[] = 'Template Version';

	/* Add support for 'License'.  Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License', $headers ) )
		$headers[] = 'License';

	/* Add support for 'License URI'. Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License URI', $headers ) )
		$headers[] = 'License URI';

	/* Add support for 'Support URI'.  This should be a link to the theme's support forums. */
	if ( !in_array( 'Support URI', $headers ) )
		$headers[] = 'Support URI';

	/* Add support for 'Documentation URI'.  This should be a link to the theme's documentation. */
	if ( !in_array( 'Documentation URI', $headers ) )
		$headers[] = 'Documentation URI';

	/* Return the array of custom theme headers. */
	return $headers;
}

/**
 * Looks for a template based on the hybrid_get_context() function.  If the $template parameter
 * is a directory, it will look for files within that directory.  Otherwise, $template becomes the 
 * template name prefix.  The function looks for templates based on the context of the current page
 * being viewed by the user.
 *
 * @since 0.8.0
 * @access public
 * @param string $template The slug of the template whose context we're searching for.
 * @return string $template The full path of the located template.
 */
function get_atomic_template( $template ) {

	$templates = array();

	$theme_dir = trailingslashit( THEME_DIR ) . $template;
	$child_dir = trailingslashit( CHILD_THEME_DIR ) . $template;

	if ( is_dir( $child_dir ) || is_dir( $theme_dir ) ) {
		$dir = true;
		$templates[] = "{$template}/index.php";
	}
	else {
		$dir = false;
		$templates[] = "{$template}.php";
	}

	foreach ( hybrid_get_context() as $context )
		$templates[] = ( ( $dir ) ? "{$template}/{$context}.php" : "{$template}-{$context}.php" );

	return locate_template( array_reverse( $templates ), true );
}

/**
 * Generates the relevant template info.  Adds template meta with theme version.  Uses the theme 
 * name and version from style.css.  In 0.6, added the hybrid_meta_template 
 * filter hook.
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function hybrid_meta_template() {
	$data = hybrid_get_theme_data();
	$template = '<meta name="template" content="' . esc_attr( "{$data['Title']} {$data['Version']}" ) . '" />' . "\n";
	echo apply_atomic( 'meta_template', $template );
}

/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function hybrid_site_title() {

	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<%1$s id="site-title"><a href="%2$s" title="%3$s" rel="home"><span>%4$s</span></a></%1$s>', tag_escape( $tag ), home_url(), esc_attr( $title ), $title );

	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function hybrid_site_description() {

	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<%1$s id="site-description"><span>%2$s</span></%1$s>', tag_escape( $tag ), $desc );

	/* Display the site description and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_description', $desc );
}

/**
 * Checks if a post of any post type has a custom template.  This is the equivalent of WordPress' 
 * is_page_template() function with the exception that it works for all post types.
 *
 * @since 1.2.0
 * @access public
 * @param string $template The name of the template to check for.
 * @return bool Whether the post has a template.
 */
function hybrid_has_post_template( $template = '' ) {

	/* Assume we're viewing a singular post. */
	if ( is_singular() ) {

		/* Get the queried object. */
		$post = get_queried_object();

		/* Get the post template, which is saved as metadata. */
		$post_template = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );

		/* If a specific template was input, check that the post template matches. */
		if ( !empty( $template) && ( $template == $post_template ) )
			return true;

		/* If no specific template was input, check if the post has a template. */
		elseif ( empty( $template) && !empty( $post_template ) )
			return true;
	}

	/* Return false for everything else. */
	return false;
}

?>