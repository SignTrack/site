<?php


function widely_header_banner_nav() {
	remove_all_actions( 'genesis_after_header' );
	remove_all_actions( 'genesis_header_right' );
	add_action( 'genesis_header_right', 'widely_header_right' );

	add_action( 'genesis_header_right', 'genesis_do_nav' );
	add_action( 'genesis_header_right', 'genesis_do_subnav' );

	add_action( 'genesis_header_right', 'widely_header_right_close' );
}

function widely_header_right() {
	if ( isset( $wp_registered_sidebars['header-right'] ) && is_active_sidebar( 'header-right' ) ) {
		echo '<div class="header-right-nav header-right-nav-contained">';
	}
	else {
		echo '<div class="header-right-nav">';
	}

}


function widely_header_right_close() {
	echo '</div>';
}
