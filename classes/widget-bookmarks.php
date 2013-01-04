<?php
/**
 * The Bookmarks widget replaces the default WordPress Links widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_bookmarks() function.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Bookmarks Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Bookmarks extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'bookmarks',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your bookmarks (links).', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-bookmarks',               // $this->id_base
			__( 'Bookmarks', 'hybrid-core' ), // $this->name	
			$widget_options,                  // $this->widget_options
			$control_options                  // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.6.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Set up the $before_widget ID for multiple widgets created by the bookmarks widget. */
		if ( !empty( $instance['categorize'] ) )
			$before_widget = preg_replace( '/id="[^"]*"/','id="%id"', $before_widget );

		/* Add a class to $before_widget if one is set. */
		if ( !empty( $instance['class'] ) )
			$before_widget = str_replace( 'class="', 'class="' . esc_attr( $instance['class'] ) . ' ', $before_widget );

		/* Set the $args for wp_list_bookmarks() to the $instance array. */
		$args = $instance;

		/* wp_list_bookmarks() hasn't been updated in WP to use wp_parse_id_list(), so we have to pass strings for includes/excludes. */
		if ( !empty( $args['category'] ) && is_array( $args['category'] ) )
			$args['category'] = join( ', ', $args['category'] );

		if ( !empty( $args['exclude_category'] ) && is_array( $args['exclude_category'] ) )
			$args['exclude_category'] = join( ', ', $args['exclude_category'] );

		if ( !empty( $args['include'] ) && is_array( $args['include'] ) )
			$args['include'] = join( ',', $args['include'] );

		if ( !empty( $args['exclude'] ) && is_array( $args['exclude'] ) )
			$args['exclude'] = join( ',', $args['exclude'] );

		/* If no limit is given, set it to -1. */
		$args['limit'] = empty( $args['limit'] ) ? -1 : $args['limit'];

		/* Some arguments must be set to the sidebar arguments to be output correctly. */
		$args['title_li']        = apply_filters( 'widget_title', ( empty( $args['title_li'] ) ? __( 'Bookmarks', 'hybrid-core' ) : $args['title_li'] ), $instance, $this->id_base );
		$args['title_before']    = $before_title;
		$args['title_after']     = $after_title;
		$args['category_before'] = $before_widget;
		$args['category_after']  = $after_widget;
		$args['category_name']   = '';
		$args['echo']            = false;

		/* Output the bookmarks widget. */
		$bookmarks = str_replace( array( "\r", "\n", "\t" ), '', wp_list_bookmarks( $args ) );

		/* If no title is given and the bookmarks aren't categorized, add a wrapper <ul>. */
		if ( empty( $args['title_li'] ) && false === $args['categorize'] )
			$bookmarks = '<ul class="xoxo bookmarks">' . $bookmarks . '</ul>';

		/* Output the bookmarks. */
		echo $bookmarks;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		$instance['title_li'] = strip_tags( $new_instance['title_li'] );
		$instance['limit']    = strip_tags( $new_instance['limit'] );
		$instance['class']    = strip_tags( $new_instance['class'] );
		$instance['search']   = strip_tags( $new_instance['search'] );

		$instance['category_order']   = $new_instance['category_order'];
		$instance['category_orderby'] = $new_instance['category_orderby'];
		$instance['orderby']          = $new_instance['orderby'];
		$instance['order']            = $new_instance['order'];
		$instance['between']          = $new_instance['between'];
		$instance['link_before']      = $new_instance['link_before'];
		$instance['link_after']       = $new_instance['link_after'];

		$instance['categorize']       = ( isset( $new_instance['categorize'] ) ? 1 : 0 );
		$instance['hide_invisible']   = ( isset( $new_instance['hide_invisible'] ) ? 1 : 0 );
		$instance['show_private']     = ( isset( $new_instance['show_private'] ) ? 1 : 0 );
		$instance['show_rating']      = ( isset( $new_instance['show_rating'] ) ? 1 : 0 );
		$instance['show_updated']     = ( isset( $new_instance['show_updated'] ) ? 1 : 0 );
		$instance['show_images']      = ( isset( $new_instance['show_images'] ) ? 1 : 0 );
		$instance['show_name']        = ( isset( $new_instance['show_name'] ) ? 1 : 0 );
		$instance['show_description'] = ( isset( $new_instance['show_description'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title_li'         => esc_attr__( 'Bookmarks', 'hybrid-core' ),
			'categorize'       => true,
			'category_order'   => 'ASC',
			'category_orderby' => 'name',
			'category'         => array(),
			'exclude_category' => array(),
			'limit'            => -1,
			'order'            => 'ASC',
			'orderby'          => 'name',
			'include'          => array(),
			'exclude'          => array(),
			'search'           => '',
			'hide_invisible'   => true,
			'show_description' => false,
			'show_images'      => false,
			'show_rating'      => false,
			'show_updated'     => false,
			'show_private'     => false,
			'show_name'        => false,
			'class'            => 'linkcat',
			'link_before'      => '<span>',
			'link_after'       => '</span>',
			'between'          => '<br />',
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$terms = get_terms( 'link_category' );
		$bookmarks = get_bookmarks( array( 'hide_invisible' => false ) );

		$category_order = array( 
			'ASC'  => esc_attr__( 'Ascending', 'hybrid-core' ), 
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' ) 
		);

		$category_orderby = array( 
			'count' => esc_attr__( 'Count', 'hybrid-core' ), 
			'ID'    => esc_attr__( 'ID', 'hybrid-core' ), 
			'name'  => esc_attr__( 'Name', 'hybrid-core' ), 
			'slug'  => esc_attr__( 'Slug', 'hybrid-core' ) 
		);

		$order = array( 
			'ASC'  => esc_attr__( 'Ascending', 'hybrid-core' ), 
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' ) 
		);

		$orderby = array( 
			'id'          => esc_attr__( 'ID', 'hybrid-core' ), 
			'description' => esc_attr__( 'Description', 'hybrid-core' ), 
			'length'      => esc_attr__( 'Length', 'hybrid-core' ), 
			'name'        => esc_attr__( 'Name', 'hybrid-core' ), 
			'notes'       => esc_attr__( 'Notes', 'hybrid-core' ), 
			'owner'       => esc_attr__( 'Owner', 'hybrid-core' ), 
			'rand'        => esc_attr__( 'Random', 'hybrid-core' ), 
			'rating'      => esc_attr__( 'Rating', 'hybrid-core' ), 
			'rel'         => esc_attr__( 'Rel', 'hybrid-core' ), 
			'rss'         => esc_attr__( 'RSS', 'hybrid-core' ), 
			'target'      => esc_attr__( 'Target', 'hybrid-core' ), 
			'updated'     => esc_attr__( 'Updated', 'hybrid-core' ), 
			'url'         => esc_attr__( 'URL', 'hybrid-core' ) 
		);
		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title_li' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title_li' ); ?>" name="<?php echo $this->get_field_name( 'title_li' ); ?>" value="<?php echo esc_attr( $instance['title_li'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'category_order' ); ?>"><code>category_order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'category_order' ); ?>" name="<?php echo $this->get_field_name( 'category_order' ); ?>">
				<?php foreach ( $category_order as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['category_order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'category_orderby' ); ?>"><code>category_orderby</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'category_orderby' ); ?>" name="<?php echo $this->get_field_name( 'category_orderby' ); ?>">
				<?php foreach ( $category_orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['category_orderby'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><code>category</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['category'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_category' ); ?>"><code>exclude_category</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'exclude_category' ); ?>" name="<?php echo $this->get_field_name( 'exclude_category' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['exclude_category'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>"><code>class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" value="<?php echo esc_attr( $instance['class'] ); ?>" />
		</p>

		</div>

		<div class="hybrid-widget-controls columns-3">

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code>limit</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
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
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $bookmarks as $bookmark ) { ?>
					<option value="<?php echo esc_attr( $bookmark->link_id ); ?>" <?php echo ( in_array( $bookmark->link_id, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $bookmark->link_name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $bookmarks as $bookmark ) { ?>
					<option value="<?php echo esc_attr( $bookmark->link_id ); ?>" <?php echo ( in_array( $bookmark->link_id, (array) $instance['exclude'] ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $bookmark->link_name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><code>search</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
		</p>

		</div>

		<div class="hybrid-widget-controls columns-3 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'between' ); ?>"><code>between</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'between' ); ?>" name="<?php echo $this->get_field_name( 'between' ); ?>" value="<?php echo esc_attr( $instance['between'] ); ?>" />
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
			<label for="<?php echo $this->get_field_id( 'categorize' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['categorize'], true ); ?> id="<?php echo $this->get_field_id( 'categorize' ); ?>" name="<?php echo $this->get_field_name( 'categorize' ); ?>" /> <?php _e( 'Categorize?', 'hybrid-core' ); ?> <code>categorize</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_description' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_description'], true ); ?> id="<?php echo $this->get_field_id( 'show_description' ); ?>" name="<?php echo $this->get_field_name( 'show_description' ); ?>" /> <?php _e( 'Show description?', 'hybrid-core' ); ?> <code>show_description</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_invisible' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_invisible'], true ); ?> id="<?php echo $this->get_field_id( 'hide_invisible' ); ?>" name="<?php echo $this->get_field_name( 'hide_invisible' ); ?>" /> <?php _e( 'Hide invisible?', 'hybrid-core' ); ?> <code>hide_invisible</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_rating' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_rating'], true ); ?> id="<?php echo $this->get_field_id( 'show_rating' ); ?>" name="<?php echo $this->get_field_name( 'show_rating' ); ?>" /> <?php _e( 'Show rating?', 'hybrid-core' ); ?> <code>show_rating</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_updated' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_updated'], true ); ?> id="<?php echo $this->get_field_id( 'show_updated' ); ?>" name="<?php echo $this->get_field_name( 'show_updated' ); ?>" /> <?php _e( 'Show updated?', 'hybrid-core' ); ?> <code>show_updated</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_images' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_images'], true ); ?> id="<?php echo $this->get_field_id( 'show_images' ); ?>" name="<?php echo $this->get_field_name( 'show_images' ); ?>" /> <?php _e( 'Show images?', 'hybrid-core' ); ?> <code>show_images</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_name' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_name'], true ); ?> id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" /> <?php _e( 'Show name?', 'hybrid-core' ); ?> <code>show_name</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_private' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_private'], true ); ?> id="<?php echo $this->get_field_id( 'show_private' ); ?>" name="<?php echo $this->get_field_name( 'show_private' ); ?>" /> <?php _e( 'Show private?', 'hybrid-core' ); ?> <code>show_private</code></label>
		</p>

		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>