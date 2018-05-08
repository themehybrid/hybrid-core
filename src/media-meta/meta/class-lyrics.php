<?php

namespace Hybrid\MediaMeta\Meta;

class Lyrics extends Meta {

	protected $name = 'unsychronised_lyric';

	public function raw() {

		$lyrics = '';

		// Look for the 'unsynchronised_lyric' tag.
		if ( isset( $this->meta['unsynchronised_lyric'] ) ) {
			$lyrics = $this->meta['unsynchronised_lyric'];

		// Seen this misspelling of the id3 tag.
		} elseif ( isset( $this->meta['unsychronised_lyric'] ) ) {
			$lyrics = $this->meta['unsychronised_lyric'];
		}

		return $lyrics;
	}

	public function fetch() {

		$lyrics = $this->raw();

		if ( $lyrics ) {
			$lyrics = strip_tags( $lyrics );
			$lyrics = wptexturize( $lyrics );
			$lyrics = convert_chars( $lyrics );
			$lyrics = wpautop( $lyrics );
		}

		return $lyrics;
	}
}
