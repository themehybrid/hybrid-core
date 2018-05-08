<?php

namespace Hybrid\MediaMeta\Meta;

class Dimensions extends Meta {

	public function raw() {

		$dimensions = [];

		if ( ! empty( $this->meta['width'] ) && ! empty( $this->meta['height'] ) ) {

			$dimensions['width']  = $this->meta['width'];
			$dimensions['height'] = $this->meta['height'];
		}

		return $dimensions;
	}

	public function fetch() {

		$dimensions = $this->raw();

		if ( $dimensions ) {

			return sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $dimensions['width']  ) ),
				number_format_i18n( absint( $dimensions['height'] ) )
			);
		}

		return '';
	}
}
