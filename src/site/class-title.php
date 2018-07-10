<?php

namespace Hybrid\Site;

use Hybrid\TemplateTag\Tag;

class Title extends Tag {

	protected $name    = 'site-title';
	protected $context = '';

	protected $tag = '';
	protected $component = 'app-header';

	public function fetch() {

		$html  = '';
		$title = get_bloginfo( 'name', 'display' );

		if ( ! $this->tag ) {
			$this->tag = is_front_page() ? 'h1' : 'div';
		}

		if ( $title ) {
			$link = sprintf( '<a href="%s">%s</a>', esc_url( home_url() ), $title );

			$attr = [];

			if ( $this->component ) {
				$attr['class'] = "{$this->component}__title";
			}

			$html = sprintf(
				'<%1$s %2$s>%3$s</%1$s>',
				tag_escape( $this->tag ),
				$this->attr( $attr )->fetch(),
				$link
			);
		}

		return apply_filters( 'hybrid/site/title', $html );
	}
}
