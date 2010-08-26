<?php
/**
 * The theme administration functions are initialized and set up mainly from this file.  It is used to
 * launch the theme settings page and allow child themes and plugins to access theme-specific 
 * features. See meta-box.php for the post meta box functions.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Initialize the theme admin functionality. */
add_action( 'init', 'hybrid_admin_init' );

/**
 * Initializes the theme administration functions. Makes sure we have a theme settings
 * page and a meta box on the edit post/page screen.
 *
 * @since 0.7
 */
function hybrid_admin_init() {
	$prefix = hybrid_get_prefix();

	if ( current_theme_supports( 'hybrid-core-theme-settings' ) ) {

		/* Initialize the theme settings page. */
		add_action( 'admin_menu', 'hybrid_settings_page_init' );

		/* Save settings page meta boxes. */
		add_action( "{$prefix}_update_settings_page", 'hybrid_save_theme_settings' );
	}

	/* Add a new meta box to the post editor. */
	if ( current_theme_supports( 'hybrid-core-post-meta-box' ) )
		add_action( 'admin_menu', 'hybrid_create_post_meta_box' );

	/* Load the admin stylesheet for the widgets screen. */
	add_action( 'load-widgets.php', 'hybrid_settings_page_enqueue_style' );
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