<?php

add_action( 'genesis_before', 'widely_render_mobile_nav', 10);

function widely_render_mobile_nav() {

	?>
	<div class="mobile-nav-container">
		<?php //genesis_do_nav(); genesis_do_subnav();
			echo genesis_widely_get_mobile_nav_menu();
		 ?>
	</div>

	<div class="mobile-contact-container">
		<a href="tel:1234567890" class="button-secondary">123.456.7890</a>
		<a href="mailto:blah@blah.com" class="button-secondary">Email</a>
	</div>

	<?php

}




function genesis_widely_get_mobile_nav_menu( $args = array() ) {

	$class = 'menu genesis-nav-menu menu-primary';
	if ( genesis_superfish_enabled() ) {
		$class .= ' js-superfish';
	}

	$args = wp_parse_args( $args, array(
		'theme_location' => 'primary',
		'container'      => $class,
		'menu_class'     => 'menu genesis-nav-menu',
		'echo'           => 0,
	) );

	//* If a menu is not assigned to theme location, abort
	if ( ! has_nav_menu( $args['theme_location'] ) ) {
		return;
	}

	$sanitized_location = sanitize_key( $args['theme_location'] );

	$nav = wp_nav_menu( $args );

	//* Do nothing if there is nothing to show
	if ( ! $nav ) {
		return;
	}

	$xhtml_id = $args['theme_location'];

	if ( 'primary' === $args['theme_location'] ) {
		$xhtml_id = 'mobile-nav';
	} elseif ( 'secondary' === $args['theme_location'] ) {
		$xhtml_id = 'subnav';
	}

	$nav_markup_open = genesis_markup( array(
		'html5'   => '<nav %s>',
		'xhtml'   => '<div id="' . $xhtml_id . '">',
		'context' => 'nav-' . $sanitized_location,
		'echo'    => false,
	) );
	$nav_markup_open .= genesis_structural_wrap( 'menu-' . $sanitized_location, 'open', 0 );

	$nav_markup_close  = genesis_structural_wrap( 'menu-' . $sanitized_location, 'close', 0 );
	$nav_markup_close .= genesis_html5() ? '</nav>' : '</div>';

	$nav_output = $nav_markup_open . $nav . $nav_markup_close;

	$filter_location = 'genesis_' . $sanitized_location . '_nav';

	//* Handle back-compat for primary and secondary nav filters.
	if ( 'primary' === $args['theme_location'] ) {
		$filter_location = 'genesis_do_nav';
	} elseif ( 'secondary' === $args['theme_location'] ) {
		$filter_location = 'genesis_do_subnav';
	}

	/**
	* Filter the navigation markup.
	*
	* @since 2.1.0
	*
	* @param string $nav_output Opening container markup, nav, closing container markup.
	* @param string $nav Navigation list (`<ul>`).
	* @param array $args {
	*     Arguments for `wp_nav_menu()`.
	*
	*     @type string $theme_location Menu location ID.
	*     @type string $container Container markup.
	*     @type string $menu_class Class(es) applied to the `<ul>`.
	*     @type bool $echo 0 to indicate `wp_nav_menu()` should return not echo.
	* }
	*/
	return apply_filters( $filter_location, $nav_output, $nav, $args );
}
