<?php

namespace Hybrid\MediaMeta\Meta;

class FocalLength extends Meta {

	protected $name = 'focal_length';

	public function fetch() {

		$focal_length = $this->raw();

		if ( $focal_length ) {

			// Translators: %s is the focal length of a camera.
			$focal_length = sprintf(
				__( '%s mm', 'hybrid-core' ),
				absint( $focal_length )
			);
		}

		return $focal_length;
	}
}
