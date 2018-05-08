<?php

namespace Hybrid\MediaMeta\Meta;

class Year extends Meta {

	protected $name = 'year';

	protected $sanitize_callback = 'absint';
}
