<?php
/**
 * The core functions file for the Hybrid framework. Functions defined here are generally
 * used across the entire framework to make various tasks faster. This file should be loaded
 * prior to any other files because its functions are needed to run the framework.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Defines the theme prefix. This allows developers to infinitely change the theme. In theory,
 * one could use the Hybrid core to create their own theme or filter 'hybrid_prefix' with a 
 * plugin to make it easier to use hooks across multiple themes without having to figure out
 * each theme's hooks (assuming other themes used the same system).
 *
 * @since 0.7.0
 * @uses get_template() Defines the theme prefix based on the theme directory.
 * @global object $hybrid The global Hybrid object.
 * @return string $hybrid->prefix The prefix of the theme.
 */
function hybrid_get_prefix() {
	global $hybrid;

	/* If the global prefix isn't set, define it. Plugin/theme authors may also define a custom prefix. */
	if ( empty( $hybrid->prefix ) )
		$hybrid->prefix = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );

	return $hybrid->prefix;
}

/**
 * Defines the theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * parent theme. Theme developers building from the framework should use this function in their templates 
 * to easily define the correct textdomain.
 *
 * @since 0.7.0
 * @uses get_template() Defines the theme textdomain based on the template directory.
 * @global object $hybrid The global Hybrid object.
 * @return string $hybrid->textdomain The textdomain of the theme.
 */
function hybrid_get_textdomain() {
	global $hybrid;

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $hybrid->textdomain ) )
		$hybrid->textdomain = sanitize_key( apply_filters( hybrid_get_prefix() . '_textdomain', get_template() ) );

	return $hybrid->textdomain;
}

/**
 * Returns the textdomain for the child theme.
 *
 * @since 1.2.0
 * @uses get_stylesheet() Defines the child theme textdomain based on the stylesheet directory.
 * @global object $hybrid The global Hybrid object.
 * @return string $hybrid->child_theme_textdomain The textdomain of the child theme.
 */
function hybrid_get_child_textdomain() {
	global $hybrid;

	/* If a child theme isn't active, return an empty string. */
	if ( !is_child_theme() )
		return '';

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $hybrid->child_textdomain ) )
		$hybrid->child_textdomain = sanitize_key( apply_filters( hybrid_get_prefix() . '_child_textdomain', get_stylesheet() ) );

	return $hybrid->child_textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 0.9.0
 * @param string $mofile File name of the .mo file.
 * @param string $domain The textdomain currently being filtered.
 */
function hybrid_load_textdomain( $mofile, $domain ) {

	/* If the $domain is for the parent or child theme, search for a $domain-$locale.mo file. */
	if ( $domain == hybrid_get_textdomain() || $domain == hybrid_get_child_textdomain() ) {

		/* Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder. */
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$domain}-{$locale}.mo", "{$domain}-{$locale}.mo" ) );

		/* If a mofile was found based on the given format, set $mofile to that file name. */
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}

	/* Return the $mofile string. */
	return $mofile;
}

/**
 * Adds contextual action hooks to the theme.  This allows users to easily add context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'hybrid_header'.  The do_atomic() function extends that to 
 * give extra hooks such as 'hybrid_singular_header', 'hybrid_singular-post_header', and 
 * 'hybrid_singular-post-ID_header'.
 *
 * @since 0.7.0
 * @uses hybrid_get_prefix() Gets the theme prefix.
 * @uses hybrid_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 */
function do_atomic( $tag = '', $arg = '' ) {
	if ( empty( $tag ) )
		return false;

	/* Get the theme prefix. */
	$pre = hybrid_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	/* Do actions on the basic hook. */
	do_action_ref_array( "{$pre}_{$tag}", $args );

	/* Loop through context array and fire actions on a contextual scale. */
	foreach ( (array)hybrid_get_context() as $context )
		do_action_ref_array( "{$pre}_{$context}_{$tag}", $args );
}

/**
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'hybrid_entry_meta'.  The apply_atomic() function extends 
 * that to give extra hooks such as 'hybrid_singular_entry_meta', 'hybrid_singular-post_entry_meta', 
 * and 'hybrid_singular-post-ID_entry_meta'.
 *
 * @since 0.7.0
 * @uses hybrid_get_prefix() Gets the theme prefix.
 * @uses hybrid_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value on which the filters hooked to $tag are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to $tag.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic( $tag = '', $value = '' ) {
	if ( empty( $tag ) )
		return false;

	/* Get theme prefix. */
	$pre = hybrid_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	/* Apply filters on the basic hook. */
	$value = $args[0] = apply_filters_ref_array( "{$pre}_{$tag}", $args );

	/* Loop through context array and apply filters on a contextual scale. */
	foreach ( (array)hybrid_get_context() as $context )
		$value = $args[0] = apply_filters_ref_array( "{$pre}_{$context}_{$tag}", $args );

	/* Return the final value once all filters have been applied. */
	return $value;
}

/**
 * Wraps the output of apply_atomic() in a call to do_shortcode(). This allows developers to use 
 * context-aware functionality alongside shortcodes. Rather than adding a lot of code to the 
 * function itself, developers can create individual functions to handle shortcodes.
 *
 * @since 0.7.0
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value to be filtered.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic_shortcode( $tag = '', $value = '' ) {
	return do_shortcode( apply_atomic( $tag, $value ) );
}

/**
 * Loads the Hybrid theme settings once and allows the input of the specific field the user would 
 * like to show.  Hybrid theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since 0.7.0
 * @uses get_option() Gets an option from the database.
 * @uses hybrid_get_prefix() Gets the prefix of the theme.
 * @global object $hybrid The global Hybrid object.
 * @global array $hybrid_settings Deprecated. Developers should use hybrid_get_setting().
 * @param string $option The specific theme setting the user wants.
 * @return string|int|array $settings[$option] Specific setting asked for.
 */
function hybrid_get_setting( $option = '' ) {
	global $hybrid, $hybrid_settings;

	/* If no specific option was requested, return false. */
	if ( !$option )
		return false;

	/* If the settings array hasn't been set, call get_option() to get an array of theme settings. */
	if ( !isset( $hybrid->settings ) )
		$hybrid->settings = $hybrid_settings = get_option( hybrid_get_prefix() . '_theme_settings', hybrid_get_default_theme_settings() );

	/* If the settings isn't an array or the specific option isn't in the array, return false. */
	if ( !is_array( $hybrid->settings ) || empty( $hybrid->settings[$option] ) )
		return false;

	/* If the specific option is an array, return it. */
	if ( is_array( $hybrid->settings[$option] ) )
		return $hybrid->settings[$option];

	/* Strip slashes from the setting and return. */
	else
		return wp_kses_stripslashes( $hybrid->settings[$option] );
}

/**
 * Creates an empty array of the default theme settings.  If the theme adds support for the 
 * 'hybrid-core-meta-box-footer' feature, it'll automatically add that setting to the $settings array.
 *
 * @since 1.0.0
 */
function hybrid_get_default_theme_settings() {

	/* Set up some default variables. */
	$settings = array();
	$domain = hybrid_get_textdomain();
	$prefix = hybrid_get_prefix();

	/* Get theme-supported meta boxes for the settings page. */
	$supports = get_theme_support( 'hybrid-core-theme-settings' );

	/* If the current theme supports the footer meta box and shortcodes, add default footer settings. */
	if ( is_array( $supports[0] ) && in_array( 'footer', $supports[0] ) && current_theme_supports( 'hybrid-core-shortcodes' ) ) {

		/* If there is a child theme active, add the [child-link] shortcode to the $footer_insert. */
		if ( is_child_theme() )
			$settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', $domain ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link], [theme-link], and [child-link].', $domain ) . '</p>';

		/* If no child theme is active, leave out the [child-link] shortcode. */
		else
			$settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', $domain ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link] and [theme-link].', $domain ) . '</p>';
	}

	/* Backwards compatibility hook. @deprecated 1.0.0. */
	$settings = apply_filters( "{$prefix}_settings_args", $settings );

	/* Return the $settings array and provide a hook for overwriting the default settings. */
	return apply_filters( "{$prefix}_default_theme_settings", $settings );
}

/**
 * The theme can save multiple things in a transient to help speed up page load times. We're
 * setting a default of 12 hours or 43,200 seconds (60 * 60 * 12).
 *
 * @since 0.8.0
 * @return int Transient expiration time in seconds.
 */
function hybrid_get_transient_expiration() {
	return apply_filters( hybrid_get_prefix() . '_transient_expiration', 43200 );
}

/**
 * Function for formatting a hook name if needed. It automatically adds the theme's prefix to 
 * the hook, and it will add a context (or any variable) if it's given.
 *
 * @since 0.7.0
 * @param string $tag The basic name of the hook (e.g., 'before_header').
 * @param string $context A specific context/value to be added to the hook.
 */
function hybrid_format_hook( $tag, $context = '' ) {
	return hybrid_get_prefix() . ( ( !empty( $context ) ) ? "_{$context}" : "" ). "_{$tag}";
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

	$content_width = absint( $width );
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

/**
 * Gets theme data and stores it in the global $hybrid variable.  By storing it, it can be accessed quickly without 
 * having to run through the get_theme_data() function again.
 *
 * @since 1.2.0
 * @param string $path Whether to use the template (parent theme) or stylesheet (child theme) path.
 */
function hybrid_get_theme_data( $path = 'template' ) {
	global $hybrid;

	/* If 'template' is requested, get the parent theme data. */
	if ( 'template' == $path ) {

		/* If the parent theme data isn't set, grab it with the get_theme_data() function. */
		if ( empty( $hybrid->theme_data ) )
			$hybrid->theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );

		/* Return the parent theme data. */
		return $hybrid->theme_data;
	}

	/* If 'stylesheet' is requested, get the child theme data. */
	elseif ( 'stylesheet' == $path ) {

		/* If the child theme data isn't set, grab it with the get_theme_data() function. */
		if ( empty( $hybrid->child_theme_data ) )
			$hybrid->child_theme_data = get_theme_data( trailingslashit( STYLESHEETPATH ) . 'style.css' );

		/* Return the child theme data. */
		return $hybrid->child_theme_data;
	}

	/* Return false for everything else. */
	return false;
}

?>