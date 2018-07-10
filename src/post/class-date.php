<?php

namespace Hybrid\Post;

use Hybrid\TemplateTag\Tag;

class Date extends Tag {

	protected $name = 'entry-published';

	protected $text = '%s';
	protected $format = '';

	public function fetch() {

		$attr = [ 'class' => 'entry__published' ];

		$html = sprintf(
			'<time %s>%s</time>',
			$this->attr( $attr )->fetch(),
			sprintf( $this->text, get_the_date( $this->format ) )
		);

		return $this->wrap( $html );
	}
}
