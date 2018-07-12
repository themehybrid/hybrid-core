<?php

namespace Hybrid\MediaGrabber;

function render( array $args = [] ) {

	( new MediaGrabber( $args ) )->render();
}

function fetch( array $args = [] ) {

	return ( new MediaGrabber( $args ) )->fetch();
}
