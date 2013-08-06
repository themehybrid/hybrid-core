<?php
/**
 * Theme Fonts - A script to allow users to select theme fonts.
 *
 * Theme Fonts was created to give theme developers an easy way to include multiple font settings 
 * and multiple font choices to their users.  It's main purpose is to provide integration into the 
 * WordPress theme customizer to allow for the selection of fonts.  The script will work with basic 
 * Web-safe fonts, custom fonts added to the theme, and fonts from Google Web Fonts.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   ThemeFonts
 * @version   0.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2013, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * A whole bunch of awesomeness wrapped into one package.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
final class Theme_Fonts {

	/**
	 * Theme-registered font settings.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $settings = array();

	/**
	 * Theme-registered fonts.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $fonts = array();

	/**
	 * Theme-packaged fonts.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $font_stylesheets = array();

	/**
	 * Fonts that have already been queued to load.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $font_queue = array();

	/**
	 * Allowed font weights.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $allowed_font_weights = array(
		'normal',
		'bold',
		'bolder',
		'lighter',
		'100',
		'200',
		'300',
		'400',
		'500',
		'600',
		'700',
		'800',
		'900'
	);

	/**
	 * Allowed font styles.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $allowed_font_styles = array(
		'normal',
		'italic',
		'oblique'
	);

	/**
	 * Sets up the initial actions/filters needed for the class to run.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'init', array( &$this, 'init' ) );

		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ), 15 );

		add_action( 'wp_head', array( &$this, 'print_styles' ) );
	}

	/**
	 * This method basically serves as a wrapper on 'init' to allow themes to know when to 
	 * register their custom fonts.  It also passes the object so that theme developers can 
	 * interact with it.  They'll need to use `$object->add_setting()` and `$object->add_font()`. 
	 * Theme devs should just set a 'callback' when they add support for this feature.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function init() {
		$supports = get_theme_support( 'theme-fonts' );

		if ( !empty( $supports[0] ) ) {

			if ( isset( $supports[0]['callback'] ) ) 
				add_action( 'theme_fonts_register', $supports[0]['callback'] );

			if ( isset( $supports[0]['customizer'] ) && true === $supports[0]['customizer'] )
				add_action( 'customize_register', array( &$this, 'customize_register' ) );
		}

		do_action( 'theme_fonts_register', $this );
	}

	/**
	 * Add a new font setting.  Theme developers should use this method to add new font settings 
	 * to their theme.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function add_setting( $args = array() ) {

		$defaults = array(
			'id'        => '', // Required ID for the setting.
			'label'     => '', // Internationalized label for the theme customizer.
			'default'   => '', // Default font handle to use (see Theme_Fonts::add_font).
			'selectors' => '', // string|array of CSS selectors to use in the <head> style output.
		);

		$args = wp_parse_args( $args, $defaults );

		/* If there's an ID, add the font setting. */
		if ( !empty( $args['id'] ) )
			$this->settings[ $args['id'] ] = $args;
	}

	/**
	 * Add a new font for selection.  Theme developers should use this method to add new fonts 
	 * for their theme.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array  $args
	 * @return void
	 */
	public function add_font( $args = array() ) {

		$defaults = array(
			/* Font-specific settings. */
			'family'  => '',     // Name of the font.
			'weight'  => '400',  // 400, 700, bold, etc.
			'style'   => '',     // italic, oblique, etc.

			/* Script-specific settings. */
			'handle'  => '',    // ID for the font.
			'setting' => '',    // Only allow this font with a specific setting.
			'type'    => '',    // Add 'google' to use the Google Fonts API.
			'uri'     => '',    // URI for font stylesheet packaged in the theme.
		);

		apply_filters( 'theme_fonts_add_font_defaults', $defaults );

		$args = wp_parse_args( $args, $defaults );

		apply_filters( 'theme_fonts_add_font_args', $args );

		/* If a family or handle was given, add the font. */
		if ( !empty( $args['family'] ) || !empty( $args['handle'] ) ) {

			/* Use the 'handle' as the family if one isn't set. */
			$args['family'] = !empty( $args['family'] ) ? $args['family'] : $args['handle'];

			/* Use the 'family' as the handle if one isn't set. */
			$args['handle'] = !empty( $args['handle'] ) ? $args['handle'] : sanitize_key( $args['family'] );

			/* Use the 'family' as the font stack if it's not set. */
			$args['stack'] = !empty( $args['stack'] ) ? $args['stack'] : $args['family'];

			/* Use the 'family' as the label if it's not set. */
			$args['label'] = !empty( $args['label'] ) ? esc_html( $args['label'] ) : esc_html( $args['family'] );

			/* Add the font and its arguments to the $fonts property. */
			$this->fonts[ $args['handle'] ] = $args;
		}
	}

	/**
	 * Gets a font that has been added.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string  $handle
	 * @return array
	 */
	public function get_font( $handle ) {

		return isset( $this->fonts[ $handle ] ) ? $this->fonts[ $handle ] : '';
	}

	/**
	 * Removes a font that has been added.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string  $handle
	 * @return void
	 */
	public function remove_font( $handle ) {

		if ( isset( $this->fonts[ $handle ] ) )
			unset( $this->fonts[ $handle ] );
	}

	/**
	 * Loads stylesheet file needed for Google Web Fonts.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function enqueue_styles() {

		$stylesheet = $this->get_style_uri();

		if ( !empty( $stylesheet ) )
			wp_enqueue_style( 'theme-fonts', $stylesheet, array(), null );

		if ( !empty( $this->font_stylesheets ) ) {

			foreach ( $this->font_stylesheets as $handle => $uri )
				wp_enqueue_style( "theme-fonts-{$handle}", esc_url( $uri ) );
		}
	}

	/**
	 * Builds the stylesheet URI needed to load fonts from Google Web Fonts.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function get_style_uri() {

		/* Get the theme-specified settings for the 'theme-fonts' feature. */
		$supports = get_theme_support( 'theme-fonts' );

		/* Set up an empty string for the font string. */
		$font_string = '';

		/* Loop through each of the font settings and queue the fonts associated with them. */
		foreach ( $this->settings as $name => $setting ) {

			$font_handle = get_theme_mod( "theme_font_{$name}", $setting['default'] );

			$this->queue_font( $font_handle );
		}

		if ( empty( $this->font_queue ) )
			return '';

		/* Loop through each of the queued fonts and add them to the font string. */
		foreach ( $this->font_queue as $family => $args ) {

			$font_string .= !empty( $font_string ) ? "|{$family}" : $family;

			/* If any font styles (weight, style) were specified, add them to the string. */
			if ( isset( $args['styles'] ) && is_array( $args['styles'] ) ) {

				$font_styles = array_unique( $args['styles'] );

				$font_string .= ':' . join( ',', $font_styles );
			}
		}

		/* Set up the query arguments and add the font family. */
		$query_args = array( 'family' => $font_string );

		/* If the theme registered support for other font settings, add them. */
		if ( !empty( $supports[0] ) ) {

			/* Get the defined subset. */
			$subset = isset( $supports[0]['subset'] ) ? $supports[0]['subset'] : array();

			/* Allow devs and theme users to override the subset. */
			$subset = apply_filters( 'theme_fonts_subset', $subset );

			/* If a subset was defined, add it to the query args. */
			if ( !empty( $subset ) )
				$query_args['subset'] = urlencode( join( ',', $subset ) );

			/* If specific text is requested, add it to the query args. */
			if ( isset( $supports[0]['text'] ) )
				$query_args['text'] = urlencode( $supports[0]['text'] );
		}

		/* Set up the stylesheet URI. */
		$style_uri = ( is_ssl() ? 'https' : 'http' ) . '://fonts.googleapis.com/css';

		/* Return the stylesheet URI with added query args. */
		return add_query_arg( $query_args, $style_uri );
	}

	/**
	 * Queues a font by its font family.  This is separate because multiples of the same family 
	 * may be loaded.  For example, both the 'Open Sans 400' and 'Open Sans 700 Italic' could 
	 * be loaded.  These both have the same family of 'Open Sans', so we need to queue the 
	 * fonts and attach the styles to the font family.  This is only needed for Google Web Fonts.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  string  $handle
	 * @return void
	 */
	protected function queue_font( $handle ) {

		/* Get the requested font. */
		$font = $this->get_font( $handle );

		/* If a URI was given for the font stylesheet, use it. */
		if ( !empty( $font['uri'] ) ) {

			$this->font_stylesheets[ $handle ] = $font['uri'];
		}

		/* If 'google' is set to the 'type', queue the font. */
		elseif ( !empty( $font['type'] ) && 'google' === strtolower( $font['type'] ) ) {

			/* Encode the font family for URLs. */
			$font_family = urlencode( $font['family'] );

			/* Set up an empty string for adding the font styles (weight, style). */
			$font_styles = '';

			/* If the font family has not been added to the queue, add it now. */
			if ( !array_key_exists( $font_family, $this->font_queue ) )
				$this->font_queue[ $font_family ] = array();

			/* If a weight was specified, add it to the font styles string. */
			if ( !empty( $font['weight'] ) )
				$font_styles .= $font['weight'];

			/* If a style was specified, add it to the font styles string. */
			if ( !empty( $font['style'] ) )
				$font_styles .= $font['style'];

			/* If font styles were found, add them to the font queue for their font family. */
			if ( !empty( $font_styles ) )
				$this->font_queue[ $font_family ]['styles'][] = $font_styles;
		}
	}

	/**
	 * Creates the section, settings, and controls for the WordPress theme customizer screen.  Each 
	 * font setting is given an individual setting and control within the 'fonts' section.  The data 
	 * is saved in the 'theme_mod' setting for the theme with the 'theme_font_{$setting_id}' name.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function customize_register( $wp_customize ) {

		/* Add a new section called 'fonts' to the theme customizer screen. */
		$wp_customize->add_section(
			'fonts',
			array(
				'title'      => esc_html__( 'Fonts', 'theme-fonts' ),
				'priority'   => 37,
				'capability' => 'edit_theme_options'
			)
		);

		/* Each control will be given a priority of $priority + 10. */
		$priority = 0;

		/* Loop through each of the style options and add settings and controls. */
		foreach( $this->settings as $setting_id => $args) {

			$choices = $this->get_font_choices( $setting_id );

			/* If any stylesheets were found, add a setting and control for this style option. */
			if ( !empty( $choices ) ) {

				asort( $choices );

				/* Iterate the priority. */
				$priority = $priority + 10;

				/* Add the theme font setting. */
				$wp_customize->add_setting(
					"theme_font_{$setting_id}",
					array(
						'default'              => get_theme_mod( "theme_font_{$setting_id}", $args['default'] ),
						'type'                 => 'theme_mod',
						'capability'           => 'edit_theme_options',
						'priority'             => $priority,
						'sanitize_callback'    => 'sanitize_key',
						'sanitize_js_callback' => 'sanitize_key',
					//	'transport'            => 'postMessage'
					)
				);

				/* Add the theme font control. */
				$wp_customize->add_control(
					"theme-fonts-{$setting_id}",
					array(
						'label'    => !empty( $args['label'] ) ? $args['label'] : $setting_id,
						'section'  => 'fonts',
						'settings' => "theme_font_{$setting_id}",
						'type'     => 'select',
						'choices'  => $choices
					)
				);
			}
		}
	}

	/**
	 * Returns an array of font choices for the theme customizer.  Theme developers can add fonts for 
	 * a specific setting by using the 'setting' argument.  If not set, the font is added to all 
	 * settings.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  string  $setting
	 * @return array
	 */
	protected function get_font_choices( $setting ) {

		$choices = array();

		/* Loop through each off the theme-registered fonts. */
		foreach ( $this->fonts as $handle => $font ) {

			/* If the font doesn't have a defined setting or if it's specifically for this setting, add it. */
			if ( empty( $font['setting'] ) || ( isset( $font['setting'] ) && $setting === $font['setting'] ) )
				$choices[ $handle ] = $font['label'];
		}

		return $choices;
	}

	/**
	 * Outputs custom style rules into the header.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string  $setting
	 * @return array
	 */
	public function print_styles() {

		$style = '';

		/* Loop through each of the theme's font settings. */
		foreach ( $this->settings as $name => $setting ) {

			/* Make sure the selectors are in an array and allow them to be filtered. */
			if ( !empty( $setting['selectors'] ) && !is_array( $setting['selectors'] ) )
				$setting['selectors'] = explode( ',', $setting['selectors'] );

			$selectors = apply_filters( "theme_fonts_{$name}_selectors", array_map( 'trim', $setting['selectors'] ) );

			/* If the theme didn't specify any selectors, we don't have anything to do here. */
			if ( !empty( $selectors ) ) {

				/* Get the font handle specific to this setting. */
				$font_handle = get_theme_mod( "theme_font_{$name}", $setting['default'] );

				/* Get the font arguments. */
				$font = $this->get_font( $font_handle );

				/* Add the font family (use 'stack' argument if set). */
				$font_family = !empty( $font['stack'] ) ? $font['stack'] : "'{$font['family']}'";

				/* Add the font weight. */
				$font_weight = in_array( $font['weight'], $this->allowed_font_weights ) ? $font['weight']  : '400';

				/* Add the font style. */
				$font_style = in_array( $font['style'], $this->allowed_font_styles ) ? $font['style'] : 'normal';

				/* Wrap everything up in a nice CSS statement. */
				$style .= join( ', ', $selectors ) . " { font-family: {$font_family}; font-weight: {$font_weight}; font-style: {$font_style}; } ";
			}
		}

		/* If we have a style, output it in <head>. */
		if ( !empty( $style ) )
			echo "\n" . '<style type="text/css" id="theme-fonts-rules-css">' . trim( $style ) . '</style>' . "\n";
	}
}

$theme_fonts = new Theme_Fonts();

?>