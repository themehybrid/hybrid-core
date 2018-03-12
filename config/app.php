<?php

return [

	/*
	 |-------------------------------------------------------------------------
	 | Framework namespace.
	 |-------------------------------------------------------------------------
	 |
	 | A prefix/namespace used for filter hooks and such in the theme.
	 |
	 */
	 'namespace' => 'hybrid',

	/*
 	 |-------------------------------------------------------------------------
 	 | Framework version.
 	 |-------------------------------------------------------------------------
 	 |
 	 | The version number for the theme (used mostly for script/styles).
 	 |
 	 */
 	 'version' => '5.0.0',

	/*
	 |-------------------------------------------------------------------------
	 | Framework Directory Path
	 |-------------------------------------------------------------------------
	 |
	 | The absolute path to the framework directory (e.g., `/htdocs/wp-content/themes/abc/hybrid-core`).
	 |
	 */

	'dir' => defined( 'HYBRID_DIR' ) ? HYBRID_DIR : trailingslashit( realpath( trailingslashit( __DIR__ ) . '../' ) ),

	/*
	 |-------------------------------------------------------------------------
	 | Framework Directory URI
	 |-------------------------------------------------------------------------
	 |
	 | URI to the framework directory (e.g., `http://localhost/wp-content/themes/abc/hybrid-core`).
	 |
	 */

	'uri' => defined( 'HYBRID_URI' )
	         ? HYBRID_DIR
		 : esc_url( trailingslashit( str_replace(
	                 wp_normalize_path( untrailingslashit( ABSPATH ) ),
	                 site_url(),
	                 wp_normalize_path( realpath( trailingslashit( __DIR__ ) . '../' ) )
	         ) ) ),

];
