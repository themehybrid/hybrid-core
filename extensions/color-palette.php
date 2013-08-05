<?php
/**
 * Color Palette - A script to allow user-selected theme colors.
 *
 * Color Palette was created so that theme developers could easily add color options to the built-in 
 * WordPress theme customizer.  This makes theme developers' jobs easier by allowing them to simply 
 * plug in the values.  And, it gives users something fun to play with!
 *
 * <rant>
 * I encourage all theme developers who use this feature to use it wisely.  At some point, enough is 
 * enough.  This is not meant to be a full CSS-replacement script.  It wasn't created so that all 
 * themes could have a buttload of color options.  Use some common sense when applying this script 
 * and have fun with your designs.  A script like this can be limiting to your abilities as a 
 * designer.  You don't have to cave to user demands to add more options.  Make decisions and have 
 * some faith in your own skills.  You are a theme *designer*, right?.
 * </rant>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   ColorPalette
 * @version   0.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2013, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Handles custom theme color options via the WordPress theme customizer.
 *
 * @since  0.1.0
 * @access public
 */
final class Color_Palette {

	/**
	 * Array of individual color options and their settings.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $colors = array();

	/**
	 * An array of properties and selectors.  "$rules[ $color_id ][ $property ][ $selectors ]"
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $rules = array();

	/**
	 * The allowed CSS properties the theme developer can set a color rule for.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public 	$allowed_properties = array(
		'color',
		'background-color',
		'border-color',
		'border-top-color',
		'border-bottom-color',
		'border-right-color',
		'border-left-color',
		'outline-color'
	);

	/**
	 * Sets up the Custom Colors Palette feature.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Get the theme support arguements for 'color-palette'. */
		$supports = get_theme_support( 'color-palette' );

		/* If a callback was set, add it to the correct action hook. */
		if ( !empty( $supports[0] ) && isset( $supports[0]['callback'] ) ) 
			add_action( 'color_palette_register', $supports[0]['callback'] );

		/* Output CSS into <head>. */
		add_action( 'wp_head', array( &$this, 'wp_head_callback' ) );

		/* Add a '.custom-colors' <body> class. */
		add_filter( 'body_class', array( &$this, 'body_class' ) );

		/* Add options to the theme customizer. */
		add_action( 'customize_register', array( &$this, 'customize_register' ) );

		/* Delete the cached data for this feature. */
		add_action( 'update_option_theme_mods_' . get_stylesheet(), array( &$this, 'cache_delete' ) );

		/* Hook for registering custom colors and rule sets. */
		do_action( 'color_palette_register', $this );
	}

	/**
	 * Add a color option.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array  $args
	 * @return void
	 */
	public function add_color( $args ) {

		if ( !isset( $args['id'] ) )
			return;

		$args['default'] = isset( $args['default'] ) ? $this->sanitize_hex_color( $args['default'] ) : '';

		$this->colors[ $args['id'] ] = $args;
	}

	/**
	 * Get a color option.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string  $id
	 * @return array|string
	 */
	public function get_color( $id ) {

		return isset( $this->colors[ $id ] ) ? $this->colors[ $id ] : '';
	}

	/**
	 * Add a complete rule set for a specific theme color.  The color must already be registered.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string  $color_id
	 * @param  array   $rules
	 * @return void
	 */
	public function add_rule_set( $color_id, $rules ) {

		$color = $this->get_color( $color_id );

		if ( empty( $color ) )
			return;

		foreach ( $rules as $property => $selectors ) {

			if ( in_array( $property, $this->allowed_properties ) ) {

				if ( !is_array( $selectors ) )
					$selectors = array_map( 'trim', explode( ',', $selectors ) );

				$this->rules[ $color_id ][ $property ] = $selectors;
			}
		}
	}

	/**
	 * Adds the 'custom-colors' class to the <body> element.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array  $classes
	 * @return array
	 */
	public function body_class( $classes ) {

		$classes[] = 'custom-colors';

		return $classes;
	}

	/**
	 * Callback for 'wp_head' that outputs the CSS for this feature.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function wp_head_callback() {

		/* Get the cached style. */
		$style = wp_cache_get( 'color_palette' );

		/* If the style is available, output it and return. */
		if ( !empty( $style ) ) {
			echo $style;
			return;
		}

		/* Loop through each of the rules by name. */
		foreach ( $this->rules as $color_id => $properties ) {

			$color = $this->get_color( $color_id );

			/* Get the saved color. */
			$hex = get_theme_mod( 'color_palette_' . sanitize_key( $color_id ), $color['default'] );

			/* Loop through each of the properties. */
			foreach ( $properties as $property => $selectors )
				$style .= join( ', ', $selectors ) . " { {$property}: #{$hex}; } ";
		}

		/* Put the final style output together. */
		$style = "\n" . '<style type="text/css" id="custom-colors-css">' . trim( $style ) . '</style>' . "\n";

		/* Cache the style, so we don't have to process this on each page load. */
		wp_cache_set( 'color_palette', $style );

		/* Output the custom style. */
		echo $style;
	}

	/**
	 * Registers the customize settings and controls.  We're tagging along on WordPress' built-in 
	 * 'Colors' section.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  object $wp_customize
	 * @return void
	 */
	public function customize_register( $wp_customize ) {

		/* Each control will be given a priority of $priority + 10. */
		$priority = 0;

		/* Loop through each of the defined color options. */
		foreach ( $this->colors as $color_id => $args ) {

			/* Iterate the priority. */
			$priority = $priority + 10;

			/* Sanitize the color option name. */
			$color_id = sanitize_key( $color_id );

			/* Add a new setting for this color. */
			$wp_customize->add_setting(
				"color_palette_{$color_id}",
				array(
					'default'              => "#{$args['default']}",
					'type'                 => 'theme_mod',
					'capability'           => 'edit_theme_options',
					'sanitize_callback'    => 'sanitize_hex_color_no_hash',
					'sanitize_js_callback' => 'maybe_hash_hex_color',
					'transport'            => 'postMessage'
				)
			);

			/* Add a control for this color. */
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					"color-palette-control-{$color_id}",
					array(
						'label'    => esc_html( $args['label'] ),
						'section'  => 'colors',
						'settings' => "color_palette_{$color_id}",
						'priority' => $priority
					)
				)
			);
		}

		/* If viewing the customize preview screen, add a script to show a live preview. */
		if ( $wp_customize->is_preview() && !is_admin() )
			add_action( 'wp_footer', array( &$this, 'customize_preview_script' ), 21 );
	}

	/**
	 * Theme customizer preview script.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function customize_preview_script() { ?>

		<script type="text/javascript">

		<?php foreach ( $this->rules as $color_id => $rule ) { ?>
			wp.customize( 
				'color_palette_<?php echo sanitize_key( $color_id ); ?>',
				function( value ) {
					value.bind(
						function( to ) {
						<?php foreach ( $rule as $property => $selectors ) { 

							/* Only run if property is allowed. */
							if ( !in_array( $property, $this->allowed_properties ) )
								continue;

							/* Remove pseudo-selectors and pseudo-elements. */
							$selectors = array_filter( $selectors, array( $this, 'remove_js_pseudo' ) );

							/**
							 * Allow theme developers to define jQuery().not() so 
							 * they can make sure some elements don't get 
							 * overwritten on the live preview.
							 */
							$do_not_overwrite = apply_filters( 'color_palette_preview_js_ignore', '', $color_id, $property, $selectors );

							$not = !empty( $do_not_overwrite ) ? ".not( '{$do_not_overwrite}' )" : '';
							?>
							
							jQuery( '<?php echo join( ', ', $selectors ); ?>' )<?php echo $not; ?>.css( '<?php echo $property; ?>', to );
						<?php } ?>
						}
					);
				}
			);<?php 
		} ?> 

		</script><?php 
	}

	/**
	 * Sanitizes hex colors.  Removes the left '#'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $color
	 * @return string
	 */
	public function sanitize_hex_color( $color ) {

		$color = ltrim( $color, '#' );

		return preg_replace( '/[^0-9a-fA-F]/', '', $color );
	}

	/**
	 * Helper function to be used in array_filter() for removing pseudo-selectors and pseudo-elements 
	 * from the theme customizer preview.  jQuery won't handle these well.
	 *
	 * @todo   Use jQuery's .hover() to handle ':hover'.
	 * @since  0.1.0
	 * @access public
	 * @param  string $element
	 * @return bool
	 */
	public function remove_js_pseudo( $element ) {

		if ( false === strpos( $element, ':' ) )
			return true;

		return false;
	}

	/**
	 * Deletes the cached style CSS that's output into the header.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function cache_delete() {
		wp_cache_delete( 'color_palette' );
	}
}

$color_palette = new Color_Palette();

?>