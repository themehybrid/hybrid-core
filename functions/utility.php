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
	add_post_type_support( 'post', array( hybrid_get_prefix() . '-post-settings', 'entry-views' ) );
	add_post_type_support( 'page', array( 'excerpt', hybrid_get_prefix() . '-post-settings', 'entry-views' ) );
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

	$theme_dir = THEME_DIR . '/' . $template;
	$child_dir = CHILD_THEME_DIR . '/' . $template;

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
 * Adds the correct DOCTYPE to the theme. Defaults to XHTML 1.0 Strict.
 * Child themes can overwrite this with the hybrid_doctype filter.
 *
 * @since 0.4.0
 */
function hybrid_doctype() {
	if ( !preg_match( "/MSIE 6.0/", esc_attr( $_SERVER['HTTP_USER_AGENT'] ) ) )
		$doctype = '<' . '?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . "\n";

	$doctype .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo apply_atomic( 'doctype', $doctype );
}

/**
 * Shows the content type in the header.  Gets the site's defined HTML type 
 * and charset.  Can be overwritten with the hybrid_meta_content_type filter.
 *
 * @since 0.4.0
 */
function hybrid_meta_content_type() {
	$content_type = '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . '" />' . "\n";
	echo apply_atomic( 'meta_content_type', $content_type );
}

/**
 * Generates the relevant template info.  Adds template meta with theme version.  
 * Uses the theme name and version from style.css.  In 0.6, added the hybrid_meta_template 
 * filter hook.
 *
 * @since 0.4.0
 */
function hybrid_meta_template() {
	$data = get_theme_data( TEMPLATEPATH . '/style.css' );
	$template = '<meta name="template" content="' . esc_attr( "{$data['Title']} {$data['Version']}" ) . '" />' . "\n";
	echo apply_atomic( 'meta_template', $template );
}

/**
 * Displays the pinkback URL.
 *
 * @since 0.4.0
 */
function hybrid_head_pingback() {
	$pingback = '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	echo apply_atomic( 'head_pingback', $pingback );
}

/**
 * Dynamic element to wrap the site title in.  If it is the home or front page, wrap
 * it in an <h1> element.  One other pages, wrap it in a <div> element.  This may change
 * once the theme moves from XHTML to HTML 5 because HTML 5 allows for
 * multiple <h1> elements in a single document.
 *
 * @since 0.1.0
 */
function hybrid_site_title() {
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	if ( $title = get_bloginfo( 'name' ) )
		$title = '<' . $tag . ' id="site-title"><a href="' . home_url() . '" title="' . esc_attr( $title ) . '" rel="home"><span>' . $title . '</span></a></' . $tag . '>';

	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the home or front page,
 * wrap it in an <h2> element.  One other pages, wrap it in a <div> element.  This may
 * change once the theme moves from XHTML to HTML 5 because HTML 5 has the 
 * <hgroup> element.
 *
 * @since 0.1.0
 */
function hybrid_site_description() {
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	if ( $desc = get_bloginfo( 'description' ) )
		$desc = "\n\t\t\t" . '<' . $tag . ' id="site-description"><span>' . $desc . '</span></' . $tag . '>' . "\n";

	echo apply_atomic( 'site_description', $desc );
}

/**
 * Displays the page's profile URI.
 * @link http://microformats.org/wiki/profile-uris
 *
 * @since 0.6.0
 */
function hybrid_profile_uri() {
	echo apply_atomic( 'profile_uri', 'http://gmpg.org/xfn/11' );
}

/**
 * Displays the footer insert from the theme settings page. Users can also use 
 * shortcodes in their footer area, which will be displayed with this function.
 *
 * @since 0.2.1
 * @uses do_shortcode() Allows users to add shortcodes to their footer.
 * @uses stripslashes() Strips any slashes added from the admin form.
 * @uses hybrid_get_setting() Grabs the 'footer_insert' theme setting.
 */
function hybrid_footer_insert() {
	$footer_insert = do_shortcode( hybrid_get_setting( 'footer_insert' ) );

	if ( !empty( $footer_insert ) )
		echo '<div class="footer-insert">' . $footer_insert . '</div>';
}

?>