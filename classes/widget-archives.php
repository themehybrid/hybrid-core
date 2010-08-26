<?php
/**
 * Archives Widget Class
 *
 * The Archives widget replaces the default WordPress Archives widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_get_archives() function.
 *
 * @since 0.6
 * @link http://codex.wordpress.org/Template_Tags/wp_get_archives
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * @package Hybrid
 * @subpackage Classes
 */

class Hybrid_Widget_Archives extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Hybrid_Widget_Archives() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'archives', 'description' => __( 'An advanced widget that gives you total control over the output of your archives.', $this->textdomain ) );
		$control_ops = array( 'width' => 525, 'height' => 350, 'id_base' => "{$this->prefix}-archives" );
		$this->WP_Widget( "{$this->prefix}-archives", __( 'Archives', $this->textdomain ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$args = array();

		$args['type'] = $instance['type']; 
		$args['format'] = $instance['format'];
		$args['before'] = $instance['before'];
		$args['after'] = $instance['after'];
		$args['show_post_count'] = isset( $instance['show_post_count'] ) ? $instance['show_post_count'] : false;
		$args['limit'] = !empty( $instance['limit'] ) ? intval( $instance['limit'] ) : '';
		$args['echo'] = false;

		echo $before_widget;

		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$archives = str_replace( array( "\r", "\n", "\t" ), '', wp_get_archives( $args ) );

		if ( 'option' == $args['format'] ) {

			if ( 'yearly' == $args['type'] )
				$option_title = __( 'Select Year', $this->textdomain );
			elseif ( 'monthly' == $args['type'] )
				$option_title = __( 'Select Month', $this->textdomain );
			elseif ( 'weekly' == $args['type'] )
				$option_title = __( 'Select Week', $this->textdomain );
			elseif ( 'daily' == $args['type'] )
				$option_title = __( 'Select Day', $this->textdomain );
			elseif ( 'postbypost' == $args['type'] || 'alpha' == $args['type'] )
				$option_title = __( 'Select Post', $this->textdomain );

			echo '<select name="archive-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>';
			echo '<option value="">' . esc_attr( $option_title ) . '</option>';
			echo $archives;
			echo '</select>';
		}
		elseif ( 'html' == $args['format'] ) {
			echo '<ul class="xoxo archives">' . $archives . '</ul><!-- .xoxo .archives -->';
		}
		else {
			echo $archives;
		}

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
		$instance['before'] = strip_tags( $new_instance['before'] );
		$instance['after'] = strip_tags( $new_instance['after'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['show_post_count'] = ( isset( $new_instance['show_post_count'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Archives', $this->textdomain ),
			'limit' => 10,
			'type' => 'monthly',
			'format' => 'html',
			'before' => '',
			'after' => '',
			'show_post_count' => false
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$type = array( 'alpha' => __( 'Alphabetical', $this->textdomain ), 'daily' => __( 'Daily', $this->textdomain ), 'monthly' => __( 'Monthly', $this->textdomain ),'postbypost' => __( 'Post By Post', $this->textdomain ), 'weekly' => __( 'Weekly', $this->textdomain ), 'yearly' => __( 'Yearly', $this->textdomain ) );
		$format = array( 'custom' => __( 'Custom', $this->textdomain ), 'html' => __( 'HTML', $this->textdomain ), 'option' => __( 'Option', $this->textdomain ) );

		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code>limit</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><code>type</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<?php foreach ( $type as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['type'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><code>format</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
				<?php foreach ( $format as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['format'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>"><code>before</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo $instance['before']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>"><code>after</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo $instance['after']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_post_count' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_post_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_post_count' ); ?>" name="<?php echo $this->get_field_name( 'show_post_count' ); ?>" /> <?php _e( 'Show post count?', $this->textdomain ); ?> <code>show_post_count</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>