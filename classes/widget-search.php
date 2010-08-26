<?php
/**
 * Search Widget Class
 *
 * The Search widget replaces the default WordPress Search widget. This version gives total
 * control over the output to the user by allowing the input of various arguments that typically
 * represent a search form. It also gives the user the option of using the theme's search form
 * through the use of the get_search_form() function.
 *
 * @since 0.6
 * @link http://themehybrid.com/themes/hybrid/widgets
 *
 * @package Hybrid
 * @subpackage Classes
 */

class Hybrid_Widget_Search extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Hybrid_Widget_Search() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();

		$widget_ops = array( 'classname' => 'search', 'description' => __( 'An advanced widget that gives you total control over the output of your search form.', $this->textdomain ) );
		$control_ops = array( 'width' => 525, 'height' => 350, 'id_base' => "{$this->prefix}-search" );
		$this->WP_Widget( "{$this->prefix}-search", __( 'Search', $this->textdomain ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$theme_search = isset( $instance['theme_search'] ) ? $instance['theme_search'] : false;

		echo $before_widget;

		/* If there is a title given, add it along with the $before_title and $after_title variables. */
		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		if ( $theme_search )
			get_search_form();

		else {
			global $search_form_num;

			$search_num = ( ( $search_form_num ) ? "-{$search_form_num}" : '' );
			$search_text = ( ( is_search() ) ? esc_attr( get_search_query() ) : esc_attr( $instance['search_text'] ) );

			$search = '<form method="get" class="search-form" id="search-form' . $search_num . '" action="' . home_url() . '/"><div>';

			if ( $instance['search_label'] )
				$search .= '<label for="search-text' . $search_num . '">' . $instance['search_label'] . '</label>';

			$search .= '<input class="search-text" type="text" name="s" id="search-text' . $search_num . '" value="' . esc_attr( $search_text ) . '" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;" />';

			if ( $instance['search_submit'] )
				$search .= '<input class="search-submit button" name="submit" type="submit" id="search-submit' . $search_num . '" value="' . esc_attr( $instance['search_submit'] ) . '" />';

			$search .= '</div></form><!-- .search-form -->';

			echo $search;

			$search_form_num++;
		}

		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['search_label'] = strip_tags( $new_instance['search_label'] );
		$instance['search_text'] = strip_tags( $new_instance['search_text'] );
		$instance['search_submit'] = strip_tags( $new_instance['search_submit'] );
		$instance['theme_search'] = ( isset( $new_instance['theme_search'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Search', $this->textdomain ),
			'theme_search' => false,
			'search_label' => '',
			'search_text' => '',
			'search_submit' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_label' ); ?>"><?php _e( 'Search Label:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_label' ); ?>" name="<?php echo $this->get_field_name( 'search_label' ); ?>" value="<?php echo $instance['search_label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_text' ); ?>"><?php _e( 'Search Text:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_text' ); ?>" name="<?php echo $this->get_field_name( 'search_text' ); ?>" value="<?php echo $instance['search_text']; ?>" />
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'search_submit' ); ?>"><?php _e( 'Search Submit:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_submit' ); ?>" name="<?php echo $this->get_field_name( 'search_submit' ); ?>" value="<?php echo $instance['search_submit']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'theme_search' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['theme_search'], true ); ?> id="<?php echo $this->get_field_id( 'theme_search' ); ?>" name="<?php echo $this->get_field_name( 'theme_search' ); ?>" /> <?php _e( 'Use theme\'s <code>searchform.php</code>?', $this->textdomain ); ?></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>