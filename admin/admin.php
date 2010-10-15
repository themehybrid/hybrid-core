<?php
/**
 * The theme administration functions are initialized and set up mainly from this file.  It is used to
 * launch the theme settings page and allow child themes and plugins to access theme-specific 
 * features. See meta-box.php for the post meta box functions.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Add the admin init function to the 'admin_init' hook. */
add_action( 'admin_init', 'hybrid_admin_init' );

/**
 * Initializes any admin-related features needed for the framework.
 *
 * @since 0.7
 */
function hybrid_admin_init() {

	/* Load the admin stylesheet for the widgets screen. */
	if ( current_theme_supports( 'hybrid-core-widgets' ) )
		add_action( 'load-widgets.php', 'hybrid_admin_enqueue_style' );
}

/**
 * Creates a settings field id attribute for use on the theme settings page.
 *
 * @since 0.9.1
 */
function hybrid_settings_field_id( $setting ) {
	$prefix = hybrid_get_prefix();
	return "{$prefix}_theme_settings-{$setting}";
}

/**
 * Creates a settings field name attribute for use on the theme settings page.
 *
 * @since 0.9.1
 */
function hybrid_settings_field_name( $setting ) {
	$prefix = hybrid_get_prefix();
	return "{$prefix}_theme_settings[{$setting}]";
}

/**
 * Loads the admin.css stylesheet for admin-related features.
 *
 * @since 0.9.1
 */
function hybrid_admin_enqueue_style() {
	wp_enqueue_style( hybrid_get_prefix() . '-admin', HYBRID_CSS . '/admin.css', false, 0.7, 'screen' );
}

/**
 * Function for getting an array of available custom templates with a specific header. Ideally,
 * this function would be used to grab custom singular post (any post type) templates.
 *
 * @since 0.7
 * @param array $args Arguments to check the templates against.
 * @return array $post_templates The array of templates.
 */
function hybrid_get_post_templates( $args = array() ) {

	$args = wp_parse_args( $args, array( 'label' => array( 'Post Template' ) ) );

	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$post_templates = array();

	if ( is_array( $templates ) ) {
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );

		foreach ( $templates as $template ) {
			$basename = str_replace( $base, '', $template );

			$template_data = implode( '', file( $template ) );

			$name = '';
			foreach ( $args['label'] as $label ) {
				if ( preg_match( "|{$label}:(.*)$|mi", $template_data, $name ) ) {
					$name = _cleanup_header_comment( $name[1] );
					break;
				}
			}

			if ( !empty( $name ) )
				$post_templates[trim( $name )] = $basename;
		}
	}

	return $post_templates;
}

?>