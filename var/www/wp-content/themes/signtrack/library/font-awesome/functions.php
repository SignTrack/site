<?php

/**
 * Enqueue Font Awesome!
 */

add_action( 'wp_enqueue_scripts', 'register_widely_font_awesome' );

function register_widely_font_awesome() {
	wp_register_style( 'widely-font-awesome', trailingslashit(get_stylesheet_directory_uri()) . 'library/font-awesome/css/font-awesome.min.css' );
	wp_enqueue_style( 'widely-font-awesome' );
}
