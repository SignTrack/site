<?php
if ($widely_controller->get_header_home_hero()) {
	$widely_controller->set_header_banner(true);
	add_action( 'genesis_before_content_sidebar_wrap', 'widely_frontpage_hero_script' );
	add_action( 'genesis_before', 'widely_frontpage_hero', 1 );
}

function widely_frontpage_hero_script() {
	global $widely_controller;

	if (is_null( $widely_controller->get_home_hero_url() )) {
		return;
	}
	ob_start();
	if ($widely_controller->get_header_home_hero() == 'parallax') {
		?>
		<script charset="utf-8">
		jQuery(function() {
			widelyAddHomepageHeroParallax(<?php echo "'" . $widely_controller->get_home_hero_url() . "'"; ?>);
		});
		</script>
		<?php
	}
	else {
		?>
		<script charset="utf-8">
		widelyAddHomepageHero(<?php echo "'" . $widely_controller->get_home_hero_url() . "'"; ?>);
		</script>
		<?php
	}
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
}

function widely_frontpage_hero() {
	global $widely_controller;

	if (is_null( $widely_controller->get_home_hero_url() )) {
		return;
	}
	ob_start();
	if ($widely_controller->get_header_home_hero() == 'parallax') {
		?>
		<div class="homepage-parallax-header" style="display: none;">
			<img src="<?php echo $widely_controller->get_home_hero_url(); ?>">
		</div>
		<?php
	}
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
}




if ($widely_controller->get_header_banner()) {

	require_once('mobile-header.php');
	require_once('header-banner.php');

	if ($widely_controller->get_header_banner_nav()) {
		widely_header_banner_nav();
	}
}

else {

	require_once('header-sidebar.php');
	add_filter('body_class','widely_sidebar_header_body_class');
}


function widely_sidebar_header_body_class($classes = '') {
	$classes[] = 'sidebar-header';
	return $classes;
}




if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'images/logo.png' ) ) {
	remove_all_actions('genesis_site_title');
	add_action( 'genesis_site_title', 'widely_use_logo_header');
}


function widely_use_logo_header() {
	global $widely_controller;

	$img_dir = trailingslashit( get_stylesheet_directory_uri() ) . 'images/logo.png';
	$home_url = trailingslashit( home_url() );

	if ($widely_controller->get_font_logo()) {
		echo '<a href="' . $home_url . '"><span class="font-logo"><i class="fa fa-cubes"></i></span></a>';
	}
	else {
		echo '<a href="' . $home_url . '"><img src="' . $img_dir . '" class="default-logo" alt="Logo"></a>';
	}


}

if (!$widely_controller->get_show_desc()) {
	remove_all_actions( 'genesis_site_description' );
}
