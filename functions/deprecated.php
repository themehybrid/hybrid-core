<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Also handles removed 
 * functions to avoid errors. Developers should not use these functions in their parent themes and users 
 * should not use these functions in their child themes.  The functions below will all be removed at some 
 * point in a future release.  If your theme is using one of these, you should use the listed alternative or 
 * remove it from your theme if necessary.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Old equivalent of hybrid_entry_class().
 *
 * @since 0.2
 * @deprecated 0.5 Use hybrid_entry_class() instead.
 */
function hybrid_post_class( $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '0.5', 'hybrid_entry_class()' );
	hybrid_entry_class( $deprecated );
}

/**
 * Displays the category navigation menu.
 *
 * @deprecated 0.6 Child themes should manually add a category menu using wp_list_categories().
 * @internal This function needs to stay for the long haul (post-1.0).
 *
 * @since 0.1
 */
function hybrid_cat_nav() {
	_deprecated_function( __FUNCTION__, '0.6', 'wp_nav_menu()' );

	echo "<div id='cat-navigation'>";

	do_action( 'hybrid_before_cat_nav' );

	echo apply_filters( 'hybrid_cat_nav', hybrid_category_menu( 'echo=0' ) );

	do_action( 'hybrid_after_cat_nav' );

	echo '</div>';
}

/**
 * Menu listing for categories.
 *
 * @deprecated 0.6 Themes should add menus with the wp_nav_menu() function.
 * @internal This function needs to stay for the long haul (post-1.0).
 *
 * @since 0.2.3
 * @uses wp_list_categories() Creates a list of the site's categories
 * @link http://codex.wordpress.org/Template_Tags/wp_list_categories
 * @param array $args
 */
function hybrid_category_menu( $args = array() ) {
	_deprecated_function( __FUNCTION__, '0.6', 'wp_nav_menu()' );

	$defaults = array( 'menu_class' => 'cat-nav', 'style' => 'list', 'hide_empty' => 1, 'use_desc_for_title' => 0, 'depth' => 4, 'hierarchical' => true, 'echo' => 1 );
	$args = wp_parse_args( apply_filters( 'hybrid_category_menu_args', $args ), $defaults );
	extract( $args );

	$args['title_li'] = false;
	$args['echo'] = false;

	$menu = str_replace( array( "\t", "\n", "\r" ), '', wp_list_categories( $args ) );
	$menu = '<div id="' . $menu_class . '" class="' . $menu_class . '"><ul class="menu sf-menu">' . $menu . '</ul></div>';
	$menu = apply_filters( 'hybrid_category_menu', $menu );

	if ( $echo )
		echo $menu;
	else
		return $menu;
}

/**
 * Loads the theme search form.
 *
 * @deprecated 0.6 Users should add get_search_form() whenever needed.
 * @since 0.1
 */
function hybrid_search_form() {
	_deprecated_function( __FUNCTION__, '0.6', 'get_search_form()' );

	$search = apply_filters( 'hybrid_search_form', false );

	if ( empty( $search ) )
		get_search_form();
	else
		echo $search;
}

/**
 * After single posts but before the comments template.
 * @since 0.2
 * @deprecated 0.7
 */
function hybrid_after_single() {
	_deprecated_function( __FUNCTION__, '0.7', "apply_atomic( 'after_singular' )" );
	hybrid_after_singular();
}

/**
 * After page content but before the comments template.
 * @since 0.2
 * @deprecated 0.7
 */
function hybrid_after_page() {
	_deprecated_function( __FUNCTION__, '0.7', "apply_atomic( 'after_singular' )" );
	hybrid_after_singular();
}

/**
 * Loads the Utility: After Single widget area.
 * @since 0.4
 * @deprecated 0.7
 */
function hybrid_get_utility_after_single() {
	_deprecated_function( __FUNCTION__, '0.7', 'get_sidebar()' );
	hybrid_get_utility_after_singular();
}

/**
 * Loads the Utility: After Page widget area.
 * @since 0.4
 * @deprecated 0.7
 */
function hybrid_get_utility_after_page() {
	_deprecated_function( __FUNCTION__, '0.7', 'get_sidebar()' );
	hybrid_get_utility_after_singular();
}

/**
 * Displays the page navigation menu.
 * @since 0.1
 * @deprecated 0.8
 */
function hybrid_page_nav() {
	_deprecated_function( __FUNCTION__, '0.8', 'wp_nav_menu()' );

	echo '<div id="navigation">';

	do_atomic( 'before_page_nav' );

	$args = array( 'show_home' => __( 'Home', hybrid_get_textdomain() ), 'menu_class' => 'page-nav', 'sort_column' => 'menu_order', 'depth' => 4, 'echo' => 0 );
	$nav = str_replace( array( "\r", "\n", "\t" ), '', wp_page_menu( $args ) );

	$nav = str_replace( '<div class="', '<div id="page-nav" class="', $nav );
	echo preg_replace( '/<ul>/', '<ul class="menu sf-menu">', $nav, 1 );

	do_atomic( 'after_page_nav' );

	echo "\n\t</div><!-- #navigation -->\n";
}

/**
 * Check for widgets in widget-ready areas.
 * @since 0.2
 * @deprecated 0.6.1
 */
function is_sidebar_active( $index = 1 ) {
	_deprecated_function( __FUNCTION__, '0.6.1', 'is_active_sidebar()' );
	return is_active_sidebar( $index );
}

/**
 * Loads the comment form.
 * @since 0.7
 * @deprecated 0.8
 */
function hybrid_get_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8', 'comment_form()' );
	comment_form();
}

/**
 * Fires before the comment form.
 * @since 0.6
 * @deprecated 0.8
 */
function hybrid_before_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8' );
	do_atomic( 'before_comment_form' );
}

/**
 * Fires after the comment form.
 * @since 0.6
 * @deprecated 0.8
 */
function hybrid_after_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8' );
	do_atomic( 'after_comment_form' );
}
/**
 * Displays an individual comment author.
 * @since 0.2.2
 * @deprecated 0.8
 */
function hybrid_comment_author() {
	_deprecated_function( __FUNCTION__, '0.8', 'hybrid_comment_author_shortcode()' );
	return hybrid_comment_author_shortcode();
}

/**
 * Simply not used anymore.  But, the function name may come in handy later.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_enqueue_style() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
}

/**
 * This function creates all of the default theme settings and adds them to a single array.
 * @since 0.4
 * @deprecated 1.0.0
 */
function hybrid_theme_settings() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'hybrid_get_default_theme_settings()' );
	return apply_filters( hybrid_get_prefix() . '_settings_args', hybrid_get_default_theme_settings() );
}

/**
 * Loads the admin.css stylesheet for the theme settings page.
 * @since 0.7
 * @deprecated 1.0.0
 */
function hybrid_settings_page_enqueue_style() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'hybrid_admin_enqueue_style()' );
	hybrid_admin_enqueue_style();
}

/**
 * This function is for adding extra support for theme features to the theme.
 * @since 0.8
 * @deprecated 1.0.0
 */
function hybrid_add_theme_support() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'add_theme_support()' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'hybrid-core-theme-settings' );
}

/**
 * Per-post stylesheets.  Moved to the post-stylesheets.php extension.
 * @since 0.9
 * @deprecated 1.0.0
 */
function hybrid_post_stylesheets( $deprecated_1 = '', $deprecated_2 = '') {
	_deprecated_function( __FUNCTION__, '1.0.0', 'post_stylesheets_stylesheet_uri()' );
	return post_stylesheets_stylesheet_uri( $deprecated_1, $deprecated_2 );
}

/**
 * Adds the correct DOCTYPE to the theme.
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_doctype() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	if ( !preg_match( "/MSIE 6.0/", esc_attr( $_SERVER['HTTP_USER_AGENT'] ) ) )
		$doctype = '<' . '?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . "\n";

	$doctype .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo apply_atomic( 'doctype', $doctype );
}

/**
 * Shows the content type in the header.
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_meta_content_type() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	$content_type = '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . '" />' . "\n";
	echo apply_atomic( 'meta_content_type', $content_type );
}

/**
 * Displays the pinkback URL.
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_head_pingback() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	$pingback = '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	echo apply_atomic( 'head_pingback', $pingback );
}

/**
 * Displays the page's profile URI.
 * @since 0.6.0
 * @deprecated 1.0.0
 */
function hybrid_profile_uri() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	echo apply_atomic( 'profile_uri', 'http://gmpg.org/xfn/11' );
}

/**
 * Before HTML.
 * @since 0.3.2
 * @deprecated 1.0.0
 */
function hybrid_before_html() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_html' )" );
	do_atomic( 'before_html' );
}

/**
 * After HTML.
 * @since 0.3.2
 * @deprecated 1.0.0
 */
function hybrid_after_html() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_html' )" );
	do_atomic( 'after_html' );
}

/**
 * Added to the header before wp_head().
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_head() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'wp_head' );
	do_atomic( 'head' );
}

/**
 * Before the header.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_before_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_header' )" );
	do_atomic( 'before_header' );
}

/**
 * Header.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'header' )" );
	do_atomic( 'header' );
}

/**
 * After the header.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_after_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_header' )" );
	do_atomic( 'after_header' );
}

/**
 * Before primary menu.
 * @since 0.8
 * @deprecated 1.0.0
 */
function hybrid_before_primary_menu() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_primary_menu' )" );
	do_atomic( 'before_primary_menu' );
}

/**
 * After primary menu.
 * @since 0.8
 * @deprecated 1.0.0
 */
function hybrid_after_primary_menu() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_primary_menu' )" );
	do_atomic( 'after_primary_menu' );
}

/**
 * Before the container.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_before_container() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_container' )" );
	do_atomic( 'before_container' );
}

/**
 * Before the content.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_before_content() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_content' )" );
	do_atomic( 'before_content' );
}

/**
 * After the content.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_after_content() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_content' )" );
	do_atomic( 'after_content' );
}

/**
 * Before each entry.
 * @since 0.5
 * @deprecated 1.0.0
 */
function hybrid_before_entry() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_entry' )" );
	do_atomic( 'before_entry' );
}

/**
 * After each entry.
 * @since 0.5
 * @deprecated 1.0.0
 */
function hybrid_after_entry() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_entry' )" );
	do_atomic( 'after_entry' );
}

/**
 * After singular views but before the comments template.
 * @since 0.7
 * @deprecated 1.0.0
 */
function hybrid_after_singular() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_singular' )" );

	if ( is_singular( 'post' ) && !is_attachment() )
		do_action( 'hybrid_after_single' );
	elseif ( is_page() )
		do_action( 'hybrid_after_page' );

	do_atomic( 'after_singular' );
}

/**
 * Before the primary widget area content.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_before_primary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_primary' )" );
	do_atomic( 'before_primary' );
}

/**
 * After the primary widget area content.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_after_primary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_primary' )" );
	do_atomic( 'after_primary' );
}

/**
 * Before the secondary widget area.
 * @since 0.2
 * @deprecated 1.0.0
 */
function hybrid_before_secondary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_secondary' )" );
	do_atomic( 'before_secondary' );
}

/**
 * After the secondary widget area.
 * @since 0.2
 * @deprecated 1.0.0
 */
function hybrid_after_secondary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_secondary' )" );
	do_atomic( 'after_secondary' );
}

/**
 * Before the subsidiary widget area.
 * @since 0.3.1
 * @deprecated 1.0.0
 */
function hybrid_before_subsidiary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_subsidiary' )" );
	do_atomic( 'before_subsidiary' );
}

/**
 * After the subsidiary widget area.
 * @since 0.3.1
 * @deprecated 1.0.0
 */
function hybrid_after_subsidiary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_subsidiary' )" );
	do_atomic( 'after_subsidiary' );
}

/**
 * After the container area.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_after_container() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_container' )" );
	do_atomic( 'after_container' );
}

/**
 * Before the footer.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_before_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_footer' )" );
	do_atomic( 'before_footer' );
}

/**
 * The footer.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'footer' )" );
	do_atomic( 'footer' );
}

/**
 * After the footer.
 * @since 0.1
 * @deprecated 1.0.0
 */
function hybrid_after_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_footer' )" );
	do_atomic( 'after_footer' );
}

/**
 * Fires before each comment's information.
 * @since 0.5
 * @deprecated 1.0.0
 */
function hybrid_before_comment() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_comment' )" );
	do_atomic( 'before_comment' );
}

/**
 * Fires after each comment's information.
 * @since 0.5
 * @deprecated 1.0.0
 */
function hybrid_after_comment() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_comment' )" );
	do_atomic( 'after_comment' );
}

/**
 * Fires before the comment list.
 * @since 0.6
 * @deprecated 1.0.0
 */
function hybrid_before_comment_list() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_comment_list' )" );
	do_atomic( 'before_comment_list' );
}

/**
 * Fires after the comment list.
 * @since 0.6
 * @deprecated 1.0.0
 */
function hybrid_after_comment_list() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_comment_list' )" );
	do_atomic( 'after_comment_list' );
}

/* @deprecated 1.0.0. Backwards compatibility with old theme settings. */
add_action( 'check_admin_referer', 'hybrid_back_compat_update_settings' );

/**
 * Backwards compatibility function for updating child theme settings.  Do not use this function or the 
 * available hook in development.
 *
 * @since 1.0.0
 */
function hybrid_back_compat_update_settings( $action ) {
	$prefix = hybrid_get_prefix();

	if ( "{$prefix}_theme_settings-options" == $action )
		do_action( "{$prefix}_update_settings_page" );
}

/* === Removed Functions === */

/* Functions removed in the 0.5 branch. */

function hybrid_all_tags() {
	hybrid_function_removed( 'hybrid_all_tags' );
}

function hybrid_get_users() {
	hybrid_function_removed( 'hybrid_get_users' );
}

function hybrid_footnote() {
	hybrid_function_removed( 'hybrid_footnote' );
}

function hybrid_related_posts() {
	hybrid_function_removed( 'hybrid_related_posts' );
}

function hybrid_insert() {
	hybrid_function_removed( 'hybrid_insert' );
}

/* Functions removed in the 0.6 branch. */

function hybrid_get_authors() {
	hybrid_function_removed( 'hybrid_get_authors' );
}

function hybrid_credit() {
	hybrid_function_removed( 'hybrid_credit' );
}

function hybrid_query_counter() {
	hybrid_function_removed( 'hybrid_query_counter' );
}

function hybrid_copyright() {
	hybrid_function_removed( 'hybrid_copyright' );
}

function hybrid_series() {
	hybrid_function_removed( 'hybrid_series' );
}

/* Functions removed in the 0.7 branch. */

function hybrid_all_cats() {
	hybrid_function_removed( 'hybrid_all_cats' );
}

function hybrid_all_cat_slugs() {
	hybrid_function_removed( 'hybrid_all_cat_slugs' );
}

function hybrid_all_tag_slugs() {
	hybrid_function_removed( 'hybrid_all_tag_slugs' );
}

function hybrid_mime_type_icon() {
	hybrid_function_removed( 'hybrid_mime_type_icon' );
}

function hybrid_attachment_icon() {
	hybrid_function_removed( 'hybrid_attachment_icon' );
}

function hybrid_widow() {
	hybrid_function_removed( 'hybrid_widow' );
}

function hybrid_dash() {
	hybrid_function_removed( 'hybrid_dash' );
}

function hybrid_text_filter() {
	hybrid_function_removed( 'hybrid_text_filter' );
}

function hybrid_allowed_tags() {
	hybrid_function_removed( 'hybrid_allowed_tags' );
}

function hybrid_typography() {
	hybrid_function_removed( 'hybrid_typography' );
}

function hybrid_before_cat_nav() {
	hybrid_function_removed( 'hybrid_before_cat_nav' );
}

function hybrid_after_cat_nav() {
	hybrid_function_removed( 'hybrid_after_cat_nav' );
}

function hybrid_first_paragraph() {
	hybrid_function_removed( 'hybrid_first_paragraph' );
}

function hybrid_category_term_link() {
	hybrid_function_removed( 'hybrid_category_term_link' );
}

function hybrid_post_tag_term_link() {
	hybrid_function_removed( 'hybrid_post_tag_term_link' );
}

function hybrid_search_highlight() {
	hybrid_function_removed( 'hybrid_search_highlight' );
}

function hybrid_primary_inserts() {
	hybrid_function_removed( 'hybrid_primary_inserts' );
}

function hybrid_secondary_inserts() {
	hybrid_function_removed( 'hybrid_secondary_inserts' );
}

function hybrid_subsidiary_inserts() {
	hybrid_function_removed( 'hybrid_subsidiary_inserts' );
}

function hybrid_utility_inserts() {
	hybrid_function_removed( 'hybrid_utility_inserts' );
}

function hybrid_widget_init() {
	hybrid_function_removed( 'hybrid_widget_init' );
}

function hybrid_primary_var() {
	hybrid_function_removed( 'hybrid_primary_var' );
}

function hybrid_secondary_var() {
	hybrid_function_removed( 'hybrid_secondary_var' );
}

function hybrid_subsidiary_var() {
	hybrid_function_removed( 'hybrid_subsidiary_var' );
}

function hybrid_legacy_comments() {
	hybrid_function_removed( 'hybrid_legacy_comments' );
}

function hybrid_head_feeds() {
	hybrid_function_removed( 'hybrid_head_feeds' );
}

function hybrid_legacy_functions() {
	hybrid_function_removed( 'hybrid_legacy_functions' );
}

function hybrid_capability_check() {
	hybrid_function_removed( 'hybrid_capability_check' );
}

function hybrid_template_in_use() {
	hybrid_function_removed( 'hybrid_template_in_use' );
}

function hybrid_get_utility_404() {
	hybrid_function_removed( 'hybrid_get_utility_404' );
}

function hybrid_before_comments() {
	hybrid_function_removed( 'hybrid_before_comments' );
}

function hybrid_meta_abstract() {
	hybrid_function_removed( 'hybrid_meta_abstract' );
}

function hybrid_child_settings() {
	hybrid_function_removed( 'hybrid_child_settings' );
}

function hybrid_post_meta_boxes() {
	hybrid_function_removed( 'hybrid_post_meta_boxes' );
}

function hybrid_page_meta_boxes() {
	hybrid_function_removed( 'hybrid_page_meta_boxes' );
}

function post_meta_boxes() {
	hybrid_function_removed( 'post_meta_boxes' );
}

function page_meta_boxes() {
	hybrid_function_removed( 'page_meta_boxes' );
}

function hybrid_create_meta_box() {
	hybrid_function_removed( 'hybrid_create_meta_box' );
}

function hybrid_save_meta_data() {
	hybrid_function_removed( 'hybrid_save_meta_data' );
}

function get_meta_text_input() {
	hybrid_function_removed( 'get_meta_text_input' );
}

function get_meta_select() {
	hybrid_function_removed( 'get_meta_select' );
}

function get_meta_textarea() {
	hybrid_function_removed( 'get_meta_textarea' );
}

function hybrid_error() {
	hybrid_function_removed( 'hybrid_error' );
}

function hybrid_head_canonical() {
	hybrid_function_removed( 'hybrid_head_canonical' );
}

function hybrid_disable_pagenavi_style() {
	hybrid_function_removed( 'hybrid_disable_pagenavi_style' );
}

function hybrid_comments_feed() {
	hybrid_function_removed( 'hybrid_comments_feed' );
}

function hybrid_before_page_nav() {
	hybrid_function_removed( 'hybrid_before_page_nav' );
}

function hybrid_after_page_nav() {
	hybrid_function_removed( 'hybrid_after_page_nav' );
}

function hybrid_comment_published_link_shortcode() {
	hybrid_function_removed( 'hybrid_comment_published_link_shortcode' );
}

/* Functions removed in the 0.8 branch. */

function hybrid_content_wrapper() {
	hybrid_function_removed( 'hybrid_content_wrapper' );
}

function hybrid_handle_attachment() {
	hybrid_function_removed( 'hybrid_handle_attachment' );
}

function hybrid_widget_class() {
	hybrid_function_removed( 'hybrid_widget_class' );
}

function hybrid_before_ping_list() {
	hybrid_function_removed( 'hybrid_before_ping_list' );
}

function hybrid_after_ping_list() {
	hybrid_function_removed( 'hybrid_after_ping_list' );
}

function hybrid_pings_callback() {
	hybrid_function_removed( 'hybrid_pings_callback' );
}

function hybrid_pings_end_callback() {
	hybrid_function_removed( 'hybrid_pings_end_callback' );
}

/** c
 * Message to display for removed functions.
 * @since 0.5
 */
function hybrid_function_removed( $func = '' ) {
	die( sprintf( __( '<code>%1$s</code> &mdash; This function has been removed or replaced by another function.', hybrid_get_textdomain() ), $func ) );
}

?>