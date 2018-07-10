<?php

namespace Hybrid\Post;

use Hybrid\TemplateTag\Tag;

class Format extends Tag {

	protected $name = 'entry-published';

	protected $text = '%s';

	public function fetch() {

		$format = get_post_format();
		$url    = $format ? get_post_format_link( $format ) : get_permalink();
		$string = get_post_format_string( $format );

		$attr = [
			'href' => $url,
			'class' => 'entry__format'
		];

		$html = sprintf(
			'<a %s>%s</a>',
			$this->attr( $attr )->fetch(),
			sprintf( $this->text, $string )
		);

		return $this->wrap( $html );
	}
}
