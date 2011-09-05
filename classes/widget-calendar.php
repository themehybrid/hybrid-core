<?php
/**
 * The calendar widget was created to give users the ability to show a post calendar for their blog 
 * using all the available options given in the get_calendar() function. It replaces the default WordPress
 * calendar widget.
 *
 * @package Hybrid
 * @subpackage Classes
 */

/**
 * Calendar Widget Class
 *
 * @since 0.6.0
 * @link http://codex.wordpress.org/Function_Reference/get_calendar
 * @link http://themehybrid.com/themes/hybrid/widgets
 */
class Hybrid_Widget_Calendar extends WP_Widget {

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
			'classname' => 'calendar',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your calendar.', $this->textdomain )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 200,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-calendar',			// $this->id_base
			__( 'Calendar', $this->textdomain ),	// $this->name
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

		/* Get the $initial argument. */
		$initial = !empty( $instance['initial'] ) ? true : false;

		/* Output the theme's widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Display the calendar. */
		echo '<div class="calendar-wrap">';
			echo str_replace( array( "\r", "\n", "\t" ), '', get_calendar( $initial, false ) );
		echo '</div><!-- .calendar-wrap -->';

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['initial'] = ( isset( $new_instance['initial'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Calendar', $this->textdomain ),
			'initial' => false
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="hybrid-widget-controls columns-1">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['initial'], true ); ?> id="<?php echo $this->get_field_id( 'initial' ); ?>" name="<?php echo $this->get_field_name( 'initial' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'initial' ); ?>"><?php _e( 'One-letter abbreviation?', $this->textdomain ); ?> <code>initial</code></label>
		</p>
		</div>
	<?php
	}
}

?>