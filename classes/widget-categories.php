<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Categories Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Categories extends WP_Widget {

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
			'classname'   => 'widget-categories widget_categories',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your category links.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-categories',
			__( 'Categories', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up the defaults. */
		$this->defaults = array(
			'title'              => esc_attr__( 'Categories', 'hybrid-core' ),
			'taxonomy'           => 'category',
			'style'              => 'list',
			'include'            => '',
			'exclude'            => '',
			'exclude_tree'       => '',
			'child_of'           => '',
			'current_category'   => '',
			'search'             => '',
			'hierarchical'       => true,
			'hide_empty'         => true,
			'order'              => 'ASC',
			'orderby'            => 'name',
			'depth'              => 0,
			'number'             => '',
			'feed'               => '',
			'feed_type'          => '',
			'feed_image'         => '',
			'use_desc_for_title' => false,
			'show_count'         => false,
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

		/* Set the $args for wp_list_categories() to the $instance array. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Set the $title_li and $echo arguments to false. */
		$args['title_li'] = false;
		$args['echo']     = false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Get the categories list. */
		$categories = str_replace( array( "\r", "\n", "\t" ), '', wp_list_categories( $args ) );

		/* If 'list' is the user-selected style, wrap the categories in an unordered list. */
		if ( 'list' == $args['style'] )
			$categories = '<ul class="xoxo categories">' . $categories . '</ul><!-- .xoxo .categories -->';

		/* If no style is given, wrap in a <p> tag for formatting. */
		else
			$categories = '<p class="categories style-none">' . $categories . '</p>';

		/* Output the categories list. */
		echo $categories;

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

		/* If new taxonomy is chosen, reset includes and excludes. */
		if ( $new_instance['taxonomy'] !== $old_instance['taxonomy'] )
			$new_instance['include'] = $new_instance['exclude'] = '';

		/* Sanitize key. */
		$instance['taxonomy'] = sanitize_key( $new_instance['taxonomy'] );

		/* Strip tags. */
		$instance['title']            = strip_tags( $new_instance['title']            );
		$instance['search']           = strip_tags( $new_instance['search']           );
		$instance['feed']             = strip_tags( $new_instance['feed']             );

		/* Whitelist options. */
		$order   = array( 'ASC', 'DESC' );
		$orderby = array( 'count', 'ID', 'name', 'slug', 'term_group' );
		$style   = array( 'list', 'none' );
		$feed_type = array( '', 'atom', 'rdf', 'rss', 'rss2' );

		$instance['order']     = in_array( $new_instance['order'],     $order )     ? $new_instance['order']     : 'ASC';
		$instance['orderby']   = in_array( $new_instance['orderby'],   $orderby )   ? $new_instance['orderby']   : 'name';
		$instance['style']     = in_array( $new_instance['style'],     $style )     ? $new_instance['style']     : 'list';
		$instance['feed_type'] = in_array( $new_instance['feed_type'], $feed_type ) ? $new_instance['feed_type'] : '';

		/* Integers. */
		$instance['number']           = intval( $new_instance['number']           );
		$instance['depth']            = absint( $new_instance['depth']            );
		$instance['child_of']         = absint( $new_instance['child_of']         );
		$instance['current_category'] = absint( $new_instance['current_category'] );

		/* Only allow integers and commas. */
		$instance['include']      = preg_replace( '/[^0-9,]/', '', $new_instance['include']      );
		$instance['exclude']      = preg_replace( '/[^0-9,]/', '', $new_instance['exclude']      );
		$instance['exclude_tree'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude_tree'] );

		/* URLs. */
		$instance['feed_image'] = esc_url_raw( $new_instance['feed_image'] );

		/* Checkboxes. */
		$instance['hierarchical']       = isset( $new_instance['hierarchical'] )       ? 1 : 0;
		$instance['use_desc_for_title'] = isset( $new_instance['use_desc_for_title'] ) ? 1 : 0;
		$instance['show_count']         = isset( $new_instance['show_count'] )         ? 1 : 0;
		$instance['hide_empty']         = isset( $new_instance['hide_empty'] )         ? 1 : 0;

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
		$terms      = get_terms( $instance['taxonomy'] );

		$style = array( 
			'list' => esc_attr__( 'List', 'hybrid-core' ), 
			'none' => esc_attr__( 'None', 'hybrid-core' ) 
		);

		$order = array( 
			'ASC'  => esc_attr__( 'Ascending',  'hybrid-core' ), 
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' ) 
		);

		$orderby = array( 
			'count'      => esc_attr__( 'Count',      'hybrid-core' ), 
			'ID'         => esc_attr__( 'ID',         'hybrid-core' ), 
			'name'       => esc_attr__( 'Name',       'hybrid-core' ), 
			'slug'       => esc_attr__( 'Slug',       'hybrid-core' ), 
			'term_group' => esc_attr__( 'Term Group', 'hybrid-core' ) 
		);

		$feed_type = array( 
			''     => '', 
			'atom' => esc_attr__( 'Atom',    'hybrid-core' ), 
			'rdf'  => esc_attr__( 'RDF',     'hybrid-core' ), 
			'rss'  => esc_attr__( 'RSS',     'hybrid-core' ), 
			'rss2' => esc_attr__( 'RSS 2.0', 'hybrid-core' ) 
		);

		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><code>taxonomy</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
				<?php foreach ( $taxonomies as $taxonomy ) { ?>
					<option value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php selected( $instance['taxonomy'], $taxonomy->name ); ?>><?php echo esc_html( $taxonomy->labels->singular_name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><code>style</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<?php foreach ( $style as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['style'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"><code>exclude_tree</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo esc_attr( $instance['exclude_tree'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'current_category' ); ?>"><code>current_category</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'current_category' ); ?>" name="<?php echo $this->get_field_name( 'current_category' ); ?>" value="<?php echo esc_attr( $instance['current_category'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><code>search</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'feed' ); ?>"><code>feed</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="<?php echo esc_attr( $instance['feed'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_type' ); ?>"><code>feed_type</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'feed_type' ); ?>" name="<?php echo $this->get_field_name( 'feed_type' ); ?>">
				<?php foreach ( $feed_type as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['feed_type'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"><code>feed_image</code></label>
			<input type="url" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo esc_attr( $instance['feed_image'] ); ?>" placeholder="<?php echo esc_attr( home_url( 'images/example.png' ) ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hierarchical'], true ); ?> id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" /> <?php _e( 'Hierarchical?', 'hybrid-core' ); ?> <code>hierarchical</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['use_desc_for_title'], true ); ?> id="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>" name="<?php echo $this->get_field_name( 'use_desc_for_title' ); ?>" /> <?php _e( 'Use description?', 'hybrid-core' ); ?> <code>use_desc_for_title</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" /> <?php _e( 'Show count?', 'hybrid-core' ); ?> <code>show_count</code></label>
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
