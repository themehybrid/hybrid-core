<?php
/**
 * Set of functions to gather objects by custom field.
 * Output in XHTML-compliant <object> element.
 *
 * @copyright 2008 - 2010
 * @version 0.1
 * @author Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package GetTheObject
 */

/**
 * Catchall function for getting objects.
 *
 * @since 0.1
 * @param array $args
 * @return string
 */
function get_the_object( $args = array() ) {
	global $post;

	$defaults = array(
		 'post_id' => $post->ID,
		'custom_key' => array( 'Object', 'Video' ),
		'attachment' => true,
		'default_object' => false,
		'object_scan' => false,
		'src' => false,
		'file' => false,
		'mime_type' => false,
		'class' => 'player',
		'width' => '300',
		'height' => '250',
		'echo' => true
	);

	$args = apply_filters( 'get_the_object_args', $args );

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( $src )
		$object = object_by_file( $args );

	if ( !$object && $custom_key )
		$object = object_by_custom_field( $args );

	if ( !$object && $attachment )
		$object = object_by_attachment( $args );

	if ( !$object && $object_scan )
		$object = object_by_scan( $args );

	if ( !$object && $default_object )
		$object = object_by_default( $args );

	if ( $object )
		$object = display_the_object( $args, $object );

	$object = apply_filters( 'get_the_object', $object );

	if ( $echo )
		echo $object;
	else
		return $object;
}

/**
 * Attempt to get a video by custom field
 *
 * @since 0.1
 * @global $post The current post's DB object.
 * @param array $args
 * @return string
 */
function object_by_custom_field( $args = array() ) {

	/* If $custom_key is a string, we want to split it by spaces into an array. */
	if ( !is_array( $args['custom_key'] ) )
		$args['custom_key'] = preg_split( '#\s+#', $args['custom_key'] );

	/* If $custom_key is set, loop through each custom field key, searching for values. */
	if ( isset( $args['custom_key'] ) ) {
		foreach ( $args['custom_key'] as $custom ) {
			$image = get_metadata( 'post', $args['post_id'], $custom, true );
			if ( $image )
				break;
		}
	}

	/* If a custom key value has been given for one of the keys, return the URL. */
	if ( $url )
		return array( 'url' => $url );

	return false;
}

/**
 * Used for generating an object element based on URL.
 *
 * @since 0.1
 * @param array $args
 * @return array
 */
function object_by_file( $args = array() ) {

	if ( $args['src'] )
		return array( 'url' => $args['src'] );

	return false;
}

/**
 * Check for attachment objects
 * If attachments are found, loop through each
 * The loop only breaks once $order_of_image is reached
 *
 * @since 0.1
 * @global $post The current post's DB object.
 * @param array $args
 * @return array|bool
 */
function object_by_attachment( $args = array() ) {
	extract( $args );

	/* Get attachments. */
	$attachments = get_children( array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) );

	if ( empty( $attachments ) )
		return false;

	foreach ( $attachments as $id => $attachment ) {
		if ( !wp_attachment_is_image( $id ) ) {
			$url = wp_get_attachment_url( $id );
			break;
		}
	}

	if ( $url )
		return array( 'url' => $url, 'mime_type' => $type );

	return false;
}

/**
 * Scans the post for objects within the content.
 * Not called by default with get_the_object()
 *
 * @since 0.1
 * @global $post The current post's DB object.
 * @param array $args
 * @return array|bool
 */
function object_by_scan( $args = array() ) {

	$content =  get_post_field( 'post_content', $args['post_id'] );

	preg_match_all( '|<object.*?data=[\'"](.*?)[\'"].*?>|i', $content, $url );

	if ( isset( $url ) ) {
		$out = array( 'url' => $url[1][0] );

		preg_match_all( '|<object.*?type=[\'"](.*?)[\'"].*?>|i', $content, $type );

		if ( isset( $type ) )
			$out['mime_type'] = $type[1][0];

		return $out;
	}

	return false;
}

/**
 * Used for setting a default object.
 * Not used with get_the_object() by default.
 *
 * @since 0.1
 * @param array $args
 * @return array|bool
 */
function object_by_default( $args = array() ) {

	if ( $args['default_object'] )
		return array( 'url' => $args['default_object'] );

	return false;
}

/**
 * Function for displaying a video
 *
 * @since 0.1
 * @param array $args
 * @param array $video_arr
 * @return string
 */
function display_the_object( $args, $object ) {

	extract( $args );

	if ( $object['mime_type'] )
		$mime_type = $object['mime_type'];
	if ( !$mime_type )
		$mime_type = get_the_object_mime_type( $object['url'] );
	if ( !$mime_type )
		$mime_type = 'application/x-shockwave-flash';

	if ( $object['url'] ) {
		$player = '<object type="' . $mime_type . '" data="' . $object['url'] . '" class="' . $class . '" width="' . $width . '" height="' . $height . '">';
		$player .= '<param name="movie" value="' . $object['url'] . '" />';
		$player .= '<param name="allowfullscreen" value="true" />';
		$player .= '<param name="wmode" value="transparent" />';
		$player .= '<param name="autoplay" value="false" />';
		$player .= '</object>';
	}

	return $player;
}

/**
 * Uses the video file extension to determine the mime type
 * Needs to support more extensions
 *
 * @since 0.1
 * @param string $video
 * @return string
 */
function get_the_object_mime_type( $file ) {

	if ( preg_match( '/\.flv$/', $file ) )
		$mime_type = 'application/x-shockwave-flash';

	elseif ( preg_match( '/\.mov$/', $file ) )
		$mime_type = 'video/quicktime';

	elseif ( preg_match( '/\.asf$/', $file ) || preg_match( '/\.wmv$/', $file ) )
		$mime_type = 'video/x-ms-wmv';

	elseif ( preg_match( '/\.mpg$/', $file ) || preg_match( '/\.mpeg$/', $file ) )
		$mime_type = 'audio/mpeg';

	elseif ( preg_match( '/\.wma$/', $file ) )
		$mime_type = 'audio/wma';

	return $mime_type;
}

/**
 * Catchall function for getting objects.
 *
 * @since 0.1
 * @param array $args
 * @return string
 */
function get_the_video( $args = array() ) {
	get_the_object( $args );
}

?>