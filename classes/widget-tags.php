<?php
/**
 * The Tags widget replaces the default WordPress Tag Cloud widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_tag_cloud() function.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Tags Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Tags extends WP_Widget {

	/**
	 * Default arguments for the widget settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = array();

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'widget-tags widget_tag_cloud',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your tags.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-tags',
			__( 'Tags', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up the defaults. */
		$topic_count_text = _n_noop( '%s topic', '%s topics', 'hybrid-core' );

		$this->defaults = array(
			'title'                      => esc_attr__( 'Tags', 'hybrid-core' ),
			'order'                      => 'ASC',
			'orderby'                    => 'name',
			'format'                     => 'flat',
			'include'                    => '',
			'exclude'                    => '',
			'unit'                       => 'pt',
			'smallest'                   => 8,
			'largest'                    => 22,
			'link'                       => 'view',
			'number'                     => 25,
			'separator'                  => ' ',
			'child_of'                   => '',
			'parent'                     => '',
			'taxonomy'                   => array( 'post_tag' ),
			'hide_empty'                 => 1,
			'pad_counts'                 => false,
			'search'                     => '',
			'name__like'                 => '',
			'single_text'                => $topic_count_text['singular'],
			'multiple_text'              => $topic_count_text['plural'],
			'topic_count_text_callback'  => '',
			'topic_count_scale_callback' => 'default_topic_count_scale',
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since  0.6.0
	 * @access public
	 * @param  array  $sidebar
	 * @param  array  $instance
	 * @return void
	 */
	function widget( $sidebar, $instance ) {

		/* Set the $args for wp_tag_cloud() to the $instance array. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Make sure empty callbacks aren't passed for custom functions. */
		$args['topic_count_text_callback']  = !empty( $args['topic_count_text_callback']  ) ? $args['topic_count_text_callback']  : '';
		$args['topic_count_scale_callback'] = !empty( $args['topic_count_scale_callback'] ) ? $args['topic_count_scale_callback'] : 'default_topic_count_scale';

		/* If the separator is empty, set it to the default new line. */
		$args['separator'] = !empty( $args['separator'] ) ? $args['separator'] : "\n";

		/* Overwrite the echo argument. */
		$args['echo'] = false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Get the tag cloud. */
		$tags = str_replace( array( "\r", "\n", "\t" ), ' ', wp_tag_cloud( $args ) );

		/* If $format should be flat, wrap it in the <p> element. */
		if ( 'flat' == $args['format'] ) {
			$classes = array( 'term-cloud' );

			foreach ( $args['taxonomy'] as $tax )
				$classes[] = sanitize_html_class( "{$tax}-cloud" );

			$tags = '<p class="' . join( $classes, ' ' ) . '">' . $tags . '</p>';
		}

		/* Output the tag cloud. */
		echo $tags;

		/* Close the sidebar's widget wrapper. */
		echo $sidebar['after_widget'];
	}

	/**
	 * The update callback for the widget control options.  This method is used to sanitize and/or
	 * validate the options before saving them into the database.
	 *
	 * @since  0.6.0
	 * @access public
	 * @param  array  $new_instance
	 * @param  array  $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		/* Strip tags. */
		$instance['title']      = strip_tags( $new_instance['title']      );
		$instance['separator']  = strip_tags( $new_instance['separator']  );
		$instance['name__like'] = strip_tags( $new_instance['name__like'] );
		$instance['search']     = strip_tags( $new_instance['search']     );
		$instance['single_text']     = strip_tags( $new_instance['single_text']     );
		$instance['multiple_text']     = strip_tags( $new_instance['multiple_text']     );

		/* Sanitize key. */
		$instance['taxonomy'] = array_map( 'sanitize_key', $new_instance['taxonomy'] );

		/* Whitelist options. */
		$order   = array( 'ASC', 'DESC', 'RAND' );
		$orderby = array( 'count', 'name' );
		$format  = array( 'flat', 'list' );
		$unit    = array( 'pt', 'px', 'em', '%' );
		$link    = array( 'view', 'edit' );

		$instance['order']   = in_array( $new_instance['order'],   $order )   ? $new_instance['order']   : 'ASC';
		$instance['orderby'] = in_array( $new_instance['orderby'], $orderby ) ? $new_instance['orderby'] : 'name';
		$instance['format']  = in_array( $new_instance['format'],  $format )  ? $new_instance['format']  : 'view';
		$instance['unit']    = in_array( $new_instance['unit'],    $unit )    ? $new_instance['unit']    : 'pt';
		$instance['link']    = in_array( $new_instance['link'],    $link )    ? $new_instance['link']    : 'view';

		/* Integers. */
		$instance['number']   = intval( $new_instance['number']   );
		$instance['smallest'] = absint( $new_instance['smallest'] );
		$instance['largest']  = absint( $new_instance['largest']  );
		$instance['child_of'] = absint( $new_instance['child_of'] );
		$instance['parent']   = absint( $new_instance['parent']   );

		/* Only allow integers and commas. */
		$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
		$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );

		/* Check if function exists. */
		$instance['topic_count_text_callback']  = empty( $new_instance['fallback_cb'] ) || function_exists( $new_instance['topic_count_text_callback'] )  ? $new_instance['topic_count_text_callback']  : 'default_topic_count_text';
		$instance['topic_count_scale_callback'] = empty( $new_instance['fallback_cb'] ) || function_exists( $new_instance['topic_count_scale_callback'] ) ? $new_instance['topic_count_scale_callback'] : 'default_topic_count_scale';

		/* Checkboxes. */
		$instance['pad_counts'] = isset( $new_instance['pad_counts'] ) ? 1 : 0;
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? 1 : 0;

		/* Return sanitized options. */
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since  0.6.0
	 * @access public
	 * @param  array  $instance
	 * @param  void
	 */
	function form( $instance ) {

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		/* <select> element options. */
		$taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );

		$link = array( 
			'view' => esc_attr__( 'View', 'hybrid-core' ), 
			'edit' => esc_attr__( 'Edit', 'hybrid-core' ) 
		);

		$format = array( 
			'flat' => esc_attr__( 'Flat', 'hybrid-core' ), 
			'list' => esc_attr__( 'List', 'hybrid-core' ) 
		);

		$order = array( 
			'ASC'  => esc_attr__( 'Ascending',  'hybrid-core' ), 
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' ), 
			'RAND' => esc_attr__( 'Random',     'hybrid-core' ) 
		);

		$orderby = array( 
			'count' => esc_attr__( 'Count', 'hybrid-core' ), 
			'name'  => esc_attr__( 'Name',  'hybrid-core' ) 
		);

		$unit = array( 
			'pt' => 'pt', 
			'px' => 'px', 
			'em' => 'em', 
			'%'  => '%' 
		);

		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><code>taxonomy</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $taxonomies as $taxonomy ) { ?>
					<option value="<?php echo $taxonomy->name; ?>" <?php selected( in_array( $taxonomy->name, (array)$instance['taxonomy'] ) ); ?>><?php echo $taxonomy->labels->singular_name; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><code>format</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
				<?php foreach ( $format as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['format'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><code>link</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
				<?php foreach ( $link as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['link'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" placeholder="25" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><code>largest</code></label>
			<input type="number" class="smallfat code" size="5" min="1" id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" value="<?php echo esc_attr( $instance['largest'] ); ?>" placeholder="22" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><code>smallest</code></label>
			<input type="number" class="smallfat code" size="5" min="1" id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" value="<?php echo esc_attr( $instance['smallest'] ); ?>" placeholder="8" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'unit' ); ?>"><code>unit</code></label> 
			<select class="smallfat" id="<?php echo $this->get_field_id( 'unit' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>">
				<?php foreach ( $unit as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['unit'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><code>separator</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" value="<?php echo esc_attr( $instance['separator'] ); ?>" placeholder="&thinsp;&ndash;&thinsp;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'parent' ); ?>"><code>parent</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'parent' ); ?>" name="<?php echo $this->get_field_name( 'parent' ); ?>" value="<?php echo esc_attr( $instance['parent'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><code>search</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'name__like' ); ?>"><code>name__like</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'name__like' ); ?>" name="<?php echo $this->get_field_name( 'name__like' ); ?>" value="<?php echo esc_attr( $instance['name__like'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'single_text' ); ?>"><code>single_text</code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'single_text' ); ?>" name="<?php echo $this->get_field_name( 'single_text' ); ?>" value="<?php echo esc_attr( $instance['single_text'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['single_text'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'multiple_text' ); ?>"><code>multiple_text</code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'multiple_text' ); ?>" name="<?php echo $this->get_field_name( 'multiple_text' ); ?>" value="<?php echo esc_attr( $instance['multiple_text'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['multiple_text'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'topic_count_text_callback' ); ?>"><code>topic_count_text_callback</code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'topic_count_text_callback' ); ?>" name="<?php echo $this->get_field_name( 'topic_count_text_callback' ); ?>" value="<?php echo esc_attr( $instance['topic_count_text_callback'] ); ?>" placeholder="default_topic_count_text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'topic_count_scale_callback' ); ?>"><code>topic_count_scale_callback</code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'topic_count_scale_callback' ); ?>" name="<?php echo $this->get_field_name( 'topic_count_scale_callback' ); ?>" value="<?php echo esc_attr( $instance['topic_count_scale_callback'] ); ?>" placeholder="default_topic_count_scale" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'pad_counts' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['pad_counts'], true ); ?> id="<?php echo $this->get_field_id( 'pad_counts' ); ?>" name="<?php echo $this->get_field_name( 'pad_counts' ); ?>" /> <?php _e( 'Pad counts?', 'hybrid-core' ); ?> <code>pad_counts</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" /> <?php _e( 'Hide empty?', 'hybrid-core' ); ?> <code>hide_empty</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
