<?php
/**
 * Nav Menu Widget Class
 *
 * The nav menu widget was created to give users the ability to show nav menus created from the 
 * Menus screen, by the theme, or by plugins using the wp_nav_menu() function.  It replaces the default
 * WordPress navigation menu class.
 *
 * @since 0.8
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * @package Hybrid
 * @subpackage Classes
 */

class Hybrid_Widget_Nav_Menu extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Hybrid_Widget_Nav_Menu() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'nav-menu', 'description' => __( 'An advanced widget that gives you total control over the output of your menus.', $this->textdomain ) );
		$control_ops = array( 'width' => 525, 'height' => 350, 'id_base' => "{$this->prefix}-nav-menu" );
		$this->WP_Widget( "{$this->prefix}-nav-menu", __( 'Navigation Menu', $this->textdomain ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$args = array();

		$args['menu'] = $instance['menu'];
		$args['container'] = $instance['container'];
		$args['container_id'] = $instance['container_id'];
		$args['container_class'] = $instance['container_class'];
		$args['menu_id'] = $instance['menu_id'];
		$args['menu_class'] = $instance['menu_class'];
		$args['link_before'] = $instance['link_before'];
		$args['link_after'] = $instance['link_after'];
		$args['before'] = $instance['before'];
		$args['after'] = $instance['after'];
		$args['depth'] = intval( $instance['depth'] );
		$args['fallback_cb'] = $instance['fallback_cb'];
		$args['walker'] = $instance['walker'];
		$args['echo'] = false;

		echo $before_widget;

		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		echo str_replace( array( "\r", "\n", "\t" ), '', wp_nav_menu( $args ) );

		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6
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
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Navigation', $this->textdomain ),
			'format' => 'div',
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
		$instance = wp_parse_args( (array) $instance, $defaults );

		$container = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><code>menu</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
				<?php foreach ( wp_get_nav_menus() as $menu ) { ?>
					<option value="<?php echo $menu->term_id; ?>" <?php selected( $instance['menu'], $menu->term_id ); ?>><?php echo $menu->name; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container' ); ?>"><code>container</code></label> 
			<select class="smallfat" id="<?php echo $this->get_field_id( 'container' ); ?>" name="<?php echo $this->get_field_name( 'container' ); ?>">
				<?php foreach ( $container as $option ) { ?>
					<option value="<?php echo $option; ?>" <?php selected( $instance['container'], $option ); ?>><?php echo $option; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container_id' ); ?>"><code>container_id</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_id' ); ?>" name="<?php echo $this->get_field_name( 'container_id' ); ?>" value="<?php echo $instance['container_id']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container_class' ); ?>"><code>container_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_class' ); ?>" name="<?php echo $this->get_field_name( 'container_class' ); ?>" value="<?php echo $instance['container_class']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_id' ); ?>"><code>menu_id</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_id' ); ?>" name="<?php echo $this->get_field_name( 'menu_id' ); ?>" value="<?php echo $instance['menu_id']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_class' ); ?>"><code>menu_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_class' ); ?>" name="<?php echo $this->get_field_name( 'menu_class' ); ?>" value="<?php echo $instance['menu_class']; ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo $instance['depth']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>"><code>before</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo $instance['before']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>"><code>after</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo $instance['after']; ?>" />
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
			<label for="<?php echo $this->get_field_id( 'fallback_cb' ); ?>"><code>fallback_cb</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'fallback_cb' ); ?>" name="<?php echo $this->get_field_name( 'fallback_cb' ); ?>" value="<?php echo $instance['fallback_cb']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'walker' ); ?>"><code>walker</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'walker' ); ?>" name="<?php echo $this->get_field_name( 'walker' ); ?>" value="<?php echo $instance['walker']; ?>" />
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>