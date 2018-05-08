<?php

namespace Hybrid\MediaMeta\Meta;

class Aperture extends Meta {

	protected $name = 'aperture';

	public function fetch() {

		$aperture = $this->raw();

		if ( $aperture ) {

			$aperture = sprintf(
				'<sup>f</sup>&#8260;<sub>%s</sub>',
				absint( $aperture )
			);
		}

		return $aperture;
	}
}
