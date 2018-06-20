<?php


add_action( 'genesis_before_header', 'widely_mobile_header');
function widely_mobile_header() {

	echo '<div class="widely-mobile-header">';

	widely_mobile_header_logo();

	// genesis_do_nav();
	// genesis_do_subnav();
	echo '</div>';

}


function widely_mobile_header_logo() {
	?>
	<div class="grid-1-4th grid-size-always">
		<div class="mobile-header-nav-button-container">
			<a href="#" class="mobile-header-nav-button"><i class="fa fa-th-list"></i></a>
		</div>
	</div>

	<div class="grid-1-2 grid-size-always">
		<div class="mobile-header-logo-container">
			<?php
			do_action('genesis_site_title');
			?>
		</div>
	</div>

	<div class="grid-1-4th grid-size-always">
		<div class="mobile-header-contact-button-container">
			<a href="#" class="mobile-header-contact-button"><i class="fa fa-whatsapp"></i></a>
		</div>
	</div>

	<div class="clear"></div>
	<?php
}
