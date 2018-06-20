<?php

require_once('font-awesome/functions.php');
require_once('slick/functions.php');
require_once('headers/functions.php');
require_once('nav/functions.php');
require_once('footer/functions.php');
require_once('shortcodes/functions.php');



if ($widely_controller->get_responsive_debug()) {
	add_filter('body_class','widely_responsive_body_class');
}
function widely_responsive_body_class($classes = '') {
	$classes[] = 'responsive-debug';
	return $classes;
}



add_action( 'genesis_before', 'include_widely_container', 1 );
function include_widely_container() {
	echo '<div class="widely-container">';
}

add_action( 'genesis_after', 'include_widely_close_container' );
function include_widely_close_container() {
	echo '</div>';
}


add_action( 'genesis_before_content_sidebar_wrap', 'include_widely_content_container' );
function include_widely_content_container() {
	echo '<div class="widely-inner-container">';
}

add_action( 'genesis_after_content_sidebar_wrap', 'include_widely_close_container', 20 );
