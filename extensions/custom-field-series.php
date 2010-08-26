<?php
/**
 * Functions for displaying a series of posts linked together
 * by a custom field called 'Series'.  Each post is listed that
 * belong to the same series of posts.
 *
 * @copyright 2007 - 2010
 * @version 0.2.1
 * @author Justin Tadlock
 * @link http://justintadlock.com/archives/2007/11/01/wordpress-custom-fields-listing-a-series-of-posts
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CustomFieldSeries
 */

/**
 * Grabs series by custom field.  Checks for other articles in the series.
 * Series identified custom field key 'Series' and unique value.
 *
 * @todo Fix the multiple hyphens in the series class.
 * @todo Allow filtering of title.
 *
 * @since 0.1
 * @param array $args Array of arguments.
 */
function custom_field_series( $args = array() ) {
	global $post;

	$textdomain = hybrid_get_textdomain();

	$series = '';

	$series_meta = get_metadata( 'post', $post->ID, 'Series', true );

	if ( $series_meta ) {

		$defaults = array(
			'order' => 'DESC',
			'orderby' => 'ID',
			'include' => '',
			'exclude' => '',
			'post_type' => 'any',
			'numberposts' => -1,
			'meta_key' => 'Series',
			'meta_value' => $series_meta,
			'echo' => true
		);

		$args = apply_filters( 'custom_field_series_args', $args );

		$args = wp_parse_args( $args, $defaults );

		$series_posts = get_posts( $args );

		if ( $series_posts ) {

			$class = str_replace( array( '_', ' ', '&nbsp;' ) , '-', $series_meta );
			$class = preg_replace('/[^A-Za-z0-9-]/', '', $class );
			$class = strtolower( $class );

			$series = '<div class="series series-' . $class . '">';
			$series .= '<h4 class="series-title">' . __( 'Articles in this series', $textdomain) . '</h4>';
			$series .= '<ul>';

			foreach ( $series_posts as $serial ) {

				if ( $serial->ID == $post->ID )
					$series .= '<li class="current-post">' . $serial->post_title . '</li>';

				else
					$series .= '<li><a href="' . get_permalink( $serial->ID ) . '" title="' . esc_attr( $serial->post_title ) . '">' . $serial->post_title . '</a></li>';
			}

			$series .= '</ul></div>';
		}
	}

	$series = apply_filters( 'custom_field_series', $series );

	if ( !empty( $args['echo'] ) && $series )
		echo $series;

	elseif ( $series )
		return $series;
}

?>