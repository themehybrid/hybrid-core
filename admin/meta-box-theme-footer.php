<?php
/**
 * Handles the display and functionality of the theme settings page. This provides the needed hooks and
 * meta box calls for developers to create any number of theme settings needed.
 *
 * Provides the ability for developers to add custom meta boxes to the theme settings page by using the 
 * add_meta_box() function.  Developers should hook their meta box registration function to 'admin_menu' 
 * and register the meta box for 'appearance_page-theme-settings'. If data needs to be saved, devs can 
 * use the '$prefix_update_settings_page' action hook to save their data.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Hook the settings page function to 'admin_menu'. */
add_action( 'admin_menu', 'hybrid_settings_page_init' );

/**
 * Initializes all the theme settings page functions. This function is used to create the theme settings 
 * page, then use that as a launchpad for specific actions that need to be tied to the settings page.
 *
 * Users or developers can set a custom capability (default is 'edit_theme_options') for access to the
 * settings page using the "$prefix_settings_capability" filter hook.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page_init() {
	global $hybrid;

	/* Get theme information. */
	$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	/* Register theme settings. */
	register_setting( "{$prefix}_theme_settings", "{$prefix}_theme_settings", 'hybrid_save_theme_settings' );

	/* Create the theme settings page. */
	$hybrid->settings_page = add_theme_page( sprintf( __( '%1$s Theme Settings', $domain ), $theme_data['Name'] ), sprintf( __( '%1$s Settings', $domain ), $theme_data['Name'] ), apply_filters( "{$prefix}_settings_capability", 'edit_theme_options' ), 'theme-settings', 'hybrid_settings_page' );

	/* Register the default theme settings meta boxes. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_create_settings_meta_boxes' );

	/* Make sure the settings are saved. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_load_settings_page' );

	/* Load the JavaScript and stylehsheets needed for the theme settings. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_settings_page_enqueue_script' );
	add_action( "load-{$hybrid->settings_page}", 'hybrid_admin_enqueue_style' );
	add_action( "admin_head-{$hybrid->settings_page}", 'hybrid_settings_page_load_scripts' );
}

/**
 * Validation/Sanitization callback function for theme settings.  This just returns the data passed to it.  Theme
 * developers should validate/sanitize their theme settings on the "sanitize_option_{$prefix}_theme_settings" 
 * hook.  This function merely exists for backwards compatibility.
 *
 * @since 0.7.0
 */
function hybrid_save_theme_settings( $settings ) {
	$prefix = hybrid_get_prefix();

	/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
	if ( current_theme_supports( 'hybrid-core-meta-box-footer' ) && isset( $settings['footer_insert'] ) && !current_user_can( 'unfiltered_html' ) )
		$settings['footer_insert'] = stripslashes( wp_filter_post_kses( addslashes( $settings['footer_insert'] ) ) );

	/* Allow developers to futher validate/sanitize the data. */
	/* @deprecated 1.0.0. Developers should filter "sanitize_option_{$prefix}_theme_settings" instead. */
	$settings = apply_filters( "{$prefix}_validate_theme_settings", $settings );

	/* Return the validated settings. */
	return $settings;
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

	/* If the current theme supports the footer meta box and shortcodes, add default footer settings. */
	if ( current_theme_supports( 'hybrid-core-meta-box-footer' ) && current_theme_supports( 'hybrid-core-shortcodes' ) ) {

		/* If there is a child theme active, add the [child-link] shortcode to the $footer_insert. */
		if ( STYLESHEETPATH !== TEMPLATEPATH )
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
 * Saves the default theme settings in the {$wpdb->prefix}_options if none have been added.  The 
 * settings are given a unique name depending on the theme directory.  They are always saved as 
 * {$prefix}_theme_settings in the database. 
 *
 * @since 0.7.0
 */
function hybrid_load_settings_page() {
	$prefix = hybrid_get_prefix();

	/* Get theme settings from the database. */
	$settings = get_option( "{$prefix}_theme_settings" );

	/* If no settings are available, add the default settings to the database. */
	if ( false === $settings ) {
		$settings = hybrid_get_default_theme_settings();
		add_option( "{$prefix}_theme_settings", $settings, '', 'yes' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=theme-settings' ) );
		exit;
	}
}

/**
 * Creates the default meta boxes for the theme settings page. Parent/child theme and plugin developers
 * should use add_meta_box() to create additional meta boxes.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function hybrid_create_settings_meta_boxes() {
	global $hybrid;

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );

	/* Adds the About box for the parent theme. */
	add_meta_box( "{$prefix}-about-theme-meta-box", sprintf( __( 'About %1$s', $domain ), $theme_data['Title'] ), 'hybrid_about_theme_meta_box', $hybrid->settings_page, 'normal', 'high' );
 
	/* If the user is using a child theme, add an About box for it. */
	if ( TEMPLATEPATH != STYLESHEETPATH ) {
		$child_data = get_theme_data( trailingslashit( STYLESHEETPATH ) . 'style.css' );
		add_meta_box( "{$prefix}-about-child-meta-box", sprintf( __( 'About %1$s', $domain ), $child_data['Title'] ), 'hybrid_about_theme_meta_box', $hybrid->settings_page, 'normal', 'high' );
	}

	/* Creates a meta box for the footer settings. */
	if ( current_theme_supports( 'hybrid-core-meta-box-footer' ) )
		add_meta_box( "{$prefix}-footer-settings-meta-box", __( 'Footer settings', $domain ), 'hybrid_footer_settings_meta_box', $hybrid->settings_page, 'normal', 'high' );
}

/**
 * Creates an information meta box with no settings about the theme. The meta box will display
 * information about both the parent theme and child theme. If a child theme is active, this function
 * will be called a second time.
 *
 * @since 0.7.0
 * @param $object Variable passed through the do_meta_boxes() call.
 * @param array $box Specific information about the meta box being loaded.
 */
function hybrid_about_theme_meta_box( $object, $box ) {

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	/* Grab theme information for the parent theme. */
	if ( "{$prefix}-about-theme-meta-box" == $box['id'] )
		$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );

	/* Grab theme information for the child theme. */
	elseif ( "{$prefix}-about-child-meta-box" == $box['id'] )
		$theme_data = get_theme_data( trailingslashit( STYLESHEETPATH ) . 'style.css' ); ?>

	<table class="form-table">
		<tr>
			<th><?php _e( 'Theme:', $domain ); ?></th>
			<td><a href="<?php echo $theme_data['URI']; ?>" title="<?php echo $theme_data['Title']; ?>"><?php echo $theme_data['Title']; ?> <?php echo $theme_data['Version']; ?></a></td>
		</tr>
		<tr>
			<th><?php _e( 'Author:', $domain ); ?></th>
			<td><?php echo $theme_data['Author']; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Description:', $domain ); ?></th>
			<td><?php echo $theme_data['Description']; ?></td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Creates a settings box that allows users to customize their footer. A basic textarea is given that
 * allows HTML and shortcodes to be input.
 *
 * @since 0.7.0
 */
function hybrid_footer_settings_meta_box() {
	$domain = hybrid_get_textdomain(); ?>

	<table class="form-table">
		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'footer_insert' ); ?>"><?php _e( 'Footer Insert:', $domain ); ?></label></th>
			<td>
				<p><span class="description"><?php _e( 'You can add custom <acronym title="Hypertext Markup Language">HTML</acronym> and/or shortcodes, which will be automatically inserted into your theme.', $domain ); ?></span></p>

				<p><textarea id="<?php echo hybrid_settings_field_id( 'footer_insert' ); ?>" name="<?php echo hybrid_settings_field_name( 'footer_insert' ); ?>" cols="60" rows="5" style="width: 98%;"><?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'footer_insert' ) ) ); ?></textarea></p>

				<?php if ( current_theme_supports( 'hybrid-core-shortcodes' ) ) { ?>
					<p><?php printf( __( 'Shortcodes: %s', $domain ), '<code>[the-year]</code>, <code>[site-link]</code>, <code>[wp-link]</code>, <code>[theme-link]</code>, <code>[child-link]</code>, <code>[loginout-link]</code>, <code>[query-counter]</code>' ); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the theme settings page and calls do_meta_boxes() to allow additional settings
 * meta boxes to be added to the page.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page() {
	global $hybrid;

	/* Get the theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' ); ?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php printf( __( '%1$s Theme Settings', $domain ), $theme_data['Name'] ); ?></h2>

		<?php if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) echo '<p class="updated fade below-h2" style="padding: 5px 10px;"><strong>' . __( 'Settings saved.', $domain ) . '</strong></p>'; ?>

		<div id="poststuff">

			<form method="post" action="options.php">

				<?php settings_fields( "{$prefix}_theme_settings" ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $hybrid->settings_page, 'normal', null ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $hybrid->settings_page, 'advanced', null ); ?></div>
					<div class="post-box-container column-3 side"><?php do_meta_boxes( $hybrid->settings_page, 'side', null ); ?></div>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php esc_attr_e( 'Update Settings', $domain ); ?>" />
				</p><!-- .submit -->

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads the JavaScript files required for managing the meta boxes on the theme settings
 * page, which allows users to arrange the boxes to their liking.
 *
 * @since 0.7.0
 */
function hybrid_settings_page_enqueue_script() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
}

/**
 * Loads the JavaScript required for toggling the meta boxes on the theme settings page.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page_load_scripts() {
	global $hybrid; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $hybrid->settings_page; ?>' );
		});
		//]]>
	</script><?php
}

?>