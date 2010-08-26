<?php
/**
 * Calendar Widget Class
 *
 * The calendar widget was created to give users the ability to show a post calendar for their blog 
 * using all the available options given in the get_calendar() function. It replaces the default WordPress
 * calendar widget.
 *
 * @since 0.6
 * @link http://codex.wordpress.org/Function_Reference/get_calendar
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * @package Hybrid
 * @subpackage Classes
 */

class Hybrid_Widget_Calendar extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Hybrid_Widget_Calendar() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'calendar', 'description' => __( 'An advanced widget that gives you total control over the output of your calendar.', $this->textdomain ) );
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => "{$this->prefix}-calendar" );
		$this->WP_Widget( "{$this->prefix}-calendar", __( 'Calendar', $this->textdomain ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$initial = isset( $instance['initial'] ) ? $instance['initial'] : false;

		echo $before_widget;

		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		echo '<div class="calendar-wrap">';
			get_calendar( $initial );
		echo '</div><!-- .calendar-wrap -->';

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
		$instance['initial'] = ( isset( $new_instance['initial'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Calendar', $this->textdomain ),
			'initial' => false
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="hybrid-widget-controls columns-1">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
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