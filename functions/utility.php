<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Add extra support for post types. */
add_action( 'init', 'hybrid_add_post_type_support' );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 0.8.0
 */
function hybrid_add_post_type_support() {

	/* Add support for entry-views to the 'post' post type. */
	add_post_type_support( 'post', array( 'entry-views' ) );

	/* Add support for excerpts and entry-views to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt', 'entry-views' ) );

	/* Add support for comments, trackbacks, and entry-views to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'comments', 'trackbacks', 'entry-views' ) );
}

/**
 * Looks for a template based on the hybrid_get_context() function.  If the $template parameter
 * is a directory, it will look for files within that directory.  Otherwise, $template becomes the 
 * template name prefix.  The function looks for templates based on the context of the current page
 * being viewed by the user.
 *
 * @since 0.8.0
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
 */
function hybrid_meta_template() {
	$data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );
	$template = '<meta name="template" content="' . esc_attr( "{$data['Title']} {$data['Version']}" ) . '" />' . "\n";
	echo apply_atomic( 'meta_template', $template );
}

/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 * @since 0.1.0
 */
function hybrid_site_title() {

	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) )
		$title = '<' . $tag . ' id="site-title"><a href="' . home_url() . '" title="' . esc_attr( $title ) . '" rel="home"><span>' . $title . '</span></a></' . $tag . '>';

	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 *
 * @since 0.1.0
 */
function hybrid_site_description() {

	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) )
		$desc = "\n\t\t\t" . '<' . $tag . ' id="site-description"><span>' . $desc . '</span></' . $tag . '>' . "\n";

	/* Display the site description and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_description', $desc );
}

/**
 * Function for setting the content width of a theme.  This does not check if a content width has been set; it 
 * simply overwrites whatever the content width is.
 *
 * @since 1.2.0
 * @global int $content_width The width for the theme's content area.
 * @param int $width Numeric value of the width to set.
 */
function hybrid_set_content_width( $width = '' ) {
	global $content_width;

	$content_width = $width;
}

/**
 * Function for getting the theme's content width.
 *
 * @since 1.2.0
 * @global int $content_width The width for the theme's content area.
 * @return int $content_width
 */
function hybrid_get_content_width() {
	global $content_width;

	return $content_width;
}

?>