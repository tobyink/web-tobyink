<?php

/**
 * Plugin Name: Old posts alert
 * Version: 1.0
 * Author: Toby Inkster
 * Author URI: http://toby.ink/
 */

add_filter( 'the_content', function ( $content ) {
	if ( is_archive() ) return $content;
	$date = 0 + get_the_date( 'Y' );
	if ( $date < 2015 ) {
		$content = '<p class="alert alert-warning my-3">This is a very old article. It has been imported from older blogging software, and the formatting, images, etc may have been lost. Some links may be broken. Some of the information may no longer be correct. Opinions expressed in this article may no longer be held.</p>' . $content;
	}
	elseif ( $date < 2020 ) {
		$content = '<p class="alert alert-info my-3">This is an old article. Some links may be broken. Some of the information may no longer be correct. Opinions expressed in this article may no longer be held.</p>' . $content;
	}
	return $content;
} );
