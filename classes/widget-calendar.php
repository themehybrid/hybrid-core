<?php
/**
 * The calendar widget was created to give users the ability to show a post calendar for their blog 
 * using all the available options given in the get_calendar() function. It replaces the default WordPress
 * calendar widget.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Calendar Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Calendar extends WP_Widget {

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
			'classname'   => 'widget-calendar widget_calendar',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your calendar.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 200,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-calendar',
			__( 'Calendar', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up the defaults. */
		$this->defaults = array(
			'title'   => esc_attr__( 'Calendar', 'hybrid-core' ),
			'initial' => false
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

		/* Set the $args. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Get the $initial argument. */
		$initial = !empty( $args['initial'] ) ? true : false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Display the calendar. */
		echo '<div class="calendar-wrap">';
			echo str_replace( array( "\r", "\n", "\t" ), '', get_calendar( $initial, false ) );
		echo '</div><!-- .calendar-wrap -->';

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
		$instance['title'] = strip_tags( $new_instance['title'] );

		/* Checkboxes. */
		$instance['initial'] = isset( $new_instance['initial'] ) ? 1 : 0;

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
		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

		<div class="hybrid-widget-controls columns-1">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['initial'], true ); ?> id="<?php echo $this->get_field_id( 'initial' ); ?>" name="<?php echo $this->get_field_name( 'initial' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'initial' ); ?>"><?php _e( 'One-letter abbreviation?', 'hybrid-core' ); ?> <code>initial</code></label>
		</p>
		</div>
	<?php
	}
}
