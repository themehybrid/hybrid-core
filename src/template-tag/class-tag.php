<?php

namespace Hybrid\TemplateTag;

use Hybrid\Contracts\TemplateTag;
use Hybrid\Attr\Attr;

abstract class Tag implements TemplateTag {

	protected $name = 'default';
	protected $context = '';
	protected $before = '';
	protected $after = '';

	public function __construct( array $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}
	}

	public function render() {

		echo $this->fetch();
	}

	abstract public function fetch();

	public function attr( array $attr = [] ) {

		return new Attr( $this->name, $this->context, $attr );
	}

	protected function wrap( $html ) {

		return $html ? $this->before . $html . $this->after : '';
	}
}
