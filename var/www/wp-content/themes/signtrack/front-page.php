<?php


remove_all_actions( 'genesis_site_title' );
remove_all_actions( 'genesis_header_right' );
remove_all_actions( 'genesis_site_description' );
remove_all_actions( 'genesis_post_title' );
remove_all_actions( 'genesis_footer' );


add_action( 'genesis_site_title', 'signtrack_homepage_header_left');
add_action( 'genesis_header_right', 'signtrack_homepage_header_right');

//add_action( 'genesis_post_content', 'signtrack_add_section', 1);
add_action( 'genesis_post_content', 'signtrack_close_section', 30);

add_action( 'genesis_footer', 'signtrack_footer' );

genesis();
