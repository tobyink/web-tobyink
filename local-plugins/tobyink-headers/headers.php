<?php

/**
 * Plugin Name: Cute headers
 * Version: 1.0
 * Author: Toby Inkster
 * Author URI: http://toby.ink/
 */

add_action( 'send_headers', function () {
	header( "X-Rabbit-1: */)/)" );
	header( "X-Rabbit-2: ( oxo)" );
	header( "X-Clacks-Overhead: GNU Terry Pratchett" );
} );
