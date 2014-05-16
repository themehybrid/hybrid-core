<?php
/**
 * The authors widget was created to give users the ability to list the authors of their blog because
 * there was no equivalent WordPress widget that offered the functionality. This widget allows full
 * control over its output by giving access to the parameters of wp_list_authors().
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Authors Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Authors extends WP_Widget {

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
			'classname'   => 'widget-authors',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your author lists.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-authors',
			__( 'Authors', 'hybrid-core' ),
			$widget_options,
			$control_options
		);

		/* Set up defaults. */
		$this->defaults = array(
			'title'         => esc_attr__( 'Authors', 'hybrid-core' ),
			'order'         => 'ASC',
			'orderby'       => 'display_name',
			'number'        => '',
			'include'       => '',
			'exclude'       => '',
			'optioncount'   => false,
			'exclude_admin' => false,
			'show_fullname' => true,
			'hide_empty'    => true,
			'style'         => 'list',
			'html'          => true,
			'feed'          => '',
			'feed_image'    => ''
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

		/* Set the $args for wp_list_authors() to the $instance array. */
		$args = wp_parse_args( $instance, $this->defaults );

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title',  $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		/* Get the authors list. */
		$authors = str_replace( array( "\r", "\n", "\t" ), '', wp_list_authors( $args ) );

		/* If 'list' is the style and the output should be HTML, wrap the authors in a <ul>. */
		if ( 'list' == $args['style'] && $args['html'] )
			$authors = '<ul class="xoxo authors">' . $authors . '</ul><!-- .xoxo .authors -->';

		/* If 'none' is the style and the output should be HTML, wrap the authors in a <p>. */
		elseif ( 'none' == $args['style'] && $args['html'] )
			$authors = '<p class="authors">' . $authors . '</p><!-- .authors -->';

		/* Display the authors list. */
		echo $authors;

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
		$instance['feed']  = strip_tags( $new_instance['feed']  );

		/* Whitelist options. */
		$order   = array( 'ASC', 'DESC' );
		$orderby = array( 'display_name', 'email', 'ID', 'nicename', 'post_count', 'registered', 'url', 'user_login' );
		$style   = array( 'list', 'none' );

		$instance['order']   = in_array( $new_instance['order'], $order )     ? $new_instance['order']   : 'ASC';
		$instance['orderby'] = in_array( $new_instance['orderby'], $orderby ) ? $new_instance['orderby'] : 'display_name';
		$instance['style']   = in_array( $new_instance['style'], $style )     ? $new_instance['style']   : 'list';

		/* Integers. */
		$instance['number'] = intval( $new_instance['number'] );

		/* Only allow integers and commas. */
		$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
		$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );

		/* URLs. */
		$instance['feed_image'] = esc_url_raw( $new_instance['feed_image'] );

		/* Checkboxes. */
		$instance['html']          = isset( $new_instance['html'] )          ? 1 : 0;
		$instance['optioncount']   = isset( $new_instance['optioncount'] )   ? 1 : 0;
		$instance['exclude_admin'] = isset( $new_instance['exclude_admin'] ) ? 1 : 0;
		$instance['show_fullname'] = isset( $new_instance['show_fullname'] ) ? 1 : 0;
		$instance['hide_empty']    = isset( $new_instance['hide_empty'] )    ? 1 : 0;

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

		$order = array( 
			'ASC'  => esc_attr__( 'Ascending',  'hybrid-core' ), 
			'DESC' => esc_attr__( 'Descending', 'hybrid-core' ) 
		);

		$orderby = array( 
			'display_name' => esc_attr__( 'Display Name', 'hybrid-core' ), 
			'email'        => esc_attr__( 'Email',        'hybrid-core' ), 
			'ID'           => esc_attr__( 'ID',           'hybrid-core' ), 
			'nicename'     => esc_attr__( 'Nice Name',    'hybrid-core' ), 
			'post_count'   => esc_attr__( 'Post Count',   'hybrid-core' ), 
			'registered'   => esc_attr__( 'Registered',   'hybrid-core' ), 
			'url'          => esc_attr__( 'URL',          'hybrid-core' ), 
			'user_login'   => esc_attr__( 'Login',        'hybrid-core' ) 
		);

		$style = array( 
			'list' => esc_attr__( 'List', 'hybrid-core'), 
			'none' => esc_attr__( 'None', 'hybrid-core' ) 
		);

		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
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
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="number" class="smallfat code" size="5" min="0" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" placeholder="0" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><code>style</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<?php foreach ( $style as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['style'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" placeholder="1,2,3&hellip;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed' ); ?>"><code>feed</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="<?php echo esc_attr( $instance['feed'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"><code>feed_image</code></label>
			<input type="url" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo esc_attr( $instance['feed_image'] ); ?>" placeholder="<?php echo esc_attr( home_url( 'images/example.png' ) ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'html' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['html'], true ); ?> id="<?php echo $this->get_field_id( 'html' ); ?>" name="<?php echo $this->get_field_name( 'html' ); ?>" /> <?php _e( '<acronym title="Hypertext Markup Language">HTML</acronym>?', 'hybrid-core' ); ?> <code>html</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'optioncount' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['optioncount'], true ); ?> id="<?php echo $this->get_field_id( 'optioncount' ); ?>" name="<?php echo $this->get_field_name( 'optioncount' ); ?>" /> <?php _e( 'Show post count?', 'hybrid-core' ); ?> <code>optioncount</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_admin' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['exclude_admin'], true ); ?> id="<?php echo $this->get_field_id( 'exclude_admin' ); ?>" name="<?php echo $this->get_field_name( 'exclude_admin' ); ?>" /> <?php _e( 'Exclude admin?', 'hybrid-core' ); ?> <code>exclude_admin</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_fullname' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_fullname'], true ); ?> id="<?php echo $this->get_field_id( 'show_fullname' ); ?>" name="<?php echo $this->get_field_name( 'show_fullname' ); ?>" /> <?php _e( 'Show full name?', 'hybrid-core' ); ?> <code>show_fullname</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" /> <?php _e( 'Hide empty?', 'hybrid-core' ); ?> <code>hide_empty</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}
