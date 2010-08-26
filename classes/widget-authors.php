<?php
/**
 * Authors Widget Class
 *
 * The authors widget was created to give users the ability to list the authors of their blog because
 * there was no equivalent WordPress widget that offered the functionality. This widget allows full
 * control over its output by giving access to the parameters of wp_list_authors().
 *
 * @since 0.6
 * @link http://codex.wordpress.org/Template_Tags/wp_list_authors
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * @package Hybrid
 * @subpackage Classes
 */

class Hybrid_Widget_Authors extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Hybrid_Widget_Authors() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'authors', 'description' => __( 'An advanced widget that gives you total control over the output of your author lists.',$this->textdomain ) );
		$control_ops = array( 'width' => 525, 'height' => 350, 'id_base' => "{$this->prefix}-authors" );
		$this->WP_Widget( "{$this->prefix}-authors", __( 'Authors', $this->textdomain ), $widget_ops, $control_ops );

		add_action( 'delete_user', array( &$this, 'delete_transient' ) );
		add_action( 'user_register', array( &$this, 'delete_transient' ) );
		add_action( 'profile_update', array( &$this, 'delete_transient' ) );
		add_action( 'save_post', array( &$this, 'delete_transient' ) );
		add_action( 'deleted_post', array( &$this, 'delete_transient' ) );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {

		/* If a transient has been saved with the widget information, use it. */
		$transient = get_transient( "{$this->prefix}_widget_{$args['widget_id']}" );
		if ( $transient ) {
			echo $transient;
			return;
		}

		extract( $args, EXTR_SKIP );

		$args = array();

		$args['style'] = $instance['style'];
		$args['feed'] = $instance['feed']; 
		$args['feed_image'] = $instance['feed_image'];
		$args['optioncount'] = isset( $instance['optioncount'] ) ? $instance['optioncount'] : false;
		$args['exclude_admin'] = isset( $instance['exclude_admin'] ) ? $instance['exclude_admin'] : false;
		$args['show_fullname'] = isset( $instance['show_fullname'] ) ? $instance['show_fullname'] : false;
		$args['hide_empty'] = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : false;
		$args['html'] = isset( $instance['html'] ) ? $instance['html'] : false;
		$args['echo'] = false;

		$authors_widget = $before_widget;

		if ( $instance['title'] )
			$authors_widget .= $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		$authors = str_replace( array( "\r", "\n", "\t" ), '', wp_list_authors( $args ) );

		if ( 'list' == $args['style'] && $args['html'] )
			$authors = '<ul class="xoxo authors">' . $authors . '</ul><!-- .xoxo .authors -->';

		$authors_widget .= $authors;

		$authors_widget .= $after_widget;

		set_transient( "{$this->prefix}_widget_{$widget_id}", $authors_widget, hybrid_get_transient_expiration() ); 
		echo $authors_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['feed'] = strip_tags( $new_instance['feed'] );
		$instance['feed_image'] = strip_tags( $new_instance['feed_image'] );

		$instance['html'] = ( isset( $new_instance['html'] ) ? 1 : 0 );
		$instance['optioncount'] = ( isset( $new_instance['optioncount'] ) ? 1 : 0 );
		$instance['exclude_admin'] = ( isset( $new_instance['exclude_admin'] ) ? 1 : 0 );
		$instance['show_fullname'] = ( isset( $new_instance['show_fullname'] ) ? 1 : 0 );
		$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );

		$this->delete_transient();

		return $instance;
	}

	function delete_transient() {
		delete_transient( "{$this->prefix}_widget_{$this->id}" );
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Authors', $this->textdomain ),
			'optioncount' => false,
			'exclude_admin' => false,
			'show_fullname' => true,
			'hide_empty' => true,
			'style' => 'list',
			'html' => true,
			'feed' => '',
			'feed_image' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed' ); ?>"><code>feed</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="<?php echo $instance['feed']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"><code>feed_image</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo $instance['feed_image']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><code>style</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<?php foreach ( array( 'list' => __( 'List', $this->textdomain), 'none' => __( 'None', $this->textdomain ) ) as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['style'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'html' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['html'], true ); ?> id="<?php echo $this->get_field_id( 'html' ); ?>" name="<?php echo $this->get_field_name( 'html' ); ?>" /> <?php _e( '<acronym title="Hypertext Markup Language">HTML</acronym>?', $this->textdomain ); ?> <code>html</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'optioncount' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['optioncount'], true ); ?> id="<?php echo $this->get_field_id( 'optioncount' ); ?>" name="<?php echo $this->get_field_name( 'optioncount' ); ?>" /> <?php _e( 'Show post count?', $this->textdomain ); ?> <code>optioncount</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_admin' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['exclude_admin'], true ); ?> id="<?php echo $this->get_field_id( 'exclude_admin' ); ?>" name="<?php echo $this->get_field_name( 'exclude_admin' ); ?>" /> <?php _e( 'Exclude admin?', $this->textdomain ); ?> <code>exclude_admin</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_fullname' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_fullname'], true ); ?> id="<?php echo $this->get_field_id( 'show_fullname' ); ?>" name="<?php echo $this->get_field_name( 'show_fullname' ); ?>" /> <?php _e( 'Show full name?', $this->textdomain ); ?> <code>show_fullname</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" /> <?php _e( 'Hide empty?', $this->textdomain ); ?> <code>hide_empty</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>