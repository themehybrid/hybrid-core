<?php
/**
 * Functions for handling media (i.e., attachments) within themes.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters for the audio transcript. */
add_filter( 'hybrid_audio_transcript', 'wptexturize',   10 );
add_filter( 'hybrid_audio_transcript', 'convert_chars', 20 );
add_filter( 'hybrid_audio_transcript', 'wpautop',       25 );
