<?php
/**
 * The Search widget replaces the default WordPress Search widget. This version gives total
 * control over the output to the user by allowing the input of various arguments that typically
 * represent a search form. It also gives the user the option of using the theme's search form
 * through the use of the get_search_form() function.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Search Widget Class
 *
 * @since 0.6.0
 */
class Hybrid_Widget_Search extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'search',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your search form.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-search',               // $this->id_base
			__( 'Search', 'hybrid-core' ), // $this->name
			$widget_options,               // $this->widget_options
			$control_options               // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.6.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* If the user chose to use the theme's search form, load it. */
		if ( !empty( $instance['theme_search'] ) ) {
			get_search_form();
		}

		/* Else, create the form based on the user-selected arguments. */
		else {

			/* Set up some variables for the search form. */
			if ( empty( $instance['search_text'] ) )
				$instance['search_text'] = '';

			$search_text = ( ( is_search() ) ? esc_attr( get_search_query() ) : esc_attr( $instance['search_text'] ) );

			/* Open the form. */
			$search = '<form method="get" class="search-form" id="search-form' . esc_attr( $this->id_base ) . '" action="' . home_url() . '/"><div>';

			/* If a search label was set, add it. */
			if ( !empty( $instance['search_label'] ) )
				$search .= '<label for="search-text' . esc_attr( $this->id_base ) . '">' . $instance['search_label'] . '</label>';

			/* Search form text input. */
			$search .= '<input class="search-text" type="text" name="s" id="search-text' . esc_attr( $this->id_base ) . '" value="' . $search_text . '" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;" />';

			/* Search form submit button. */
			if ( $instance['search_submit'] )
				$search .= '<input class="search-submit button" name="submit" type="submit" id="search-submit' . esc_attr( $this->id_base ). '" value="' . esc_attr( $instance['search_submit'] ) . '" />';

			/* Close the form. */
			$search .= '</div></form>';

			/* Display the form. */
			echo $search;
		}

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $new_instance;

		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['search_label']  = strip_tags( $new_instance['search_label'] );
		$instance['search_text']   = strip_tags( $new_instance['search_text'] );
		$instance['search_submit'] = strip_tags( $new_instance['search_submit'] );

		$instance['theme_search'] = ( isset( $new_instance['theme_search'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title'         => esc_attr__( 'Search', 'hybrid-core' ),
			'theme_search'  => false,
			'search_label'  => '',
			'search_text'   => '',
			'search_submit' => ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_label' ); ?>"><?php _e( 'Search Label:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_label' ); ?>" name="<?php echo $this->get_field_name( 'search_label' ); ?>" value="<?php echo esc_attr( $instance['search_label'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_text' ); ?>"><?php _e( 'Search Text:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_text' ); ?>" name="<?php echo $this->get_field_name( 'search_text' ); ?>" value="<?php echo esc_attr( $instance['search_text'] ); ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'search_submit' ); ?>"><?php _e( 'Search Submit:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_submit' ); ?>" name="<?php echo $this->get_field_name( 'search_submit' ); ?>" value="<?php echo esc_attr( $instance['search_submit'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'theme_search' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['theme_search'], true ); ?> id="<?php echo $this->get_field_id( 'theme_search' ); ?>" name="<?php echo $this->get_field_name( 'theme_search' ); ?>" /> <?php _e( 'Use theme\'s <code>searchform.php</code>?', 'hybrid-core' ); ?></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>