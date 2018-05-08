<?php

namespace Hybrid\MediaMeta\Meta;

class ShutterSpeed extends Meta {

	protected $name = 'shutter_speed';

	public function fetch() {

		$shutter = $this->raw();

		// If a shutter speed is given, format the float into a fraction.
		if ( $shutter ) {

			$shutter = $speed = floatval( strip_tags( $shutter ) );

			if ( ( 1 / $speed ) > 1 ) {
				$shutter = sprintf( '<sup>%s</sup>&#8260;', number_format_i18n( 1 ) );

				if ( number_format( ( 1 / $speed ), 1 ) ==  number_format( ( 1 / $speed ), 0 ) ) {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 0, '.', '' )
					);

				} else {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 1, '.', '' )
					);
				}
			}

			// Translators: %s is the shutter speed of a camera.
			$shutter = sprintf( __( '%s sec', 'hybrid-core' ), $shutter );
		}

		return $shutter;
	}
}
