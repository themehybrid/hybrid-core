<?php

namespace Hybrid\MediaMeta\Meta;

class FileName extends Meta {

	protected $name = 'file_name';

	public function raw() {

		return basename( get_attached_file( $this->post_id ) );

		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_get_attachment_url( $this->post_id ) ),
			basename( get_attached_file( $this->post_id ) )
		);
	}

	public function fetch() {

		$filename = $this->raw();

		if ( $filename ) {

			$filename = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_get_attachment_url( $this->post_id ) ),
				$filename
			);
		}

		return $filename;
	}
}
