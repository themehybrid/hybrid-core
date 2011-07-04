<?php
/**
 * Creates a meta box for the theme settings page, which displays information about the theme.  If a child theme 
 * is in use, an additional meta box will be added with its information.  To use this feature, the theme must 
 * support the 'about' argument for 'hybrid-core-theme-settings' feature.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Create the about theme meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'hybrid_meta_box_theme_add_about' );

/**
 * Adds the core about theme meta box to the theme settings page.
 *
 * @since 1.2.0
 */
function hybrid_meta_box_theme_add_about() {

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = hybrid_get_theme_data();

	/* Adds the About box for the parent theme. */
	add_meta_box( 'hybrid-core-about-theme', sprintf( __( 'About %1$s', $domain ), $theme_data['Title'] ), 'hybrid_meta_box_theme_display_about', hybrid_get_settings_page_name(), 'side', 'high' );

	/* If the user is using a child theme, add an About box for it. */
	if ( is_child_theme() ) {
		$child_data = hybrid_get_theme_data( 'stylesheet' );
		add_meta_box( 'hybrid-core-about-child', sprintf( __( 'About %1$s', $domain ), $child_data['Title'] ), 'hybrid_meta_box_theme_display_about', hybrid_get_settings_page_name(), 'side', 'high' );
	}
}

/**
 * Creates an information meta box with no settings about the theme. The meta box will display
 * information about both the parent theme and child theme. If a child theme is active, this function
 * will be called a second time.
 *
 * @since 1.2.0
 * @param $object Variable passed through the do_meta_boxes() call.
 * @param array $box Specific information about the meta box being loaded.
 */
function hybrid_meta_box_theme_display_about( $object, $box ) {

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	/* Grab theme information for the parent theme. */
	if ( 'hybrid-core-about-theme' == $box['id'] )
		$theme_data = hybrid_get_theme_data();

	/* Grab theme information for the child theme. */
	elseif ( 'hybrid-core-about-child' == $box['id'] )
		$theme_data = hybrid_get_theme_data( 'stylesheet' ); ?>

	<table class="form-table">
		<tr>
			<th>
				<?php _e( 'Theme:', $domain ); ?>
			</th>
			<td>
				<a href="<?php echo $theme_data['URI']; ?>" title="<?php echo $theme_data['Title']; ?>"><?php echo $theme_data['Title']; ?></a>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e( 'Version:', $domain ); ?>
			</th>
			<td>
				<?php echo $theme_data['Version']; ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e( 'Author:', $domain ); ?>
			</th>
			<td>
				<?php echo $theme_data['Author']; ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e( 'Description:', $domain ); ?>
			</th>
			<td>
				<?php echo $theme_data['Description']; ?>
			</td>
		</tr>
	</table><!-- .form-table --><?php
}

?>