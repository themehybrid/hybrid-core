<?php

namespace Hybrid\MediaMeta;

class Factory {

	public static function make( $type, $post_id, $meta ) {

		if ( 'filesize' === $type ) {
			$type = 'file_size';
		}

		$meta_class = str_replace(
			'_',
			'',
			__NAMESPACE__ . '\\Meta\\' . ucwords( $type, '_' )
		);

		$args = [
			'name' => $type,
			'meta' => $meta
		];

		$class = class_exists( $meta_class ) ? $meta_class : __NAMESPACE__ . '\\Meta\\Meta';

		return new $class( $post_id, $args );
	}
}
