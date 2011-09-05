<?php
/**
 * The nav menu widget was created to give users the ability to show nav menus created from the 
 * Menus screen, by the theme, or by plugins using the wp_nav_menu() function.  It replaces the default
 * WordPress navigation menu class.
 *
 * @package Hybrid
 * @subpackage Classes
 */

/**
 * Nav Menu Widget Class
 *
 * @since 0.8.0
 * @link http://themehybrid.com/themes/hybrid/widgets
 */
class Hybrid_Widget_Nav_Menu extends WP_Widget {

	/**
	 * Prefix for the widget.
	 * @since 0.8.0
	 */
	var $prefix;

	/**
	 * Textdomain for the widget.
	 * @since 0.8.0
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
			'classname' => 'nav-menu',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your menus.', $this->textdomain )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-nav-menu',				// $this->id_base
			__( 'Navigation Menu', $this->textdomain ),	// $this->name
			$widget_options,				// $this->widget_options
			$control_options				// $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.8.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for the wp_nav_menu() function. */
		$args = array(
			'menu' => 		$instance['menu'],
			'container' => 		$instance['container'],
			'container_id' => 		$instance['container_id'],
			'container_class' => 	$instance['container_class'],
			'menu_id' => 		$instance['menu_id'],
			'menu_class' => 		$instance['menu_class'],
			'link_before' => 		$instance['link_before'],
			'link_after' => 		$instance['link_after'],
			'before' => 		$instance['before'],
			'after' => 		$instance['after'],
			'depth' => 		intval( $instance['depth'] ),
			'fallback_cb' => 		$instance['fallback_cb'],
			'walker' => 		$instance['walker'],
			'echo' => 		false
		);

		/* Output the theme's widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Output the nav menu. */
		echo str_replace( array( "\r", "\n", "\t" ), '', wp_nav_menu( $args ) );

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.8.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['depth'] = strip_tags( $new_instance['depth'] );
		$instance['container_id'] = strip_tags( $new_instance['container_id'] );
		$instance['container_class'] = strip_tags( $new_instance['container_class'] );
		$instance['menu_id'] = strip_tags( $new_instance['menu_id'] );
		$instance['menu_class'] = strip_tags( $new_instance['menu_class'] );
		$instance['fallback_cb'] = strip_tags( $new_instance['fallback_cb'] );
		$instance['walker'] = strip_tags( $new_instance['walker'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.8.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Navigation', $this->textdomain ),
			'menu' => '',
			'container' => 'div',
			'container_id' => '',
			'container_class' => '',
			'menu_id' => '',
			'menu_class' => 'nav-menu',
			'depth' => 0,
			'before' => '',
			'after' => '',
			'link_before' => '',
			'link_after' => '',
			'fallback_cb' => 'wp_page_menu',
			'walker' => ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$container = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><code>menu</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
				<?php foreach ( wp_get_nav_menus() as $menu ) { ?>
					<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $instance['menu'], $menu->term_id ); ?>><?php echo esc_html( $menu->name ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container' ); ?>"><code>container</code></label> 
			<select class="smallfat" id="<?php echo $this->get_field_id( 'container' ); ?>" name="<?php echo $this->get_field_name( 'container' ); ?>">
				<?php foreach ( $container as $option ) { ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $instance['container'], $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container_id' ); ?>"><code>container_id</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_id' ); ?>" name="<?php echo $this->get_field_name( 'container_id' ); ?>" value="<?php echo esc_attr( $instance['container_id'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container_class' ); ?>"><code>container_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_class' ); ?>" name="<?php echo $this->get_field_name( 'container_class' ); ?>" value="<?php echo esc_attr( $instance['container_class'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_id' ); ?>"><code>menu_id</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_id' ); ?>" name="<?php echo $this->get_field_name( 'menu_id' ); ?>" value="<?php echo esc_attr( $instance['menu_id'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_class' ); ?>"><code>menu_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_class' ); ?>" name="<?php echo $this->get_field_name( 'menu_class' ); ?>" value="<?php echo esc_attr( $instance['menu_class'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>"><code>before</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo esc_attr( $instance['before'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>"><code>after</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo esc_attr( $instance['after'] ); ?>" />
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
			<label for="<?php echo $this->get_field_id( 'fallback_cb' ); ?>"><code>fallback_cb</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'fallback_cb' ); ?>" name="<?php echo $this->get_field_name( 'fallback_cb' ); ?>" value="<?php echo esc_attr( $instance['fallback_cb'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'walker' ); ?>"><code>walker</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'walker' ); ?>" name="<?php echo $this->get_field_name( 'walker' ); ?>" value="<?php echo esc_attr( $instance['walker'] ); ?>" />
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>