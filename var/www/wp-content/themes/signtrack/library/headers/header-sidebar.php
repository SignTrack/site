<?php

remove_all_actions( 'genesis_after_header' );
remove_all_actions( 'widely_header_right' );

add_action('genesis_before_header', 'widely_open_header_sidebar');

function widely_open_header_sidebar() {
	echo '<div class="widely-header-sidebar">';
}



add_action('genesis_after_header', 'widely_close_header_sidebar');

function widely_close_header_sidebar() {
	echo '</div>';
}


add_action('genesis_after_header', 'widely_open_header_sidebar_content');

function widely_open_header_sidebar_content() {
	echo '<div class="widely-header-sidebar-content">';
}


add_action('genesis_after_footer', 'widely_close_header_sidebar_content');

function widely_close_header_sidebar_content() {
	echo '</div>';
	echo '<div class="clear"></div>';
}


add_action( 'genesis_header_right', 'widely_vertical_navigation');
function widely_vertical_navigation() {

	echo '<div class="widely-vertical-nav">';
	genesis_do_nav();
	genesis_do_subnav();
	echo '</div>';

}
