<?php

namespace Hybrid\Post;

use Hybrid\TemplateTag\Tag;

class Terms extends Tag {

	protected $name = 'entry-terms';

	protected $taxonomy = 'category';
	protected $text = '%s';
	protected $sep = '';

	public function __construct( array $args = [] ) {

		if ( ! isset( $args['sep'] ) ) {

			// Translators: Separates tags, categories, etc. when displaying a post.
			$args['sep'] = _x( ', ', 'taxonomy terms separator', 'hybrid-core' );
		}

		parent::__construct( $args );

		$this->context = $this->taxonomy;
	}

	public function fetch() {

		$html = '';

		$terms = get_the_term_list( get_the_ID(), $this->taxonomy, '', $this->sep, '' );

		if ( $terms ) {

			$attr = [
				'class' => "entry__terms entry__terms--{$this->taxonomy}"
			];

			$html = sprintf(
				'<span %s>%s</span>',
				$this->attr( $attr )->fetch(),
				sprintf( $this->text, $terms )
			);
		}

		return $this->wrap( $html );
	}
}
