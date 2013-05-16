<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add extra support for post types. */
add_action( 'init', 'hybrid_add_post_type_support' );

/* Initialize the human readable time difference function. */
add_action( 'init', 'hybrid_get_time_since' );

/* Add extra file headers for themes. */
add_filter( 'extra_theme_headers', 'hybrid_extra_theme_headers' );

/**
 * This function is for adding extra support for features not default to the core post ypes.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 0.8.0
 * @access public
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
 * @access public
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
 * @access public
 * @return void
 */
function hybrid_meta_template() {
	$theme = wp_get_theme( get_template(), get_theme_root( get_template_directory() ) );
	$template = '<meta name="template" content="' . esc_attr( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) ) . '" />' . "\n";
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
 * Standardized function for outputting the footer content.
 *
 * @since 1.4.0
 * @access public
 * @return void
 */
function hybrid_footer_content() {

	/* Only run the code if the theme supports the Hybrid Core theme settings. */
	if ( current_theme_supports( 'hybrid-core-theme-settings' ) )
		echo apply_atomic_shortcode( 'footer_content', hybrid_get_setting( 'footer_insert' ) );
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

/**
 * Retrieves the file with the highest priority that exists.  The function searches both the stylesheet 
 * and template directories.  This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since 1.5.0
 * @access public
 * @link http://core.trac.wordpress.org/ticket/18302
 * @param array $file_names The files to search for.
 * @return string
 */
function hybrid_locate_theme_file( $file_names ) {

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}

/**
 * Return the difference between two timestamps in
 * a human readable format. E.g '10 minutes ago'.
 *
 * Function credit to the bbpress folks. - http://bbpress.org/
 * 
 * @since 1.5.0
 * @param  string $older_date Unix timestamp from which the difference begins.
 * @param  string $newer_date Unix timestamp to end the time difference. Defaults to false.
 * @return string             Formated time.
 */
function hybrid_get_time_since( $older_date, $newer_date = false ) {
		
	/* Setup the strings. */
	$unknown_text   = apply_filters( 'hybrid_core_time_since_unknown_text',   __( 'sometime',  'hybrid-core' ) );
	$right_now_text = apply_filters( 'hybrid_core_time_since_right_now_text', __( 'right now', 'hybrid-core' ) );
	$ago_text       = apply_filters( 'hybrid_core_time_since_ago_text',       __( '%s ago',    'hybrid-core' ) );

	/* Array of time period chunks. */
	$chunks = array(
		array( 60 * 60 * 24 * 365 , __( 'year',   'hybrid-core' ), __( 'years',   'hybrid-core' ) ),
		array( 60 * 60 * 24 * 30 ,  __( 'month',  'hybrid-core' ), __( 'months',  'hybrid-core' ) ),
		array( 60 * 60 * 24 * 7,    __( 'week',   'hybrid-core' ), __( 'weeks',   'hybrid-core' ) ),
		array( 60 * 60 * 24 ,       __( 'day',    'hybrid-core' ), __( 'days',    'hybrid-core' ) ),
		array( 60 * 60 ,            __( 'hour',   'hybrid-core' ), __( 'hours',   'hybrid-core' ) ),
		array( 60 ,                 __( 'minute', 'hybrid-core' ), __( 'minutes', 'hybrid-core' ) ),
		array( 1,                   __( 'second', 'hybrid-core' ), __( 'seconds', 'hybrid-core' ) )
	);

	if ( !empty( $older_date ) && !is_numeric( $older_date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
		$older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
	}

	/* $newer_date will equal false if we want to know the time elapsed
		 between a date and the current time. $newer_date will have a value if
		 we want to work out time elapsed between two known dates. */
	$newer_date = ( !$newer_date ) ? strtotime( current_time( 'mysql' ) ) : $newer_date;

	/* Difference in seconds. */
	$since = $newer_date - $older_date;

	/* Something went wrong with date calculation and we ended up with a negative date. */
	if ( 0 > $since ) {
		$output = $unknown_text;

	/* We only want to output two chunks of time here, eg:
	     x years
	     x months,
	     x days,
	     x hours
	 so there's only one bit of calculation below: */
	} else {

		/* Step one: the first chunk. */
		for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
			$seconds = $chunks[$i][0];

			/* Finding the biggest chunk (if the chunk fits, break). */
			$count = floor( $since / $seconds );
			if ( 0 != $count ) {
				break;
			}
		}

		/* If $i iterates all the way to $j, then the event happened 0 seconds ago. */
		if ( !isset( $chunks[$i] ) ) {
			$output = $right_now_text;

		} else {

			/* Set output var. */
			$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];

		}
	}

	/* Append 'ago' to the end of time-since if not 'right now'. */
	if ( $output != $right_now_text ) {
		$output = sprintf( $ago_text, $output );
	}

	return apply_filters( 'hybrid_get_time_since', $output, $older_date, $newer_date );
}

?>