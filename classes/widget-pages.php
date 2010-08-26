<?php
/**
 * Pages Widget
 *
 * Replaces the default WordPress Pages widget.
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * In 0.6, converted functions to a class that extends WP 2.8's widget class.
 *
 * @package Hybrid
 * @subpackage Widgets
 */

/**
 * Output of the Pages widget.
 * Arguments are parameters of the wp_list_pages() function.
 * @link http://codex.wordpress.org/Template_Tags/wp_list_pages
 *
 * @since 0.6
 */
class Hybrid_Widget_Pages extends WP_Widget {

	var $prefix;
	var $textdomain;

	function Hybrid_Widget_Pages() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'pages', 'description' => __( 'An advanced widget that gives you total control over the output of your page links.', $this->textdomain) );
		$control_ops = array( 'width' => 800, 'height' => 350, 'id_base' => "{$this->prefix}-pages" );
		$this->WP_Widget( "{$this->prefix}-pages", __( 'Pages', $this->textdomain), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$args = array();

		$args['sort_column'] = $instance['sort_column'];
		$args['sort_order'] = $instance['sort_order'];
		$args['depth'] = intval( $instance['depth'] );
		$args['child_of'] = intval( $instance['child_of'] );
		$args['meta_key'] = $instance['meta_key'];
		$args['meta_value'] = $instance['meta_value'];
		$args['authors'] = ( isset( $instance['authors'] ) ? join( ', ', $instance['authors'] ) : '' );
		$args['include'] = ( isset( $instance['include'] ) ? join( ', ', $instance['include'] ) : '' );
		$args['exclude'] = ( isset( $instance['exclude'] ) ? join( ', ', $instance['exclude'] ) : '' );
		$args['exclude_tree'] = $instance['exclude_tree'];
		$args['link_before'] = $instance['link_before'];
		$args['link_after'] = $instance['link_after'];
		$args['date_format'] = $instance['date_format'];
		$args['show_date'] = $instance['show_date'];
		$args['number'] = intval( $instance['number'] );
		$args['offset'] = intval( $instance['offset'] );
		$args['hierarchical'] = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : false;
		$args['title_li'] = false;
		$args['echo'] = false;

		/* Open the output of the widget. */
		echo $before_widget;

		/* If there is a title given, add it along with the $before_title and $after_title variables. */
		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Output the page list. */
		echo '<ul class="xoxo pages">' . str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages( $args ) ) . '</ul>';

		/* Close the output of the widget. */
		echo $after_widget;
	}

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

	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Pages', $this->textdomain),
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
		$instance = wp_parse_args( (array) $instance, $defaults );

		$posts = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'post_mime_type' => '', 'orderby' => 'title', 'order' => 'ASC', 'numberposts' => -1 ) );
		$authors = array();
		foreach ( $posts as $post )
			$authors[$post->post_author] = get_the_author_meta( 'display_name', $post->post_author );

		$sort_order = array( 'ASC' => __( 'Ascending', $this->textdomain ), 'DESC' => __( 'Descending', $this->textdomain ) );
		$sort_column = array( 'post_author' => __( 'Author', $this->textdomain ), 'post_date' => __( 'Date', $this->textdomain ), 'ID' => __( 'ID', $this->textdomain ), 'menu_order' => __( 'Menu Order', $this->textdomain ), 'post_modified' => __( 'Modified', $this->textdomain ), 'post_name' => __( 'Slug', $this->textdomain ), 'post_title' => __( 'Title', $this->textdomain ) );
		$show_date = array( '' => '', 'created' => __( 'Created', $this->textdomain ), 'modified' => __( 'Modified', $this->textdomain ) );
		$meta_key = array_merge( array( '' ), (array) get_meta_keys() );
		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_order' ); ?>"><code>sort_order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'sort_order' ); ?>" name="<?php echo $this->get_field_name( 'sort_order' ); ?>">
				<?php foreach ( $sort_order as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['sort_order'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_column' ); ?>"><code>sort_column</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'sort_column' ); ?>" name="<?php echo $this->get_field_name( 'sort_column' ); ?>">
				<?php foreach ( $sort_column as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['sort_column'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo $instance['depth']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><code>offset</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo $instance['offset']; ?>"  />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><code>child_of</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo $instance['child_of']; ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3">

		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $posts as $post ) { ?>
					<option value="<?php echo $post->ID; ?>" <?php echo ( in_array( $post->ID, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_attr( $post->post_title ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $posts as $post ) { ?>
					<option value="<?php echo $post->ID; ?>" <?php echo ( in_array( $post->ID, (array) $instance['exclude'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_attr( $post->post_title ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"><code>exclude_tree</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo $instance['exclude_tree']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><code>meta_key</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>">
				<?php foreach ( $meta_key as $meta ) { ?>
					<option value="<?php echo $meta; ?>" <?php selected( $instance['meta_key'], $meta ); ?>><?php echo $meta; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_value' ); ?>"><code>meta_value</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo $instance['meta_value']; ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-3 column-last">

		<p>
			<label for="<?php echo $this->get_field_id( 'authors' ); ?>"><code>authors</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'authors' ); ?>" name="<?php echo $this->get_field_name( 'authors' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $authors as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php echo ( in_array( $option_value, (array) $instance['authors'] ) ? 'selected="selected"' : '' ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_before' ); ?>"><code>link_before</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo $instance['link_before']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_after' ); ?>"><code>link_after</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo $instance['link_after']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><code>show_date</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>">
				<?php foreach ( $show_date as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['show_date'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'date_format' ); ?>"><code>date_format</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo $instance['date_format']; ?>" />
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