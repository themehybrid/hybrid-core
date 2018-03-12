<?php
/**
 * The framework has its own template hierarchy that can be used instead of the default WordPress
 * template hierarchy.  It is not much different than the default.  It was built to extend the default by
 * making it smarter and more flexible.  The goal is to give theme developers and end users an
 * easy-to-override system that doesn't involve massive amounts of conditional tags within files.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Overwrites the core WP template hierarchy.
 *
 * @since  4.0.0
 * @access public
 */
final class Hybrid_Template_Hiearchy {

	/**
	 * Returns the instance.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up template hierarchy filters.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function setup() {

		// Filter the front page template.
		add_filter( 'frontpage_template_hierarchy',  array( $this, 'front_page' ), 5 );

		// Filter the single, page, and attachment templates.
		add_filter( 'single_template_hierarchy',     array( $this, 'single' ), 5 );
		add_filter( 'page_template_hierarchy',       array( $this, 'single' ), 5 );
		add_filter( 'attachment_template_hierarchy', array( $this, 'single' ), 5 );

		// Filter taxonomy templates.
		add_filter( 'taxonomy_template_hierarchy', array( $this, 'taxonomy' ), 5 );
		add_filter( 'category_template_hierarchy', array( $this, 'taxonomy' ), 5 );
		add_filter( 'tag_template_hierarchy',      array( $this, 'taxonomy' ), 5 );

		// Filter the author template.
		add_filter( 'author_template_hierarchy', array( $this, 'author' ), 5 );

		// Filter the date template.
		add_filter( 'date_template_hierarchy', array( $this, 'date' ), 5 );
	}

	/**
	 * Fix for the front page template handling in WordPress core. Its handling is
	 * not logical because it forces devs to account for both a page on the front page
	 * and posts on the front page.  Theme devs must handle both scenarios if they've
	 * created a "front-page.php" template.  This filter overwrites that and disables
	 * the "front-page.php" template if posts are to be shown on the front page.  This
	 * way, the "front-page.php" template will only ever be used if an actual page is
	 * supposed to be shown on the front.
	 *
	 * Additionally, this filter allows the user to override the front page via the
	 * standard page template.  User choice should always trump developer choice.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  array   $templates
	 * @return array
	 */
	public function front_page( $templates ) {

		$templates = array();

		if ( ! is_home() ) {

			$custom = hybrid_get_post_template( get_queried_object_id() );

			if ( $custom )
				$templates[] = $custom;

			$templates[] = 'front-page.php';
		}

		// Return the template hierarchy.
		return $templates;
	}

	/**
	 * Overrides the default single (singular post) template for all post types, including
	 * pages and attachments.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  array   $templates
	 * @return array
	 */
	public function single( $templates ) {

		$templates = array();

		// Get the queried post.
		$post = get_queried_object();

		// Decode the post name.
		$name = urldecode( $post->post_name );

		// Check for a custom post template.
		$custom = hybrid_get_post_template( $post->ID );

		if ( $custom )
			$templates[] = $custom;

		// If viewing an attachment page, handle the files by mime type.
		if ( is_attachment() ) {

			// Split the mime type into two distinct parts.
			$type    = hybrid_get_attachment_type();
			$subtype = hybrid_get_attachment_subtype();

			if ( $subtype ) {
				$templates[] = "attachment-{$type}-{$subtype}.php";
				$templates[] = "attachment-{$subtype}.php";
			}

			$templates[] = "attachment-{$type}.php";

		// If not viewing an attachment page.
		} else {

			// Add a post ID template.
			$templates[] = "single-{$post->post_type}-{$post->ID}.php";
			$templates[] = "{$post->post_type}-{$post->ID}.php";

			// Add a post name (slug) template.
			$templates[] = "single-{$post->post_type}-{$name}.php";
			$templates[] = "{$post->post_type}-{$name}.php";
		}

		// Add a template based off the post type name.
		$templates[] = "single-{$post->post_type}.php";
		$templates[] = "{$post->post_type}.php";

		// Allow for WP standard 'single' template.
		$templates[] = 'single.php';

		// Return the template hierarchy.
		return $templates;
	}

	/**
	 * Overrides WP's default template for taxonomy-based archives. This allows better
	 * organization of taxonomy template files by making categories and post tags work
	 * the same way as other taxonomies.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  array   $templates
	 * @return array
	 */
	public function taxonomy( $template ) {

		$templates = array();

		// Get the queried term object.
		$term = get_queried_object();

		// Remove 'post-format' from the slug.
		$slug = 'post_format' === $term->taxonomy ? hybrid_clean_post_format_slug( $term->slug ) : urldecode( $term->slug );

		// Check for a custom term template.
		$custom = hybrid_get_term_template( get_queried_object_id() );

		if ( $custom )
			$templates[] = $custom;

		// Slug-based template.
		$templates[] = "taxonomy-{$term->taxonomy}-{$slug}.php";

		// Taxonomy-specific template.
		$templates[] = "taxonomy-{$term->taxonomy}.php";

		// Default template.
		$templates[] = 'taxonomy.php';

		// Return the template hierarchy.
		return $templates;
	}

	/**
	 * Overrides WP's default template for author-based archives. Better abstraction
	 * of templates than `is_author()` allows by allowing themes to specify templates
	 * for a specific author.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  array   $templates
	 * @return array
	 */
	public function author( $templates ) {

		$templates = array();

		// Get the user nicename.
		$name = get_the_author_meta( 'user_nicename', get_query_var( 'author' ) );

		// Get the user object.
		$user = new WP_User( absint( get_query_var( 'author' ) ) );

		// Check for a custom user template.
		$custom = hybrid_get_user_template( $user->ID );

		if ( $custom )
			$templates[] = $custom;

		// Add the user nicename template.
		$templates[] = "user-{$name}.php";

		// Add role-based templates for the user.
		if ( is_array( $user->roles ) ) {

			foreach ( $user->roles as $role )
				$templates[] = "user-role-{$role}.php";
		}

		// Add a basic user/author template.
		$templates[] = 'user.php';
		$templates[] = 'author.php';

		// Return the template hierarchy.
		return $templates;
	}

	/**
	 * Overrides WP's default template for date-based archives. Better abstraction of
	 * templates than `is_date()` allows by checking for the year, month, week, day, hour,
	 * and minute.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  array   $templates
	 * @return array
	 */
	public function date( $templates ) {

		$templates = array();

		// If viewing a time-based archive.
		if ( is_time() ) {

			// If viewing a minutely archive.
			if ( get_query_var( 'minute' ) )
				$templates[] = 'minute.php';

			// If viewing an hourly archive.
			elseif ( get_query_var( 'hour' ) )
				$templates[] = 'hour.php';

			// Catchall for any time-based archive.
			$templates[] = 'time.php';

		// If viewing a daily archive.
		} elseif ( is_day() ) {

			$templates[] = 'day.php';

		// If viewing a weekly archive.
		} elseif ( get_query_var( 'w' ) ) {

			$templates[] = 'week.php';

		// If viewing a monthly archive.
		} elseif ( is_month() ) {

			$templates[] = 'month.php';

		// If viewing a yearly archive.
		} elseif ( is_year() ) {

			$templates[] = 'year.php';
		}

		// Catchall template for date-based archives.
		$templates[] = 'date.php';

		// Return the template hierarchy.
		return $templates;
	}
}

Hybrid_Template_Hiearchy::get_instance();
