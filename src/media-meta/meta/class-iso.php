<?php

namespace Hybrid\MediaMeta\Meta;

class Iso extends Meta {

	protected $name = 'iso';

	protected $sanitize_callback = 'absint';
}
