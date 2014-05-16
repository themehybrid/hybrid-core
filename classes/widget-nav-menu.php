<?php
/**
 * The nav menu widget was created to give users the ability to show nav menus created from the 
 * Menus screen, by the theme, or by plugins using the wp_nav_menu() function.  It replaces the default
 * WordPress navigation menu class.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Nav Menu Widget Class
 *
 * @since 0.8.0
 */
class Hybrid_Widget_Nav_Menu extends WP_Widget {

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
			'classname'   => 'widget-nav-menu widget_nav_menu',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your menus.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-nav-menu',
			__( 'Navigation Menu', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up the defaults. */
		$this->defaults = array(
			'title'           => esc_attr__( 'Navigation', 'hybrid-core' ),
			'menu'            => '',
			'container'       => 'div',
			'container_id'    => '',
			'container_class' => '',
			'menu_id'         => '',
			'menu_class'      => 'nav-menu',
			'depth'           => 0,
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'fallback_cb'     => 'wp_page_menu',
			'theme_location'  => ''
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since  0.8.0
	 * @access public
	 * @param  array  $sidebar
	 * @param  array  $instance
	 * @return void
	 */
	function widget( $sidebar, $instance ) {

		/* Set the $args for wp_nav_menu() to the $instance array. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Output the nav menu. */
		echo str_replace( array( "\r", "\n", "\t" ), '', wp_nav_menu( $args ) );

		/* Close the sidebar's widget wrapper. */
		echo $sidebar['after_widget'];
	}

	/**
	 * The update callback for the widget control options.  This method is used to sanitize and/or
	 * validate the options before saving them into the database.
	 *
	 * @since  0.8.0
	 * @access public
	 * @param  array  $new_instance
	 * @param  array  $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		/* Strip tags. */
		$instance['title']           = strip_tags( $new_instance['title']           );
		$instance['menu']            = strip_tags( $new_instance['menu']            );
		$instance['theme_location']  = strip_tags( $new_instance['theme_location']  );

		/* Whitelist options. */
		$container = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );

		$instance['container'] = in_array( $new_instance['container'], $container ) ? $new_instance['container'] : 'div';

		/* Integers. */
		$instance['depth'] = absint( $new_instance['depth'] );

		/* HTML class. */
		$instance['container_id']    = sanitize_html_class( $new_instance['container_id']    );
		$instance['container_class'] = sanitize_html_class( $new_instance['container_class'] );
		$instance['menu_id']         = sanitize_html_class( $new_instance['menu_id']         );
		$instance['menu_class']      = sanitize_html_class( $new_instance['menu_class']      );

		/* Text boxes. Make sure user can use 'unfiltered_html'. */
		$instance['before']      = current_user_can( 'unfiltered_html' ) ? $new_instance['before']      : wp_filter_post_kses( $new_instance['before']      );
		$instance['after']       = current_user_can( 'unfiltered_html' ) ? $new_instance['after']       : wp_filter_post_kses( $new_instance['after']       );
		$instance['link_before'] = current_user_can( 'unfiltered_html' ) ? $new_instance['link_before'] : wp_filter_post_kses( $new_instance['link_before'] );
		$instance['link_after']  = current_user_can( 'unfiltered_html' ) ? $new_instance['link_after']  : wp_filter_post_kses( $new_instance['link_after']  );

		/* Function name. */
		$instance['fallback_cb'] = empty( $new_instance['fallback_cb'] ) || function_exists( $new_instance['fallback_cb'] ) ? $new_instance['fallback_cb'] : 'wp_page_menu';

		/* Return sanitized options. */
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since  0.8.0
	 * @access public
	 * @param  array  $instance
	 * @param  void
	 */
	function form( $instance ) {

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$container       = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
		$theme_locations = get_registered_nav_menus();
		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><code>menu</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
				<option value=""></option>
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
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_id' ); ?>" name="<?php echo $this->get_field_name( 'container_id' ); ?>" value="<?php echo esc_attr( $instance['container_id'] ); ?>" placeholder="example" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'container_class' ); ?>"><code>container_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_class' ); ?>" name="<?php echo $this->get_field_name( 'container_class' ); ?>" value="<?php echo esc_attr( $instance['container_class'] ); ?>" placeholder="example" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_id' ); ?>"><code>menu_id</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_id' ); ?>" name="<?php echo $this->get_field_name( 'menu_id' ); ?>" value="<?php echo esc_attr( $instance['menu_id'] ); ?>" placeholder="example" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_class' ); ?>"><code>menu_class</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_class' ); ?>" name="<?php echo $this->get_field_name( 'menu_class' ); ?>" value="<?php echo esc_attr( $instance['menu_class'] ); ?>" placeholder="example" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'depth' ); ?>"><code>depth</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" placeholder="0" />
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
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'fallback_cb' ); ?>" name="<?php echo $this->get_field_name( 'fallback_cb' ); ?>" value="<?php echo esc_attr( $instance['fallback_cb'] ); ?>" placeholder="wp_page_menu" />
		</p>
		<?php if ( !empty( $theme_locations ) ) { ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'theme_location' ); ?>"><code>theme_location</code></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'theme_location' ); ?>" name="<?php echo $this->get_field_name( 'theme_location' ); ?>">
					<option value=""></option>
					<?php foreach ( $theme_locations as $location => $label ) { ?>
						<option value="<?php echo esc_attr( $location ); ?>" <?php selected( $instance['theme_location'], $location ); ?>><?php echo esc_html( $label ); ?></option>
					<?php } ?>
				</select>
			</p>
		<?php } ?>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
