<?php
/**
 * The Pages widget replaces the default WordPress Pages widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_pages() function.
 *
 * @package Hybrid
 * @subpackage Widgets
 */

/**
 * Pages Widget Class
 *
 * @since 0.6.0
 * @link http://codex.wordpress.org/Template_Tags/wp_list_pages
 * @link http://themehybrid.com/themes/hybrid/widgets
 */
class Hybrid_Widget_Pages extends WP_Widget {

	/**
	 * Prefix for the widget.
	 * @since 0.7.0
	 */
	var $prefix;

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
			'classname' => 'pages',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your page links.', $this->textdomain )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-pages',			// $this->id_base
			__( 'Pages', $this->textdomain),	// $this->name
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

		/* Set up the arguments for the wp_list_pages() function. */
		$args = array(
			'sort_column' => 		$instance['sort_column'],
			'sort_order' =>		$instance['sort_order'],
			'depth' =>		intval( $instance['depth'] ),
			'child_of' =>		intval( $instance['child_of'] ),
			'meta_key' =>		$instance['meta_key'],
			'meta_value' =>		$instance['meta_value'],
			'authors' =>		!empty( $instance['authors'] ) ? join( ', ', $instance['authors'] ) : '',
			'include' =>		!empty( $instance['include'] ) ? join( ', ', $instance['include'] ) : '',
			'exclude' =>		!empty( $instance['exclude'] ) ? join( ', ', $instance['exclude'] ) : '',
			'exclude_tree' =>		$instance['exclude_tree'],
			'link_before' =>		$instance['link_before'],
			'link_after' =>		$instance['link_after'],
			'date_format' =>		$instance['date_format'],
			'show_date' =>		$instance['show_date'],
			'number' =>		intval( $instance['number'] ),
			'offset' =>		intval( $instance['offset'] ),
			'hierarchical' =>		!empty( $instance['hierarchical'] ) ? true : false,
			'title_li' =>		false,
			'echo' =>			false
		);

		/* Open the output of the widget. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Output the page list. */
		echo '<ul class="xoxo pages">' . str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages( $args ) ) . '</ul>';

		/* Close the output of the widget. */
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

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['depth'] = strip_tags( $new_instance['depth'] );
		$instance['child_of'] = strip_tags( $new_instance['child_of'] );
		$instance['meta_key'] = strip_tags( $new_instance['meta_key'] );
		$instance['meta_value'] = strip_tags( $new_instance['meta_value'] );
		$instance['exclude_tree'] = strip_tags( $new_instance['exclude_tree'] );
		$instance['date_format'] = strip_tags( $new_instance['date_format'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['offset'] = strip_tags( $new_instance['offset'] );
		$instance['sort_column'] = $new_instance['sort_column'];
		$instance['sort_order'] = $new_instance['sort_order'];
		$instance['show_date'] = $new_instance['show_date'];
		$instance['link_before'] = $new_instance['link_before'];
		$instance['link_after'] = $new_instance['link_after'];

		$instance['hierarchical'] = ( isset( $new_instance['hierarchical'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Pages', $this->textdomain),
			'depth' => 0,
			'number' => '',
			'offset' => '',
			'child_of' => '',
			'include' => array(),
			'exclude' => array(),
			'exclude_tree' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => array(),
			'link_before' => '',
			'link_after' => '',
			'show_date' => '',
			'hierarchical' => true,
			'sort_column' => 'post_title',
			'sort_order' => 'ASC',
			'date_format' => get_option( 'date_format' )
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$posts = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'post_mime_type' => '', 'orderby' => 'title', 'order' => 'ASC', 'numberposts' => -1 ) );
		$authors = array();
		foreach ( $posts as $post )
			$authors[$post->post_author] = get_the_author_meta( 'display_name', $post->post_author );

		$sort_order = array( 'ASC' => esc_attr__( 'Ascending', $this->textdomain ), 'DESC' => esc_attr__( 'Descending', $this->textdomain ) );
		$sort_column = array( 'post_author' => esc_attr__( 'Author', $this->textdomain ), 'post_date' => esc_attr__( 'Date', $this->textdomain ), 'ID' => esc_attr__( 'ID', $this->textdomain ), 'menu_order' => esc_attr__( 'Menu Order', $this->textdomain ), 'post_modified' => esc_attr__( 'Modified', $this->textdomain ), 'post_name' => esc_attr__( 'Slug', $this->textdomain ), 'post_title' => esc_attr__( 'Title', $this->textdomain ) );
		$show_date = array( '' => '', 'created' => esc_attr__( 'Created', $this->textdomain ), 'modified' => esc_attr__( 'Modified', $this->textdomain ) );
		$meta_key = array_merge( array( '' ), (array) get_meta_keys() );

		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_order' ); ?>"><code>sort_order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'sort_order' ); ?>" name="<?php echo $this->get_field_name( 'sort_order' ); ?>">
				<?php foreach ( $sort_order as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['sort_order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_column' ); ?>"><code>sort_column</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'sort_column' ); ?>" name="<?php echo $this->get_field_name( 'sort_column' ); ?>">
				<?php foreach ( $sort_column as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['sort_column'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
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
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><code>offset</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo esc_attr( $instance['offset'] ); ?>"  />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $posts as $post ) { ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>" <?php echo ( in_array( $post->ID, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $post->post_title ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $posts as $post ) { ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>" <?php echo ( in_array( $post->ID, (array) $instance['exclude'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $post->post_title ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"><code>exclude_tree</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo esc_attr( $instance['exclude_tree'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><code>meta_key</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>">
				<?php foreach ( $meta_key as $meta ) { ?>
					<option value="<?php echo esc_attr( $meta ); ?>" <?php selected( $instance['meta_key'], $meta ); ?>><?php echo esc_html( $meta ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_value' ); ?>"><code>meta_value</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo esc_attr( $instance['meta_value'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'authors' ); ?>"><code>authors</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'authors' ); ?>" name="<?php echo $this->get_field_name( 'authors' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $authors as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php echo ( in_array( $option_value, (array) $instance['authors'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_before' ); ?>"><code>link_before</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo esc_attr( $instance['link_before'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_after' ); ?>"><code>link_after</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo esc_attr( $instance['link_after'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><code>show_date</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>">
				<?php foreach ( $show_date as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['show_date'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'date_format' ); ?>"><code>date_format</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo esc_attr( $instance['date_format'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hierarchical'], true ); ?> id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" /> <?php _e( 'Hierarchical?', $this->textdomain); ?> <code>hierarchical</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>