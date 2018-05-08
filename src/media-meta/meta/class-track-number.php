<?php

namespace Hybrid\MediaMeta\Meta;

class TrackNumber extends Meta {

	protected $name = 'track_number';

	protected $sanitize_callback = 'absint';
}
