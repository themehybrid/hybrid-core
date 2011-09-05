<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 * @package Hybrid
 * @subpackage Classes
 */

/**
 * Categories Widget Class
 *
 * @since 0.6
 * @link http://codex.wordpress.org/Template_Tags/wp_list_categories
 * @link http://themehybrid.com/themes/hybrid/widgets
 */
class Hybrid_Widget_Categories extends WP_Widget {

	/**
	 * Prefix for the widget.
	 * @since 0.7.0
	 */
	var $prefix;

	/**
	 * Textdomain for the widget.
	 * @since 0.7.0
	 */
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set the widget prefix. */
		$this->prefix = hybrid_get_prefix();

		/* Set the widget textdomain. */
		$this->textdomain = hybrid_get_textdomain();

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'categories',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your category links.', $this->textdomain )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-categories',		// $this->id_base
			__( 'Categories', $this->textdomain ),	// $this->name
			$widget_options,			// $this->widget_options
			$control_options			// $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for wp_list_categories(). */
		$args = array(
			'taxonomy' =>		$instance['taxonomy'],
			'style' =>			$instance['style'],
			'orderby' =>		$instance['orderby'],
			'order' =>		$instance['order'],
			'include' =>		!empty( $instance['include'] ) ? join( ', ', $instance['include'] ) : '',
			'exclude' =>		!empty( $instance['exclude'] ) ? join( ', ', $instance['exclude'] ) : '',
			'exclude_tree' =>		$instance['exclude_tree'],
			'depth' =>		intval( $instance['depth'] ),
			'number' =>		intval( $instance['number'] ),
			'child_of' =>		intval( $instance['child_of'] ),
			'current_category' =>	intval( $instance['current_category'] ),
			'feed' =>			$instance['feed'],
			'feed_type' =>		$instance['feed_type'],
			'feed_image' =>		esc_url( $instance['feed_image'] ),
			'search' =>		$instance['search'],
			'hierarchical' =>		!empty( $instance['hierarchical'] ) ? true : false,
			'use_desc_for_title' =>	!empty( $instance['use_desc_for_title'] ) ? true : false,
			'show_last_update' =>	!empty( $instance['show_last_update'] ) ? true : false,
			'show_count' =>		!empty( $instance['show_count'] ) ? true : false,
			'hide_empty' =>		!empty( $instance['hide_empty'] ) ? true : false,
			'title_li' =>		false,
			'echo' =>			false
		);

		/* Output the theme's widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Get the categories list. */
		$categories = str_replace( array( "\r", "\n", "\t" ), '', wp_list_categories( $args ) );

		/* If 'list' is the user-selected style, wrap the categories in an unordered list. */
		if ( 'list' == $args['style'] )
			$categories = '<ul class="xoxo categories">' . $categories . '</ul><!-- .xoxo .categories -->';

		/* Output the categories list. */
		echo $categories;

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		/* If new taxonomy is chosen, reset includes and excludes. */
		if ( $instance['taxonomy'] !== $old_instance['taxonomy'] && '' !== $old_instance['taxonomy'] ) {
			$instance['include'] = array();
			$instance['exclude'] = array();
		}

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['exclude_tree'] = strip_tags( $new_instance['exclude_tree'] );
		$instance['depth'] = strip_tags( $new_instance['depth'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['child_of'] = strip_tags( $new_instance['child_of'] );
		$instance['current_category'] = strip_tags( $new_instance['current_category'] );
		$instance['feed'] = strip_tags( $new_instance['feed'] );
		$instance['feed_image'] = strip_tags( $new_instance['feed_image'] );
		$instance['search'] = strip_tags( $new_instance['search'] );

		$instance['hierarchical'] = ( isset( $new_instance['hierarchical'] ) ? 1 : 0 );
		$instance['use_desc_for_title'] = ( isset( $new_instance['use_desc_for_title'] ) ? 1 : 0 );
		$instance['show_last_update'] = ( isset( $new_instance['show_last_update'] ) ? 1 : 0 );
		$instance['show_count'] = ( isset( $new_instance['show_count'] ) ? 1 : 0 );
		$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Categories', $this->textdomain ),
			'taxonomy' => 'category',
			'style' => 'list',
			'include' => array(),
			'exclude' => array(),
			'exclude_tree' => '',
			'child_of' => '',
			'current_category' => '',
			'search' => '',
			'hierarchical' => true,
			'hide_empty' => true,
			'order' => 'ASC',
			'orderby' => 'name',
			'depth' => 0,
			'number' => '',
			'feed' => '',
			'feed_type' => '',
			'feed_image' => '',
			'use_desc_for_title' => false,
			'show_last_update' => false,
			'show_count' => false,
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/* <select> element options. */
		$taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );
		$terms = get_terms( $instance['taxonomy'] );
		$style = array( 'list' => esc_attr__( 'List', $this->textdomain ), 'none' => esc_attr__( 'None', $this->textdomain ) );
		$order = array( 'ASC' => esc_attr__( 'Ascending', $this->textdomain ), 'DESC' => esc_attr__( 'Descending', $this->textdomain ) );
		$orderby = array( 'count' => esc_attr__( 'Count', $this->textdomain ), 'ID' => esc_attr__( 'ID', $this->textdomain ), 'name' => esc_attr__( 'Name', $this->textdomain ), 'slug' => esc_attr__( 'Slug', $this->textdomain ), 'term_group' => esc_attr__( 'Term Group', $this->textdomain ) );
		$feed_type = array( '' => '', 'atom' => esc_attr__( 'Atom', $this->textdomain ), 'rdf' => esc_attr__( 'RDF', $this->textdomain ), 'rss' => esc_attr__( 'RSS', $this->textdomain ), 'rss2' => esc_attr__( 'RSS 2.0', $this->textdomain ) );

		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
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
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['exclude'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"><code>exclude_tree</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo esc_attr( $instance['exclude_tree'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'current_category' ); ?>"><code>current_category</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'current_category' ); ?>" name="<?php echo $this->get_field_name( 'current_category' ); ?>" value="<?php echo esc_attr( $instance['current_category'] ); ?>" />
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
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo esc_attr( $instance['feed_image'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hierarchical'], true ); ?> id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" /> <?php _e( 'Hierarchical?', $this->textdomain ); ?> <code>hierarchical</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['use_desc_for_title'], true ); ?> id="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>" name="<?php echo $this->get_field_name( 'use_desc_for_title' ); ?>" /> <?php _e( 'Use description?', $this->textdomain ); ?> <code>use_desc_for_title</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_last_update' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_last_update'], true ); ?> id="<?php echo $this->get_field_id( 'show_last_update' ); ?>" name="<?php echo $this->get_field_name( 'show_last_update' ); ?>" /> <?php _e( 'Show last update?', $this->textdomain ); ?> <code>show_last_update</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" /> <?php _e( 'Show count?', $this->textdomain ); ?> <code>show_count</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" /> <?php _e( 'Hide empty?', $this->textdomain ); ?> <code>hide_empty</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>