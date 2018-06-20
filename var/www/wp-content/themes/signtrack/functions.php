<?php
/**
* dump function for debug
*/
if (!function_exists('dump')) {
	function dump ($var, $label = 'Dump', $echo = TRUE) {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		$output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left; width: 100% !important; font-size: 12px !important;">' . $label . ' => ' . $output . '</pre>';
		if ($echo == TRUE) {
			echo $output;
		}
		else {
			return $output;
		}
	}
}
if (!function_exists('dump_exit')) {
	function dump_exit($var, $label = 'Dump', $echo = TRUE) {
		dump ($var, $label, $echo);exit;
	}
}


remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

add_action( 'genesis_meta', 'add_viewport_meta_tag' );
function add_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
}

global $email_user_pass;
$email_user_pass = "";

// Apprently you have to start Genesis even though this
// is a Genesis child theme
require_once(get_template_directory() . '/lib/init.php');


// Load our Controller
require_once(CHILD_DIR . '/includes/controller.php');
require_once(CHILD_DIR . '/includes/functions.php');


// Load our library
require_once(CHILD_DIR . '/library/functions.php');


// Register style sheet.
add_action( 'wp_enqueue_scripts', 'register_widely_styles' );

/**
* Register style sheet.
*/
function register_widely_styles() {
	wp_register_style( 'widely', trailingslashit(get_stylesheet_directory_uri()) . 'css/style.css' );
	wp_enqueue_style( 'widely' );
	wp_register_style( 'widely-override', trailingslashit(get_stylesheet_directory_uri()) . 'css/css-override.css' );
	wp_enqueue_style( 'widely-override' );
}




// Always use wp_enqueue_scripts action hook to both enqueue and register scripts
add_action( 'wp_enqueue_scripts', 'register_widely_scripts' );

function register_widely_scripts(){

	wp_register_script( 'jquery', trailingslashit(get_stylesheet_directory_uri()) . '/js/jquery.min.js' );
	wp_enqueue_script( 'jquery' );

	wp_register_script( 'widely-script', trailingslashit(get_stylesheet_directory_uri()) . '/js/scripts.min.js' );

	wp_enqueue_script( 'widely-script' );
}







/**
 * Just incase
 */
add_action( 'genesis_after_content_sidebar_wrap', 'widely_clear', 15);

function widely_clear() {
	echo '<div class="clear"></div>';
}


/**
 * Add some Body Classes
 */

add_filter('body_class','browser_body_class');

function browser_body_class($classes = '') {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}
