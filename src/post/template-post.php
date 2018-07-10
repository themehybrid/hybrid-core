<?php

namespace Hybrid\Post;

function render_author( array $args = [] ) {

	( new Author( $args ) )->render();
}

function fetch_author( array $args = [] ) {

	return ( new Author( $args ) )->fetch();
}

function render_date( array $args = [] ) {

	( new Date( $args ) )->render();
}

function fetch_date( array $args = [] ) {

	return ( new Date( $args ) )->fetch();
}

function render_comments_link( array $args = [] ) {

	( new CommentsLink( $args ) )->render();
}

function fetch_comments_link( array $args = [] ) {

	return ( new CommentsLink( $args ) )->fetch();
}

function render_terms( array $args = [] ) {

	( new Terms( $args ) )->render();
}

function fetch_terms( array $args = [] ) {

	return ( new Terms( $args ) )->fetch();
}

function render_format( array $args = [] ) {

	( new Format( $args ) )->render();
}

function fetch_format( array $args = [] ) {

	return ( new Format( $args ) )->fetch();
}
