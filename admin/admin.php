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

	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php', 'hybrid_admin_load_post_meta_boxes' );
	add_action( 'load-post-new.php', 'hybrid_admin_load_post_meta_boxes' );

	/* Registers admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'hybrid_admin_register_styles', 1 );

	/* Loads admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'hybrid_admin_enqueue_styles' );
}

/**
 * Loads the core post meta box files on the 'load-post.php' action hook.  Each meta box file is only loaded if 
 * the theme declares support for the feature.
 *
 * @since 1.2.0
 */
function hybrid_admin_load_post_meta_boxes() {

	/* Load the SEO post meta box. */
	require_if_theme_supports( 'hybrid-core-seo', trailingslashit( HYBRID_ADMIN ) . 'meta-box-post-seo.php' );

	/* Load the post template meta box. */
	require_if_theme_supports( 'hybrid-core-template-hierarchy', trailingslashit( HYBRID_ADMIN ) . 'meta-box-post-template.php' );
}

/**
 * Registers the framework's 'admin.css' stylesheet file.  The function does not load the stylesheet.  It merely
 * registers it with WordPress.
 *
 * @since 1.2.0
 */
function hybrid_admin_register_styles() {
	wp_register_style( 'hybrid-core-admin', trailingslashit( HYBRID_CSS ) . 'admin.css', false, '20110512', 'screen' );
}

/**
 * Loads the admin.css stylesheet for admin-related features.
 *
 * @since 1.2.0
 */
function hybrid_admin_enqueue_styles( $hook_suffix ) {

	/* Load admin styles if on the widgets screen and the current theme supports 'hybrid-core-widgets'. */
	if ( current_theme_supports( 'hybrid-core-widgets' ) && 'widgets.php' == $hook_suffix )
		wp_enqueue_style( 'hybrid-core-admin' );
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