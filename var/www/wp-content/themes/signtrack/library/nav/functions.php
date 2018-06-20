<?php

if ($widely_controller->get_fixed_nav()) {

	add_filter('body_class','widely_fixed_nav_body_class');
}

function widely_fixed_nav_body_class($classes = '') {
	$classes[] = 'fixed-nav';
	return $classes;
}



if ($widely_controller->get_mobile_nav()) {

	require_once('mobile-nav.php');

}
