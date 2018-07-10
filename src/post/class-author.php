<?php

namespace Hybrid\Post;

use Hybrid\TemplateTag\Tag;

class Author extends Tag {

	protected $name = 'entry-author';

	protected $text = '%s';

	public function fetch() {

		$attr = [
			'href'  => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			'class' => 'entry__author'
		];

		$el = sprintf(
			'<a %s>%s</a>',
			$this->attr( $attr )->fetch(),
			sprintf( $this->text, get_the_author() )
		);

		return $this->before . $el . $this->after;
	}
}
