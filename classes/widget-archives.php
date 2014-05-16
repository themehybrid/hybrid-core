<?php
/**
 * The Archives widget replaces the default WordPress Archives widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_get_archives() function.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Archives widget class.
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Archives extends WP_Widget {

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
			'classname'   => 'widget-archives widget_archive',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your archives.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-archives',
			__( 'Archives', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up defaults. */
		$this->defaults = array(
			'title'           => esc_attr__( 'Archives', 'hybrid-core' ),
			'limit'           => 10,
			'type'            => 'monthly',
			'order'           => 'DESC',
			'format'          => 'html',
			'before'          => '',
			'after'           => '',
			'show_post_count' => false
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

		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Get the archives list. */
		$archives = str_replace( array( "\r", "\n", "\t" ), '', wp_get_archives( $args ) );

		/* If the archives should be shown in a <select> drop-down. */
		if ( 'option' == $args['format'] ) {

			/* Create a title for the drop-down based on the archive type. */
			if ( 'yearly' == $args['type'] )
				$option_title = esc_html__( 'Select Year', 'hybrid-core' );

			elseif ( 'monthly' == $args['type'] )
				$option_title = esc_html__( 'Select Month', 'hybrid-core' );

			elseif ( 'weekly' == $args['type'] )
				$option_title = esc_html__( 'Select Week', 'hybrid-core' );

			elseif ( 'daily' == $args['type'] )
				$option_title = esc_html__( 'Select Day', 'hybrid-core' );

			elseif ( 'postbypost' == $args['type'] || 'alpha' == $args['type'] )
				$option_title = esc_html__( 'Select Post', 'hybrid-core' );

			/* Output the <select> element and each <option>. */
			echo '<p><select name="archive-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>';
				echo '<option value="">' . $option_title . '</option>';
				echo $archives;
			echo '</select></p>';
		}

		/* If the format should be an unordered list. */
		elseif ( 'html' == $args['format'] ) {
			echo '<ul class="xoxo archives">' . $archives . '</ul><!-- .xoxo .archives -->';
		}

		/* All other formats. */
		else {
			echo $archives;
		}

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
		$instance['title']  = strip_tags( $new_instance['title']  );

		/* Whitelist options. */
		$type   = array( 'alpha', 'daily', 'monthly', 'postbypost', 'weekly', 'yearly' );
		$order  = array( 'ASC', 'DESC' );
		$format = array( 'custom', 'html', 'option' );

		$instance['type']   = in_array( $new_instance['type'], $type )     ? $new_instance['type']   : 'monthly';
		$instance['order']  = in_array( $new_instance['order'], $order )   ? $new_instance['order']  : 'DESC';
		$instance['format'] = in_array( $new_instance['format'], $format ) ? $new_instance['format'] : 'html';

		/* Integers. */
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['limit'] = 0 === $instance['limit'] ? '' : $instance['limit'];

		/* Text boxes. Make sure user can use 'unfiltered_html'. */
		$instance['before'] = current_user_can( 'unfiltered_html' ) ? $new_instance['before'] : wp_filter_post_kses( $new_instance['before'] );
		$instance['after']  = current_user_can( 'unfiltered_html' ) ? $new_instance['after']  : wp_filter_post_kses( $new_instance['after']  );

		/* Checkboxes. */
		$instance['show_post_count'] = isset( $new_instance['show_post_count'] ) ? 1 : 0;

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
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		/* Create an array of archive types. */
		$type = array( 
			'alpha'      => esc_attr__( 'Alphabetical', 'hybrid-core' ), 
			'daily'      => esc_attr__( 'Daily',        'hybrid-core' ), 
			'monthly'    => esc_attr__( 'Monthly',      'hybrid-core' ),
			'postbypost' => esc_attr__( 'Post By Post', 'hybrid-core' ), 
			'weekly'     => esc_attr__( 'Weekly',       'hybrid-core' ), 
			'yearly'     => esc_attr__( 'Yearly',       'hybrid-core' ) 
		);

		/* Create an array of order options. */
		$order = array(
			'ASC'  => esc_attr__( 'Ascending',  'hybrid-core' ),
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' )
		);

		/* Create an array of archive formats. */
		$format = array( 
			'custom' => esc_attr__( 'Custom', 'hybrid-core' ), 
			'html'   => esc_attr__( 'HTML',   'hybrid-core' ), 
			'option' => esc_attr__( 'Option', 'hybrid-core' ) 
		);
		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code>limit</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" placeholder="10" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><code>type</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<?php foreach ( $type as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['type'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
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
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><code>format</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
				<?php foreach ( $format as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['format'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>"><code>before</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo esc_attr( $instance['before'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>"><code>after</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo esc_attr( $instance['after'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_post_count' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_post_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_post_count' ); ?>" name="<?php echo $this->get_field_name( 'show_post_count' ); ?>" /> <?php _e( 'Show post count?', 'hybrid-core' ); ?> <code>show_post_count</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
