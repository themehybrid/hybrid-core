<?php

namespace Hybrid\Site;

use Hybrid\TemplateTag\Tag;

class Description extends Tag {

	protected $name    = 'site-description';
	protected $context = '';

	protected $tag = 'div';
	protected $component = 'app-header';

	public function fetch() {

		$html = '';
		$desc = get_bloginfo( 'description', 'display' );

		if ( $desc ) {

			$attr = [];

			if ( $this->component ) {
				$attr['class'] = "{$this->component}__description";
			}

			$html = sprintf(
				'<%1$s %2$s>%3$s</%1$s>',
				tag_escape( $this->tag ),
				$this->attr( $attr )->fetch(),
				$desc
			);
		}

		return apply_filters( 'hybrid/site/description', $html );
	}
}
