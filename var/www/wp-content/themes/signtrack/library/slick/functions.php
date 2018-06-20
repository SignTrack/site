<?php

/**
 * Thank you Ken Wheeler for creating Slick Slider!
 *
 * Twitter: @ken_wheeler
 */


/**
* Enqueue Slick
*/

add_action( 'wp_enqueue_scripts', 'register_widely_slick_styles' );

function register_widely_slick_styles() {
	wp_register_style( 'widely-slick-styles', trailingslashit(get_stylesheet_directory_uri()) . 'library/slick/slick.css' );
	wp_enqueue_style( 'widely-slick-styles' );

	wp_register_style( 'widely-slick-styles-theme', trailingslashit(get_stylesheet_directory_uri()) . 'library/slick/slick-theme.css' );
	wp_enqueue_style( 'widely-slick-styles-theme' );
}



add_action( 'wp_enqueue_scripts', 'register_widely_slick_scripts' );

function register_widely_slick_scripts() {
	wp_register_style( 'widely-slick-scripts', trailingslashit(get_stylesheet_directory_uri()) . 'library/slick/slick.min.js' );
	wp_enqueue_style( 'widely-slick-scripts' );

}
