<?php

namespace Hybrid\MediaMeta\Meta;

class CreatedTimestamp extends Meta {

	protected $name = 'created_timestamp';

	public function fetch() {

		$timestamp = $this->raw();

		if ( $timestamp ) {

			$timestamp = date_i18n(
				get_option( 'date_format' ),
				strip_tags( $timestamp )
			);
		}

		return $timestamp;
	}
}
