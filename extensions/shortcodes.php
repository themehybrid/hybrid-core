<?php

add_shortcode( 'column', 'column_shortcode' );

function column_shortcode( $attr, $content = null ) {

	$attr = shortcode_atts(
		array(
			'position' => '',
			'number' => '1'
		),
		$attr
	);

	$position = '';

	if ( !empty( $attr['position'] ) )
		$position = ' column-last';

	$out = '<div class="column column-' . $attr['number'] . $position .'">';
	$out .= do_shortcode( $content );
	$out .= '</div>';

	return $out;
}
















?>