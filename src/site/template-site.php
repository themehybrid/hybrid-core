<?php

namespace Hybrid\Site;

function render_title( array $args = [] ) {

	( new Title( $args ) )->render();
}

function fetch_title( array $args = [] ) {

	return ( new Title( $args ) )->fetch();
}

function render_description( array $args = [] ) {

	( new Description( $args ) )->render();
}

function fetch_description( array $args = [] ) {

	return ( new Description( $args ) )->fetch();
}
