<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Developers should not use 
 * these functions in their parent themes and users should not use these functions in their child themes.  
 * All deprecated functions will be removed at some point in a future release.  If your theme is using one 
 * of these, you should use the listed alternative or remove it from your theme if necessary.
 *
 * This file also maintains a list of "removed" functions.  Removed functions simply exist as function names 
 * for an added layer of protection in the off-chance that a developer failed to switch over to an 
 * alternative when the function was first deprecated.  Removed functions are periodically permanently 
 * removed from the code base.
 *
 * Functions deprecated prior to the 2.0.0 version are no longer available.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* === Deprecated Functions === */

/**
 * Returns an array of the core framework's available styles for use in themes.
 *
 * @since      1.5.0
 * @deprecated 2.1.0
 * @access     public
 * @return     array
 */
function hybrid_get_styles() {
	return apply_filters( 'hybrid_styles', array() );
}

/**
 * Adds the title to the header.
 *
 * @since      2.0.0
 * @deprecated 2.1.0
 * @access     public
 * @return     void
 */
function hybrid_doctitle() {
	?><title><?php wp_title( ':' ); ?></title>
<?php }

/**
 * Registers the framework's `admin-widgets.css` stylesheet file.  The function does not load the stylesheet.  
 * It merely registers it with WordPress.
 *
 * @since      1.2.0
 * @deprecated 2.1.0
 * @access     public
 * @return     void
 */
function hybrid_admin_register_styles() {
	_deprecated_function( __FUNCTION__, '2.1.0', null );
}

/**
 * Loads the `admin-widgets.css` file when viewing the widgets screen.
 *
 * @since      1.2.0
 * @deprecated 2.1.0
 * @access     public
 * @return     void
 */
function hybrid_admin_enqueue_styles() {
	_deprecated_function( __FUNCTION__, '2.1.0', null );
}

/**
 * Creates a settings field id attribute for use on the theme settings page.  This is a helper function for use
 * with the WordPress settings API.
 *
 * @since      1.0.0
 * @deprecated 2.1.0
 * @access     public
 * @param      string  $setting
 * @return     string
 */
function hybrid_settings_field_id( $setting ) {
	_deprecated_function( __FUNCTION__, '2.1.0', '' );
	return hybrid_get_prefix() . '_theme_settings-' . sanitize_html_class( $setting );
}

/**
 * Creates a settings field name attribute for use on the theme settings page.  This is a helper function for 
 * use with the WordPress settings API.
 *
 * @since      1.0.0
 * @deprecated 2.1.0
 * @access     public
 * @param      string  $setting
 * @return     string
 */
function hybrid_settings_field_name( $setting ) {
	_deprecated_function( __FUNCTION__, '2.1.0', '' );
	return hybrid_get_prefix() . "_theme_settings[{$setting}]";
}

/**
 * Creates a theme settings page for the theme.
 *
 * @since      2.0.0
 * @deprecated 2.1.0
 * @access     public
 */
final class Hybrid_Theme_Settings{

	public function __construct() {

		/* Deprecated in 2.1.0. */
		_deprecated_function( __CLASS__, '2.1.0', 'customize_register' );
	}
}

/**
 * Loads the Hybrid theme settings once and allows the input of the specific field the user would 
 * like to show.  Hybrid theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since  0.7.0
 * @deprecated 2.1.0
 * @access public
 * @global object  $hybrid  The global Hybrid object.
 * @param  string  $option  The specific theme setting the user wants.
 * @return mixed            Specific setting asked for.
 */
function hybrid_get_setting( $option = '' ) {
	global $hybrid;

	_deprecated_function( __FUNCTION__, '2.1.0', 'get_theme_mod' );

	/* If no specific option was requested, return false. */
	if ( !$option )
		return false;

	/* Get the default settings. */
	$defaults = hybrid_get_default_theme_settings();

	/* If the settings array hasn't been set, call get_option() to get an array of theme settings. */
	if ( !isset( $hybrid->settings ) || !is_array( $hybrid->settings ) )
		$hybrid->settings = get_option( hybrid_get_prefix() . '_theme_settings', $defaults );

	/* If the option isn't set but the default is, set the option to the default. */
	if ( !isset( $hybrid->settings[ $option ] ) && isset( $defaults[ $option ] ) )
		$hybrid->settings[ $option ] = $defaults[ $option ];

	/* If no option is found at this point, return false. */
	if ( !isset( $hybrid->settings[ $option ] ) )
		return false;

	/* If the specific option is an array, return it. */
	if ( is_array( $hybrid->settings[ $option ] ) )
		return $hybrid->settings[ $option ];

	/* Strip slashes from the setting and return. */
	else
		return wp_kses_stripslashes( $hybrid->settings[ $option ] );
}

/**
 * Sets up a default array of theme settings for use with the theme.  Theme developers should filter the 
 * "{$prefix}_default_theme_settings" hook to define any default theme settings.  WordPress does not 
 * provide a hook for default settings at this time.
 *
 * @since  1.0.0
 * @deprecated 2.1.0
 * @access public
 * @return array $settings The default theme settings.
 */
function hybrid_get_default_theme_settings() {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_theme_mods' );
	return apply_filters( hybrid_get_prefix() . '_default_theme_settings', array() );
}

/**
 * Tells WordPress to load the styles needed for the framework using the wp_enqueue_style() function.
 *
 * As of version 2.1.0, this function and the use of `add_theme_support( 'hybrid-core-styles' )` has 
 * been deprecated. Theme authors should use `wp_enqueue_style()` to enqueue one of the appropriate 
 * framework styles registered in `hybrid_register_styles()`.
 *
 * @since      1.5.0
 * @deprecated 2.1.0
 * @access     public
 * @return     void
 */
function hybrid_enqueue_styles() {

	/* Get the theme-supported stylesheets. */
	$supports = get_theme_support( 'hybrid-core-styles' );

	/* If the theme doesn't add support for any styles, return. */
	if ( !is_array( $supports[0] ) )
		return;

	/* Loop through each of the core framework styles and enqueue them if supported. */
	foreach ( $supports[0] as $style )
		wp_enqueue_style( "hybrid-{$style}" );
}

/**
 * Textarea customize control class.
 *
 * @since 1.4.0
 */
class Hybrid_Customize_Control_Textarea extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 1.4.0
	 */
	public $type = 'textarea';

	/**
	 * Displays the textarea on the customize screen.
	 *
	 * @since 1.4.0
	 */
	public function render_content() { ?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="customize-control-content">
				<textarea class="widefat" cols="45" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</div>
		</label>
	<?php }
}

/**
 * Arguments for the wp_list_comments_function() used in comments.php. Users can set up a 
 * custom comments callback function by changing $callback to the custom function.  Note that 
 * $style should remain 'ol' since this is hardcoded into the theme and is the semantically correct
 * element to use for listing comments.
 *
 * @since  0.7.0
 * @access public
 * @param  array  $args 
 * @return array
 */
function hybrid_list_comments_args( $args = array() ) {

	/* Set the default arguments for listing comments. */
	$defaults = array(
		'style'        => 'ol',
		'type'         => 'all',
		'avatar_size'  => 80,
		'callback'     => 'hybrid_comments_callback',
		'end-callback' => 'hybrid_comments_end_callback'
	);

	/* Return the arguments and allow devs to overwrite them. */
	return apply_filters( 'hybrid_list_comments_args', wp_parse_args( $args, $defaults ) );
}

/**
 * @since      0.7.0
 * @deprecated 1.3.0
 */
function hybrid_get_textdomain() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'hybrid_get_parent_textdomain' );
	return hybrid_get_parent_textdomain();
}

/* Add notice that `loop-pagination` is deprecated in Hybrid Core. */
//_deprecated_function( "add_theme_support( 'loop-pagination' )", '2.1.0', '' );

/**
 * Loop pagination function for paginating loops with multiple posts.  This should be used on archive, blog, and 
 * search pages.  It is not for singular views.
 *
 * @since      0.1.0
 * @deprecated 1.0.0
 * @access     public
 * @param      array   $args
 * @return     string
 */
function loop_pagination( $args = array() ) {

	_deprecated_function( __FUNCTION__, '2.1.0', 'the_posts_pagination()' );

	return isset( $args['echo'] ) && false === $args['echo'] ? get_the_posts_pagination( $args ) : the_posts_pagination( $args );
}

/**
 * Creates new shortcodes for use in any shortcode-ready area.
 *
 * @note       Theme Check chokes on this uncommented. Devs should never call this anyway, but for reference...
 * @since      0.8.0
 * @deprecated 2.0.4
 * @access     public
 * @return     void
 */
//function hybrid_add_shortcodes() {}

/**
 * Shortcode to display the current year.
 *
 * @since      0.6.0
 * @deprecated 2.0.4
 * @access     public
 * @return     string
 */
function hybrid_the_year_shortcode() {
	return date_i18n( 'Y' );
}

/**
 * Shortcode to display a link back to the site.
 *
 * @since      0.6.0
 * @deprecated 2.0.4
 * @access     public
 * @return     string
 */
function hybrid_site_link_shortcode() {
	return hybrid_get_site_link();
}

/**
 * Shortcode to display a link to WordPress.org.
 *
 * @since      0.6.0
 * @deprecated 2.0.4
 * @access     public
 * @return     string
 */
function hybrid_wp_link_shortcode() {
	return hybrid_get_wp_link();
}

/**
 * Shortcode to display a link to the parent theme page.
 *
 * @since      0.6.0
 * @deprecated 2.0.4
 * @access     public
 * @return     string
 */
function hybrid_theme_link_shortcode() {
	return hybrid_get_theme_link();
}

/**
 * Shortcode to display a link to the child theme's page.
 *
 * @since      0.6.0
 * @deprecated 2.0.4
 * @access     public
 * @return     string
 */
function hybrid_child_link_shortcode() {
	return hybrid_get_child_theme_link();
}

/* === Removed Functions === */

/* Functions removed in the 2.0 branch. */

function hybrid_function_removed() {}
function post_format_tools_post_has_content() {}
function post_format_tools_url_grabber() {}
function post_format_tools_get_image_attachment_count() {}
function post_format_tools_get_video() {}
function get_atomic_template() {}
function do_atomic() {}
function apply_atomic() {}
function apply_atomic_shortcode() {}
function hybrid_body_attributes() {}
function hybrid_body_class() {}
function hybrid_get_body_class() {}
function hybrid_footer_content() {}
function hybrid_post_attributes() {}
function hybrid_post_class() {}
function hybrid_entry_class() {}
function hybrid_get_post_class() {}
function hybrid_comment_attributes() {}
function hybrid_comment_class() {}
function hybrid_get_comment_class() {}
function hybrid_avatar() {}
function hybrid_document_title() {}
function hybrid_loginout_link_shortcode() {}
function hybrid_query_counter_shortcode() {}
function hybrid_nav_menu_shortcode() {}
function hybrid_entry_edit_link_shortcode() {}
function hybrid_entry_published_shortcode() {}
function hybrid_entry_comments_link_shortcode() {}
function hybrid_entry_author_shortcode() {}
function hybrid_entry_terms_shortcode() {}
function hybrid_entry_title_shortcode() {}
function hybrid_entry_shortlink_shortcode() {}
function hybrid_entry_permalink_shortcode() {}
function hybrid_post_format_link_shortcode() {}
function hybrid_comment_published_shortcode() {}
function hybrid_comment_author_shortcode() {}
function hybrid_comment_permalink_shortcode() {}
function hybrid_comment_edit_link_shortcode() {}
function hybrid_comment_reply_link_shortcode() {}
function hybrid_get_transient_expiration() {}
function hybrid_translate() {}
function hybrid_translate_plural() {}
function hybrid_gettext() {}
function hybrid_gettext_with_context() {}
function hybrid_ngettext() {}
function hybrid_ngettext_with_context() {}
function hybrid_extensions_gettext() {}
function hybrid_extensions_gettext_with_context() {}
function hybrid_extensions_ngettext() {}
function hybrid_extensions_ngettext_with_context() {}
function hybrid_register_widgets() {}
function hybrid_unregister_widgets() {}
