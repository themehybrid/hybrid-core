<?php

namespace Hybrid\Post;

use Hybrid\TemplateTag\Tag;

class CommentsLink extends Tag {

	protected $name = 'entry-comments';

	protected $zero = false;
	protected $one = false;
	protected $more = false;

	public function fetch() {

		$number = get_comments_number();

		if ( 0 == $number && ! comments_open() && ! pings_open() ) {
			return '';
		}

		$attr = [
			'href'  => get_comments_link(),
			'class' => 'entry__comments'
		];

		$text = get_comments_number( $this->zero, $this->one, $this->more );

		$el = sprintf(
			'<a %s>%s</a>',
			$this->attr( $attr ),
			$text
		);

		return $this->wrap( $el );
	}
}
