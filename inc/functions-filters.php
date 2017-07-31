<?php
/**
 * Filters for theme-related WordPress features.  These filters are for handling adding or modifying the
 * output of common WordPress template tags to make for a richer theme development experience without
 * having to resort to custom template tags.  Many of the filters are simply for adding HTML5 microdata.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add extra support for post types.
add_action( 'init', 'hybrid_add_post_type_support', 15 );

# Filters the archive title and description.
add_filter( 'get_the_archive_title',       'hybrid_archive_title_filter',       5  );
add_filter( 'get_the_archive_description', 'hybrid_archive_description_filter', 0  );
add_filter( 'get_the_archive_description', 'hybrid_archive_description_format', 95 );

# Use same default filters as 'the_content' with a little more flexibility.
add_filter( 'hybrid_archive_description', array( $GLOBALS['wp_embed'], 'run_shortcode' ),   5  );
add_filter( 'hybrid_archive_description', array( $GLOBALS['wp_embed'], 'autoembed'     ),   5  );
add_filter( 'hybrid_archive_description',                              'wptexturize',       10 );
add_filter( 'hybrid_archive_description',                              'convert_smilies',   15 );
add_filter( 'hybrid_archive_description',                              'convert_chars',     20 );
add_filter( 'hybrid_archive_description',                              'wpautop',           25 );
add_filter( 'hybrid_archive_description',                              'do_shortcode',      30 );
add_filter( 'hybrid_archive_description',                              'shortcode_unautop', 35 );

# Don't strip tags on single post titles.
remove_filter( 'single_post_title', 'strip_tags' );

# Filters the title for untitled posts.
add_filter( 'the_title', 'hybrid_untitled_post' );

# Default excerpt more.
add_filter( 'excerpt_more', 'hybrid_excerpt_more', 5 );

# Modifies the arguments and output of wp_link_pages().
add_filter( 'wp_link_pages_args', 'hybrid_link_pages_args', 5 );
add_filter( 'wp_link_pages_link', 'hybrid_link_pages_link', 5 );

# Filters to add microdata support to common template tags.
add_filter( 'the_author_posts_link',          'hybrid_the_author_posts_link',          5 );
add_filter( 'get_comment_author_link',        'hybrid_get_comment_author_link',        5 );
add_filter( 'get_comment_author_url_link',    'hybrid_get_comment_author_url_link',    5 );
add_filter( 'get_avatar',                     'hybrid_get_avatar',                     5 );
add_filter( 'post_thumbnail_html',            'hybrid_post_thumbnail_html',            5 );
add_filter( 'comments_popup_link_attributes', 'hybrid_comments_popup_link_attributes', 5 );

# Adds custom CSS classes to nav menu items.
add_filter( 'nav_menu_css_class', 'hybrid_nav_menu_css_class', 5, 2 );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but
 * they're not registered.
 *
 * @since 0.8.0
 * @access public
 * @return void
 */
function hybrid_add_post_type_support() {

	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', array( 'excerpt' ) );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	// Add theme layouts support to core and custom post types.
	add_post_type_support( 'post',              'theme-layouts' );
	add_post_type_support( 'page',              'theme-layouts' );
	add_post_type_support( 'attachment',        'theme-layouts' );

	add_post_type_support( 'forum',             'theme-layouts' );
	add_post_type_support( 'literature',        'theme-layouts' );
	add_post_type_support( 'portfolio_project', 'theme-layouts' );
	add_post_type_support( 'product',           'theme-layouts' );
	add_post_type_support( 'restaurant_item',   'theme-layouts' );
}

/**
 * Filters `get_the_archve_title` to add better archive titles than core.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $title
 * @return string
 */
function hybrid_archive_title_filter( $title ) {

	if ( is_home() && ! is_front_page() )
		$title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_category() )
		$title = single_cat_title( '', false );

	elseif ( is_tag() )
		$title = single_tag_title( '', false );

	elseif ( is_tax() )
		$title = single_term_title( '', false );

	elseif ( is_author() )
		$title = hybrid_get_single_author_title();

	elseif ( is_search() )
		$title = hybrid_get_search_title();

	elseif ( is_post_type_archive() )
		$title = post_type_archive_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$title = hybrid_get_single_minute_hour_title();

	elseif ( get_query_var( 'minute' ) )
		$title = hybrid_get_single_minute_title();

	elseif ( get_query_var( 'hour' ) )
		$title = hybrid_get_single_hour_title();

	elseif ( is_day() )
		$title = hybrid_get_single_day_title();

	elseif ( get_query_var( 'w' ) )
		$title = hybrid_get_single_week_title();

	elseif ( is_month() )
		$title = single_month_title( ' ', false );

	elseif ( is_year() )
		$title = hybrid_get_single_year_title();

	elseif ( is_archive() )
		$title = hybrid_get_single_archive_title();

	return apply_filters( 'hybrid_archive_title', $title );
}

/**
 * Filters `get_the_archve_description` to add better archive descriptions than core.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function hybrid_archive_description_filter( $desc ) {

	$new_desc = '';

	if ( is_home() && ! is_front_page() )
		$new_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	elseif ( is_category() )
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	elseif ( is_tag() )
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	elseif ( is_tax() )
		$new_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	elseif ( is_author() )
		$new_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	elseif ( is_post_type_archive() )
		$new_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

	return $new_desc ? $new_desc : $desc;
}

/**
 * Filters `get_the_archve_description` to add custom formatting.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function hybrid_archive_description_format( $desc ) {

	return apply_filters( 'hybrid_archive_description', $desc );
}

/**
 * The WordPress.org theme review requires that a link be provided to the single post page for untitled
 * posts.  This is a filter on 'the_title' so that an '(Untitled)' title appears in that scenario, allowing
 * for the normal method to work.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $title
 * @return string
 */
function hybrid_untitled_post( $title ) {

	// Translators: Used as a placeholder for untitled posts on non-singular views.
	if ( ! $title && ! is_singular() && in_the_loop() && ! is_admin() )
		$title = esc_html__( '(Untitled)', 'hybrid-core' );

	return $title;
}

/**
 * Filters the excerpt more output with internationalized text and a link to the post.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $text
 * @return string
 */
function hybrid_excerpt_more( $text ) {

	if ( 0 !== strpos( $text, '<a' ) )
		$text = sprintf( ' <a href="%s" class="more-link">%s</a>', esc_url( get_permalink() ), trim( $text ) );

	return $text;
}

/**
 * Wraps the output of `wp_link_pages()` with `<p class="page-links">` if it's simply wrapped in a
 * `<p>` tag.
 *
 * @since  2.0.0
 * @access public
 * @param  array  $args
 * @return array
 */
function hybrid_link_pages_args( $args ) {

	$args['before'] = str_replace( '<p>', '<p class="page-links">', $args['before'] );

	return $args;
}

/**
 * Wraps page "links" that aren't actually links (just text) with `<span class="page-numbers">` so that they
 * can also be styled.  This makes `wp_link_pages()` consistent with the output of `paginate_links()`.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_link_pages_link( $link ) {

	return 0 !== strpos( $link, '<a' ) ? "<span class='page-numbers'>{$link}</span>" : $link;
}

/**
 * Adds microdata to the author posts link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_the_author_posts_link( $link ) {

	$pattern = array(
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1 class="url fn n" itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to the comment author link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_get_comment_author_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to the comment author URL link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_get_comment_author_url_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i"
	);
	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to avatars.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $avatar
 * @return string
 */
function hybrid_get_avatar( $avatar ) {

	return preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $avatar );
}

/**
 * Adds microdata to the post thumbnail HTML.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $html
 * @return string
 */
function hybrid_post_thumbnail_html( $html ) {

	return function_exists( 'get_the_image' ) ? $html : preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $html );
}

/**
 * Adds microdata to the comments popup link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $attr
 * @return string
 */
function hybrid_comments_popup_link_attributes( $attr ) {

	return 'itemprop="discussionURL"';
}

/**
 * Adds a custom class to nav menu items that correspond to a post type archive.  The
 * `menu-item-parent-archive` class is shown when viewing a single post of that belongs
 * to the given post type.
 *
 * @since  4.0.0
 * @access public
 * @param  array   $classes
 * @param  object  $item
 * @return array
 */
function hybrid_nav_menu_css_class( $classes, $item ) {

	if ( 'post_type' === $item->type && is_singular( $item->object ) )
		$classes[] = 'menu-item-parent-archive';

	return $classes;
}
