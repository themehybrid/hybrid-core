<?php
/**
 * HTML attribute functions and filters.  The purposes of this is to provide a way for theme/plugin devs
 * to hook into the attributes for specific HTML elements and create new or modify existing attributes.
 * This is sort of like `body_class()`, `post_class()`, and `comment_class()` on steroids.  Plus, it
 * handles attributes for many more elements.  The biggest benefit of using this is to provide richer
 * microdata while being forward compatible with the ever-changing Web.  Currently, the default microdata
 * vocabulary supported is Schema.org.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Attributes for major structural elements.
add_filter( 'hybrid_attr_body',    'hybrid_attr_body',    5    );
add_filter( 'hybrid_attr_header',  'hybrid_attr_header',  5    );
add_filter( 'hybrid_attr_footer',  'hybrid_attr_footer',  5    );
add_filter( 'hybrid_attr_content', 'hybrid_attr_content', 5    );
add_filter( 'hybrid_attr_sidebar', 'hybrid_attr_sidebar', 5, 2 );
add_filter( 'hybrid_attr_menu',    'hybrid_attr_menu',    5, 2 );

# Header attributes.
add_filter( 'hybrid_attr_head',             'hybrid_attr_head',             5 );
add_filter( 'hybrid_attr_branding',         'hybrid_attr_branding',         5 );
add_filter( 'hybrid_attr_site-title',       'hybrid_attr_site_title',       5 );
add_filter( 'hybrid_attr_site-description', 'hybrid_attr_site_description', 5 );

# Archive page header attributes.
add_filter( 'hybrid_attr_archive-header',      'hybrid_attr_archive_header',      5 );
add_filter( 'hybrid_attr_archive-title',       'hybrid_attr_archive_title',       5 );
add_filter( 'hybrid_attr_archive-description', 'hybrid_attr_archive_description', 5 );

# Post-specific attributes.
add_filter( 'hybrid_attr_post',            'hybrid_attr_post',            5    );
add_filter( 'hybrid_attr_entry',           'hybrid_attr_post',            5    ); // Alternate for "post".
add_filter( 'hybrid_attr_entry-title',     'hybrid_attr_entry_title',     5    );
add_filter( 'hybrid_attr_entry-author',    'hybrid_attr_entry_author',    5    );
add_filter( 'hybrid_attr_entry-published', 'hybrid_attr_entry_published', 5    );
add_filter( 'hybrid_attr_entry-content',   'hybrid_attr_entry_content',   5    );
add_filter( 'hybrid_attr_entry-summary',   'hybrid_attr_entry_summary',   5    );
add_filter( 'hybrid_attr_entry-terms',     'hybrid_attr_entry_terms',     5, 2 );

# Comment specific attributes.
add_filter( 'hybrid_attr_comment',           'hybrid_attr_comment',           5 );
add_filter( 'hybrid_attr_comment-author',    'hybrid_attr_comment_author',    5 );
add_filter( 'hybrid_attr_comment-published', 'hybrid_attr_comment_published', 5 );
add_filter( 'hybrid_attr_comment-permalink', 'hybrid_attr_comment_permalink', 5 );
add_filter( 'hybrid_attr_comment-content',   'hybrid_attr_comment_content',   5 );

/**
 * Outputs an HTML element's attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $slug     The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context  A specific context (e.g., 'primary').
 * @param  array   $attr     Array of attributes to pass in (overwrites filters).
 * @return void
 */
function hybrid_attr( $slug, $context = '', $attr = array()  ) {
	echo hybrid_get_attr( $slug, $context, $attr );
}

/**
 * Gets an HTML element's attributes.  This function is actually meant to be filtered by theme authors, plugins,
 * or advanced child theme users.  The purpose is to allow folks to modify, remove, or add any attributes they
 * want without having to edit every template file in the theme.  So, one could support microformats instead
 * of microdata, if desired.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $slug     The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context  A specific context (e.g., 'primary').
 * @param  array   $attr     Array of attributes to pass in (overwrites filters).
 * @return string
 */
function hybrid_get_attr( $slug, $context = '', $attr = array() ) {

	$out    = '';
	$attr   = wp_parse_args( $attr, apply_filters( "hybrid_attr_{$slug}", array(), $context ) );

	if ( empty( $attr ) )
		$attr['class'] = $slug;

	foreach ( $attr as $name => $value )
		$out .= $value ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );

	return trim( $out );
}

/* === Structural === */

/**
 * <body> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_body( $attr ) {

	$attr['class']     = join( ' ', get_body_class() );
	$attr['dir']       = is_rtl() ? 'rtl' : 'ltr';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPage';

	if ( is_singular( 'post' ) || is_home() || is_archive() )
		$attr['itemtype'] = 'http://schema.org/Blog';

	elseif ( is_search() )
		$attr['itemtype'] = 'http://schema.org/SearchResultsPage';

	return $attr;
}

/**
 * Page <header> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_header( $attr ) {

	$attr['id']        = 'header';
	$attr['class']     = 'site-header';
	$attr['role']      = 'banner';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPHeader';

	return $attr;
}

/**
 * Page <footer> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_footer( $attr ) {

	$attr['id']        = 'footer';
	$attr['class']     = 'site-footer';
	$attr['role']      = 'contentinfo';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPFooter';

	return $attr;
}

/**
 * Main content container of the page attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_content( $attr ) {

	$attr['id']       = 'content';
	$attr['class']    = 'content';
	$attr['role']     = 'main';

	if ( ! is_singular( 'post' ) && ! is_home() && ! is_archive() )
		$attr['itemprop'] = 'mainContentOfPage';

	return $attr;
}

/**
 * Sidebar attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_sidebar( $attr, $context ) {

	$attr['class'] = 'sidebar';
	$attr['role']  = 'complementary';

	if ( $context ) {

		$attr['class'] .= " sidebar-{$context}";
		$attr['id']     = "sidebar-{$context}";

		$sidebar_name = hybrid_get_sidebar_name( $context );

		if ( $sidebar_name ) {
			// Translators: The %s is the sidebar name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Sidebar', 'sidebar aria label', 'hybrid-core' ), $sidebar_name ) );
		}
	}

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPSideBar';

	return $attr;
}

/**
 * Nav menu attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_menu( $attr, $context ) {

	$attr['class'] = 'menu';
	$attr['role']  = 'navigation';

	if ( $context ) {

		$attr['class'] .= " menu-{$context}";
		$attr['id']     = "menu-{$context}";

		$menu_name = hybrid_get_menu_location_name( $context );

		if ( $menu_name ) {
			// Translators: The %s is the menu name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Menu', 'nav menu aria label', 'hybrid-core' ), $menu_name ) );
		}
	}

	$attr['itemscope']  = 'itemscope';
	$attr['itemtype']   = 'http://schema.org/SiteNavigationElement';

	return $attr;
}

/* === header === */

/**
 * <head> attributes.
 *
 * @since  3.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_head( $attr ) {

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebSite';

	return $attr;
}

/**
 * Branding (usually a wrapper for title and tagline) attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_branding( $attr ) {

	$attr['id']    = 'branding';
	$attr['class'] = 'site-branding';

	return $attr;
}

/**
 * Site title attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_site_title( $attr ) {

	$attr['id']       = 'site-title';
	$attr['class']    = 'site-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Site description attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_site_description( $attr ) {

	$attr['id']       = 'site-description';
	$attr['class']    = 'site-description';
	$attr['itemprop'] = 'description';

	return $attr;
}

/* === loop === */

/**
 * Archive header attributes.
 *
 * @since  3.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_archive_header( $attr ) {

	$attr['class']     = 'archive-header';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPageElement';

	return $attr;
}

/**
 * Archive title attributes.
 *
 * @since  3.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_archive_title( $attr ) {

	$attr['class']     = 'archive-title';
	$attr['itemprop']  = 'headline';

	return $attr;
}

/**
 * Archive description attributes.
 *
 * @since  3.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_archive_description( $attr ) {

	$attr['class']     = 'archive-description';
	$attr['itemprop']  = 'text';

	return $attr;
}

/* === posts === */

/**
 * Post <article> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_post( $attr ) {

	$post = get_post();

	// Make sure we have a real post first.
	if ( ! empty( $post ) ) {

		$attr['id']        = 'post-' . get_the_ID();
		$attr['class']     = join( ' ', get_post_class() );
		$attr['itemscope'] = 'itemscope';

		if ( 'post' === get_post_type() ) {

			$attr['itemtype']  = 'http://schema.org/BlogPosting';

			/* Add itemprop if within the main query. */
			if ( is_main_query() && ! is_search() )
				$attr['itemprop'] = 'blogPost';
		}

		elseif ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {

			$attr['itemtype'] = 'http://schema.org/ImageObject';
		}

		elseif ( 'attachment' === get_post_type() && hybrid_attachment_is_audio() ) {

			$attr['itemtype'] = 'http://schema.org/AudioObject';
		}

		elseif ( 'attachment' === get_post_type() && hybrid_attachment_is_video() ) {

			$attr['itemtype'] = 'http://schema.org/VideoObject';
		}

		else {
			$attr['itemtype']  = 'http://schema.org/CreativeWork';
		}

	} else {

		$attr['id']    = 'post-0';
		$attr['class'] = join( ' ', get_post_class() );
	}

	return $attr;
}

/**
 * Post title attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_entry_title( $attr ) {

	$attr['class']    = 'entry-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Post author attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_entry_author( $attr ) {

	$attr['class']     = 'entry-author';
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Post time/published attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_entry_published( $attr ) {

	$attr['class']    = 'entry-published updated';
	$attr['datetime'] = get_the_time( 'Y-m-d\TH:i:sP' );
	$attr['itemprop'] = 'datePublished';

	// Translators: Post date/time "title" attribute.
	$attr['title']    = get_the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'hybrid-core' ) );

	return $attr;
}

/**
 * Post content (not excerpt) attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_entry_content( $attr ) {

	$attr['class'] = 'entry-content';

	if ( 'post' === get_post_type() )
		$attr['itemprop'] = 'articleBody';
	else
		$attr['itemprop'] = 'text';

	return $attr;
}

/**
 * Post summary/excerpt attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_entry_summary( $attr ) {

	$attr['class']    = 'entry-summary';
	$attr['itemprop'] = 'description';

	return $attr;
}

/**
 * Post terms (tags, categories, etc.) attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function hybrid_attr_entry_terms( $attr, $context ) {

	if ( !empty( $context ) ) {

		$attr['class'] = 'entry-terms ' . sanitize_html_class( $context );

		if ( 'category' === $context )
			$attr['itemprop'] = 'articleSection';

		else if ( 'post_tag' === $context )
			$attr['itemprop'] = 'keywords';
	}

	return $attr;
}


/* === Comment elements === */


/**
 * Comment wrapper attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment( $attr ) {

	$attr['id']    = 'comment-' . get_comment_ID();
	$attr['class'] = join( ' ', get_comment_class() );

	if ( in_array( get_comment_type(), array( '', 'comment' ) ) ) {

		$attr['itemprop']  = 'comment';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Comment';
	}

	return $attr;
}

/**
 * Comment author attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment_author( $attr ) {

	$attr['class']     = 'comment-author';
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Comment time/published attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment_published( $attr ) {

	$attr['class']    = 'comment-published';
	$attr['datetime'] = get_comment_time( 'Y-m-d\TH:i:sP' );

	// Translators: Comment date/time "title" attribute.
	$attr['title']    = get_comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'hybrid-core' ) );
	$attr['itemprop'] = 'datePublished';

	return $attr;
}

/**
 * Comment permalink attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment_permalink( $attr ) {

	$attr['class']    = 'comment-permalink';
	$attr['href']     = get_comment_link();
	$attr['itemprop'] = 'url';

	return $attr;
}

/**
 * Comment content/text attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment_content( $attr ) {

	$attr['class']    = 'comment-content';
	$attr['itemprop'] = 'text';

	return $attr;
}
