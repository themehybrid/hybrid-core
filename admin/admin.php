<?php
/**
 * Theme administration functions used with other components of the framework admin.  This file is for 
 * setting up any basic features and holding additional admin helper functions.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Add the admin init function to the 'admin_init' hook. */
add_action( 'admin_init', 'hybrid_admin_init' );

/**
 * Initializes any admin-related features needed for the framework.
 *
 * @since 0.7.0
 */
function hybrid_admin_init() {

	/* Load the admin stylesheet for the widgets screen. */
	if ( current_theme_supports( 'hybrid-core-widgets' ) )
		add_action( 'load-widgets.php', 'hybrid_admin_enqueue_style' );

	/* Load the admin stylesheet for the post editor screen. */
	if ( current_theme_supports( 'custom-post-formats' ) || current_theme_supports( 'post-layouts' ) || current_theme_supports( 'hybrid-core-post-meta-box' ) )
		add_action( 'load-post.php', 'hybrid_admin_enqueue_style' );
}

/**
 * Creates a settings field id attribute for use on the theme settings page.  This is a helper function for use
 * with the WordPress settings API.
 *
 * @since 1.0.0
 */
function hybrid_settings_field_id( $setting ) {
	return hybrid_get_prefix() . "_theme_settings-{$setting}";
}

/**
 * Creates a settings field name attribute for use on the theme settings page.  This is a helper function for 
 * use with the WordPress settings API.
 *
 * @since 1.0.0
 */
function hybrid_settings_field_name( $setting ) {
	return hybrid_get_prefix() . "_theme_settings[{$setting}]";
}

/**
 * Loads the admin.css stylesheet for admin-related features.
 *
 * @since 1.0.0
 */
function hybrid_admin_enqueue_style() {
	wp_enqueue_style( hybrid_get_prefix() . '-admin', trailingslashit( HYBRID_CSS ) . 'admin.css', false, 0.7, 'screen' );
}

/**
 * Function for getting an array of available custom templates with a specific header. Ideally, this function 
 * would be used to grab custom singular post (any post type) templates.  It is a recreation of the WordPress
 * page templates function because it doesn't allow for other types of templates.
 *
 * @since 0.7.0
 * @param array $args Arguments to check the templates against.
 * @return array $post_templates The array of templates.
 */
function hybrid_get_post_templates( $args = array() ) {

	/* Parse the arguments with the defaults. */
	$args = wp_parse_args( $args, array( 'label' => array( 'Post Template' ) ) );

	/* Get theme and templates variables. */
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$post_templates = array();

	/* If there's an array of templates, loop through each template. */
	if ( is_array( $templates ) ) {

		/* Set up a $base path that we'll use to remove from the file name. */
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );

		/* Loop through the post templates. */
		foreach ( $templates as $template ) {

			/* Remove the base (parent/child theme path) from the template file name. */
			$basename = str_replace( $base, '', $template );

			/* Get the template data. */
			$template_data = implode( '', file( $template ) );

			/* Make sure the name is set to an empty string. */
			$name = '';

			/* Loop through each of the potential labels and see if a match is found. */
			foreach ( $args['label'] as $label ) {
				if ( preg_match( "|{$label}:(.*)$|mi", $template_data, $name ) ) {
					$name = _cleanup_header_comment( $name[1] );
					break;
				}
			}

			/* If a post template was found, add its name and file name to the $post_templates array. */
			if ( !empty( $name ) )
				$post_templates[trim( $name )] = $basename;
		}
	}

	/* Return array of post templates. */
	return $post_templates;
}

?>