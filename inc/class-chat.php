<?php
/**
 * Class that takes chat text and turns it into a nice, HTML-formatted transcript.  This is meant to
 * be used alongside the "chat" post format.
 *
 * @package    Hybrid
 * @subpackage Includes
 * @author     David Chandra <david.warna@gmail.com>
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @link       http://justintadlock.com/archives/2012/08/21/post-formats-chat
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class for handling chat transcripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
class Hybrid_Chat {

	/**
	 * Holds the array of authors in the chat.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $authors = array();

	/**
	 * Holds the array of stanzas in the chat.  Stanzas are groups of text.  Generally, these
	 * will belong to different speakers, but multiple stanzas, back-to-back can belong to
	 * a single author.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $stanzas = array();

	/**
	 * The chat content to be formatted.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $content = '';

	/**
	 * Separator between stanza author and message.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $separator = ':';

	/**
	 * Constructor method.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $content
	 * @return void
	 */
	public function __construct( $content ) {

		$this->content   = $content;
		$this->separator = apply_filters( 'hybrid_post_format_chat_separator', $this->separator );

		// Filters for the chat author.
		add_filter( 'hybrid_chat_author', 'strip_tags' );

		// Filters for the chat message.
		add_filter( 'hybrid_chat_text', array( $this, 'format_chat_text' ), 5 );
		add_filter( 'hybrid_chat_text',               'wpautop',            5 );

		// Set the stanzas.
		$this->set_stanzas();
	}

	/**
	 * Returns the formatted chat transcript.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function get_transcript() {

		$output = $current_author = '';

		// Loop through the stanzas.
		foreach ( $this->stanzas as $stanza ) {

			// Get the chat author.
			$author = ! empty( $stanza['author'] ) ? $stanza['author'] : '';

			// If we have an author, set the current author.
			if ( $author )
				$current_author = $author;

			// Get the speaker/row ID.
			$speaker_id = $this->get_author_id( $current_author );

			// Format the time if there was one given.
			$time = ! empty( $stanza['time'] ) ? sprintf( '<time class="chat-timestamp">%s</time> ', esc_html( $stanza['time'] ) ) : '';

			// Add the chat row author.
			if ( $author )
				$author = sprintf( '<div class="chat-author %s vcard">%s<cite class="fn">%s</cite></div>', sanitize_html_class( strtolower( "chat-author-{$author}" ) ), $time, apply_filters( 'hybrid_chat_author', $author ) );

			// Add the chat row text.
			$message = sprintf( '<div class="chat-text">%s</div>', apply_filters( 'hybrid_chat_text', $stanza['message'] ) );

			// Format the entire stanza.
			$output .= sprintf( '<div class="chat-stanza chat-row %s">%s %s</div>', sanitize_html_class( "chat-speaker-{$speaker_id}" ), $author, $message );
		}

		// Wrap the entire output and return.
		return sprintf( '<div class="chat-transcript">%s</div>', $output );
	}

	/**
	 * Finds and stores the stanzas.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	protected function set_stanzas() {

		// Splits the content into multiple stanzas.
		$stanzas = preg_split( "/(\r?\n)+|(<br\s*\/?>\s*)+/", $this->content );

		// Loops through the stanzas and gets individual stanza arrays.
		foreach ( $stanzas as $stanza_content )
			$this->stanzas[] = $this->get_stanza( $stanza_content );
	}

	/**
	 * Gets a specific stanza.  Expected input is a single stanza.
	 *
	 * @todo   Figure out how to split between author/time/message.
	 * @since  3.0.0
	 * @access public
	 * @param  string  $content
	 * @return array
	 */
	protected function get_stanza( $content ) {

		// If a speaker is found, split into array with author/message.
		if ( preg_match( '/(?<!http|https)' . $this->separator . '/', $content  ) ) {

			list( $author, $message ) = explode( $this->separator, trim( $content ), 2 );

			return array( 'author' => $author, 'message' => $message );
		}

		// Return just the message.
		return array( 'author' => '', 'message' => $content ? $content : '' );
	}

	/**
	 * Gets an ID based on the author name.  This keeps track of how many authors there are so
	 * that chats with multiple people will have a unique ID based on the person.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $author
	 * @return int
	 */
	protected function get_author_id( $author ) {

		// Let's sanitize the chat author to avoid craziness and differences like "John" and "john".
		$author = strtolower( strip_tags( $author ) );

		// Add the chat author to the array.
		if ( ! in_array( $author, $this->authors ) )
			$this->authors[] = $author;

		// Return the array key for the chat author and add "1" to avoid an ID of "0".
		return absint( array_search( $author, $this->authors ) ) + 1;
	}

	/**
	 * Formats the chat text to remove crazy whitespace.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $text
	 * @return string
	 */
	public function format_chat_text( $text ) {

		return trim( str_replace( array( "\r", "\n", "\t" ), '', $text ) );
	}
}
