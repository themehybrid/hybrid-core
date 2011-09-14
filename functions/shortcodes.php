<?php
/**
 * Shortcodes bundled for use with themes.  These shortcodes can be used in any shortcode-ready area, 
 * which includes the post content area.  Themes may optionally make alternate shortcode-aware areas 
 * where these shortcodes may be used.  Note that some shortcodes are specific to posts and comments 
 * and would be useless outside of the post and comment loops.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Register shortcodes. */
add_action( 'init', 'hybrid_add_shortcodes' );

/**
 * Creates new shortcodes for use in any shortcode-ready area.  This function uses the add_shortcode() 
 * function to register new shortcodes with WordPress.
 *
 * @since 0.8.0
 * @uses add_shortcode() to create new shortcodes.
 * @link http://codex.wordpress.org/Shortcode_API
 */
function hybrid_add_shortcodes() {

	/* Add theme-specific shortcodes. */
	add_shortcode( 'the-year', 'hybrid_the_year_shortcode' );
	add_shortcode( 'site-link', 'hybrid_site_link_shortcode' );
	add_shortcode( 'wp-link', 'hybrid_wp_link_shortcode' );
	add_shortcode( 'theme-link', 'hybrid_theme_link_shortcode' );
	add_shortcode( 'child-link', 'hybrid_child_link_shortcode' );
	add_shortcode( 'loginout-link', 'hybrid_loginout_link_shortcode' );
	add_shortcode( 'query-counter', 'hybrid_query_counter_shortcode' );
	add_shortcode( 'nav-menu', 'hybrid_nav_menu_shortcode' );

	/* Add entry-specific shortcodes. */
	add_shortcode( 'entry-title', 'hybrid_entry_title_shortcode' );
	add_shortcode( 'entry-author', 'hybrid_entry_author_shortcode' );
	add_shortcode( 'entry-terms', 'hybrid_entry_terms_shortcode' );
	add_shortcode( 'entry-comments-link', 'hybrid_entry_comments_link_shortcode' );
	add_shortcode( 'entry-published', 'hybrid_entry_published_shortcode' );
	add_shortcode( 'entry-edit-link', 'hybrid_entry_edit_link_shortcode' );
	add_shortcode( 'entry-shortlink', 'hybrid_entry_shortlink_shortcode' );

	/* Add comment-specific shortcodes. */
	add_shortcode( 'comment-published', 'hybrid_comment_published_shortcode' );
	add_shortcode( 'comment-author', 'hybrid_comment_author_shortcode' );
	add_shortcode( 'comment-edit-link', 'hybrid_comment_edit_link_shortcode' );
	add_shortcode( 'comment-reply-link', 'hybrid_comment_reply_link_shortcode' );
	add_shortcode( 'comment-permalink', 'hybrid_comment_permalink_shortcode' );
}

/**
 * Shortcode to display the current year.
 *
 * @since 0.6.0
 * @uses date() Gets the current year.
 */
function hybrid_the_year_shortcode() {
	return date( __( 'Y', hybrid_get_textdomain() ) );
}

/**
 * Shortcode to display a link back to the site.
 *
 * @since 0.6.0
 * @uses get_bloginfo() Gets information about the install.
 */
function hybrid_site_link_shortcode() {
	return '<a class="site-link" href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home"><span>' . get_bloginfo( 'name' ) . '</span></a>';
}

/**
 * Shortcode to display a link to WordPress.org.
 *
 * @since 0.6.0
 */
function hybrid_wp_link_shortcode() {
	return '<a class="wp-link" href="http://wordpress.org" title="' . esc_attr__( 'Powered by WordPress, state-of-the-art semantic personal publishing platform', hybrid_get_textdomain() ) . '"><span>' . __( 'WordPress', hybrid_get_textdomain() ) . '</span></a>';
}

/**
 * Shortcode to display a link to the Hybrid theme page.
 *
 * @since 0.6.0
 * @uses get_theme_data() Gets theme (parent theme) information.
 */
function hybrid_theme_link_shortcode() {
	$data = hybrid_get_theme_data();
	return '<a class="theme-link" href="' . esc_url( $data['URI'] ) . '" title="' . esc_attr( $data['Name'] ) . '"><span>' . esc_attr( $data['Name'] ) . '</span></a>';
}

/**
 * Shortcode to display a link to the child theme's page.
 *
 * @since 0.6.0
 * @uses get_theme_data() Gets theme (child theme) information.
 */
function hybrid_child_link_shortcode() {
	$data = hybrid_get_theme_data( 'stylesheet' );
	return '<a class="child-link" href="' . esc_url( $data['URI'] ) . '" title="' . esc_attr( $data['Name'] ) . '"><span>' . esc_html( $data['Name'] ) . '</span></a>';
}

/**
 * Shortcode to display a login link or logout link.
 *
 * @since 0.6.0
 * @uses is_user_logged_in() Checks if the current user is logged into the site.
 * @uses wp_logout_url() Creates a logout URL.
 * @uses wp_login_url() Creates a login URL.
 */
function hybrid_loginout_link_shortcode() {
	$domain = hybrid_get_textdomain();
	if ( is_user_logged_in() )
		$out = '<a class="logout-link" href="' . esc_url( wp_logout_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Log out of this account', $domain ) . '">' . __( 'Log out', $domain ) . '</a>';
	else
		$out = '<a class="login-link" href="' . esc_url( wp_login_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Log into this account', $domain ) . '">' . __( 'Log in', $domain ) . '</a>';

	return $out;
}

/**
 * Displays query count and load time if the current user can edit themes.
 *
 * @since 0.6.0
 * @uses current_user_can() Checks if the current user can edit themes.
 */
function hybrid_query_counter_shortcode() {
	if ( current_user_can( 'edit_theme_options' ) )
		return sprintf( __( 'This page loaded in %1$s seconds with %2$s database queries.', hybrid_get_textdomain() ), timer_stop( 0, 3 ), get_num_queries() );
	return '';
}

/**
 * Displays a nav menu that has been created from the Menus screen in the admin.
 *
 * @since 0.8.0
 * @uses wp_nav_menu() Displays the nav menu.
 */
function hybrid_nav_menu_shortcode( $attr ) {

	$attr = shortcode_atts(
		array(
			'menu' => '',
			'container' => 'div',
			'container_id' => '',
			'container_class' => 'nav-menu',
			'menu_id' => '',
			'menu_class' => '',
			'link_before' => '',
			'link_after' => '',
			'before' => '',
			'after' => '',
			'fallback_cb' => 'wp_page_menu',
			'walker' => ''
		),
		$attr
	);
	$attr['echo'] = false;

	return wp_nav_menu( $attr );
}

/**
 * Displays the edit link for an individual post.
 *
 * @since 0.7.0
 * @param array $attr
 */
function hybrid_entry_edit_link_shortcode( $attr ) {
	global $post;

	$domain = hybrid_get_textdomain();
	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post->ID ) )
		return '';

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

	return $attr['before'] . '<span class="edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( $post->ID ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', $domain ), $post_type->labels->singular_name ) . '">' . __( 'Edit', $domain ) . '</a></span>' . $attr['after'];
}

/**
 * Displays the published date of an individual post.
 *
 * @since 0.7.0
 * @param array $attr
 */
function hybrid_entry_published_shortcode( $attr ) {
	$domain = hybrid_get_textdomain();
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'format' => get_option( 'date_format' ) ), $attr );

	$published = '<abbr class="published" title="' . sprintf( get_the_time( esc_attr__( 'l, F jS, Y, g:i a', $domain ) ) ) . '">' . sprintf( get_the_time( $attr['format'] ) ) . '</abbr>';
	return $attr['before'] . $published . $attr['after'];
}

/**
 * Displays a post's number of comments wrapped in a link to the comments area.
 *
 * @since 0.7.0
 * @param array $attr
 */
function hybrid_entry_comments_link_shortcode( $attr ) {

	$domain = hybrid_get_textdomain();
	$comments_link = '';
	$number = doubleval( get_comments_number() );
	$attr = shortcode_atts( array( 'zero' => __( 'Leave a response', $domain ), 'one' => __( '%1$s Response', $domain ), 'more' => __( '%1$s Responses', $domain ), 'css_class' => 'comments-link', 'none' => '', 'before' => '', 'after' => '' ), $attr );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		if ( $attr['none'] )
			$comments_link = '<span class="' . esc_attr( $attr['css_class'] ) . '">' . sprintf( $attr['none'], number_format_i18n( $number ) ) . '</span>';
	}
	elseif ( 0 == $number )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_permalink() . '#respond" title="' . sprintf( esc_attr__( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $attr['zero'], number_format_i18n( $number ) ) . '</a>';
	elseif ( 1 == $number )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $attr['one'], number_format_i18n( $number ) ) . '</a>';
	elseif ( 1 < $number )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $attr['more'], number_format_i18n( $number ) ) . '</a>';

	if ( $comments_link )
		$comments_link = $attr['before'] . $comments_link . $attr['after'];

	return $comments_link;
}

/**
 * Displays an individual post's author with a link to his or her archive.
 *
 * @since 0.7.0
 * @param array $attr
 */
function hybrid_entry_author_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$author = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
	return $attr['before'] . $author . $attr['after'];
}

/**
 * Displays a list of terms for a specific taxonomy.
 *
 * @since 0.7.0
 * @param array $attr
 */
function hybrid_entry_terms_shortcode( $attr ) {
	global $post;

	$attr = shortcode_atts( array( 'id' => $post->ID, 'taxonomy' => 'post_tag', 'separator' => ', ', 'before' => '', 'after' => '' ), $attr );

	$attr['before'] = ( empty( $attr['before'] ) ? '<span class="' . $attr['taxonomy'] . '">' : '<span class="' . $attr['taxonomy'] . '"><span class="before">' . $attr['before'] . '</span>' );
	$attr['after'] = ( empty( $attr['after'] ) ? '</span>' : '<span class="after">' . $attr['after'] . '</span></span>' );

	return get_the_term_list( $attr['id'], $attr['taxonomy'], $attr['before'], $attr['separator'], $attr['after'] );
}

/**
 * Displays a post's title with a link to the post.
 *
 * @since 0.7.0
 */
function hybrid_entry_title_shortcode() {
	global $post;

	if ( is_front_page() && !is_home() )
		$title = the_title( '<h2 class="' . esc_attr( $post->post_type ) . '-title entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>', false );

	elseif ( is_singular() )
		$title = the_title( '<h1 class="' . esc_attr( $post->post_type ) . '-title entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h1>', false );

	elseif ( 'link_category' == get_query_var( 'taxonomy' ) )
		$title = false;

	else
		$title = the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>', false );

	/* If there's no post title, return a clickable '(No title)'. */
	if ( empty( $title ) && !is_singular() && 'link_category' !== get_query_var( 'taxonomy' ) )
		$title = '<h2 class="entry-title no-entry-title"><a href="' . get_permalink() . '" rel="bookmark">' . __( '(Untitled)', hybrid_get_textdomain() ) . '</a></h2>';

	return $title;
}

/**
 * Displays the shortlinke of an individual entry.
 *
 * @since 0.8.0
 */
function hybrid_entry_shortlink_shortcode( $attr ) {
	global $post;

	$domain = hybrid_get_textdomain();

	$attr = shortcode_atts(
		array(
			'text' => __( 'Shortlink', $domain ),
			'title' => the_title_attribute( array( 'echo' => false ) ),
			'before' => '',
			'after' => ''
		),
		$attr
	);

	$shortlink = esc_url( wp_get_shortlink( $post->ID ) );

	return "{$attr['before']}<a class='shortlink' href='{$shortlink}' title='" . esc_attr( $attr['title'] ) . "' rel='shortlink'>{$attr['text']}</a>{$attr['after']}";
}

/**
 * Displays the published date and time of an individual comment.
 *
 * @since 0.7.0
 */
function hybrid_comment_published_shortcode() {
	$domain = hybrid_get_textdomain();
	$link = '<span class="published">' . sprintf( __( '%1$s at %2$s', $domain ), '<abbr class="comment-date" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', $domain ) ) . '">' . get_comment_date() . '</abbr>', '<abbr class="comment-time" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', $domain ) ) . '">' . get_comment_time() . '</abbr>' ) . '</span>';
	return $link;
}

/**
 * Displays the comment author of an individual comment.
 *
 * @since 0.8.0
 * @global $comment The current comment's DB object.
 * @return string
 */
function hybrid_comment_author_shortcode( $attr ) {
	global $comment;

	$attr = shortcode_atts(
		array(
			'before' => '',
			'after' => '',
			'tag' => 'span' // @deprecated 1.2.0 Back-compatibility. Please don't use this argument.
		),
		$attr
	);

	$author = esc_html( get_comment_author( $comment->comment_ID ) );
	$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

	/* Display link and cite if URL is set. Also, properly cites trackbacks/pingbacks. */
	if ( $url )
		$output = '<cite class="fn" title="' . $url . '"><a href="' . $url . '" title="' . esc_attr( $author ) . '" class="url" rel="external nofollow">' . $author . '</a></cite>';
	else
		$output = '<cite class="fn">' . $author . '</cite>';

	$output = '<' . tag_escape( $attr['tag'] ) . ' class="comment-author vcard">' . $attr['before'] . apply_filters( 'get_comment_author_link', $output ) . $attr['after'] . '</' . tag_escape( $attr['tag'] ) . '><!-- .comment-author .vcard -->';

	return $output;
}

/**
 * Displays the permalink to an individual comment.
 *
 * @since 0.7.0
 */
function hybrid_comment_permalink_shortcode( $attr ) {
	global $comment;

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$domain = hybrid_get_textdomain();
	$link = '<a class="permalink" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" title="' . sprintf( esc_attr__( 'Permalink to comment %1$s', $domain ), $comment->comment_ID ) . '">' . __( 'Permalink', $domain ) . '</a>';
	return $attr['before'] . $link . $attr['after'];
}

/**
 * Displays a comment's edit link to users that have the capability to edit the comment.
 *
 * @since 0.7.0
 */
function hybrid_comment_edit_link_shortcode( $attr ) {
	global $comment;

	$edit_link = get_edit_comment_link( $comment->comment_ID );

	if ( !$edit_link )
		return '';

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$domain = hybrid_get_textdomain();

	$link = '<a class="comment-edit-link" href="' . esc_url( $edit_link ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', $domain ), $comment->comment_type ) . '"><span class="edit">' . __( 'Edit', $domain ) . '</span></a>';
	$link = apply_filters( 'edit_comment_link', $link, $comment->comment_ID );

	return $attr['before'] . $link . $attr['after'];
}

/**
 * Displays a reply link for the 'comment' comment_type if threaded comments are enabled.
 *
 * @since 0.7.0
 */
function hybrid_comment_reply_link_shortcode( $attr ) {
	$domain = hybrid_get_textdomain();

	if ( !get_option( 'thread_comments' ) || 'comment' !== get_comment_type() )
		return '';

	$defaults = array(
		'reply_text' => __( 'Reply', $domain ),
		'login_text' => __( 'Log in to reply.', $domain ),
		'depth' => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' ),
		'before' => '',
		'after' => ''
	);
	$attr = shortcode_atts( $defaults, $attr );

	return get_comment_reply_link( $attr );
}

?>