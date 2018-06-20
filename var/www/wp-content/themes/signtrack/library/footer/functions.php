<?php

remove_all_actions('genesis_footer');

add_action( 'genesis_footer', 'widely_footer' );

function widely_footer() {
	?>

	<div id="footer" class="footer">
		<!-- <div class="wrap">
			<?php widely_render_footer_widget_areas(); ?>
		</div> -->

		<div class="widely-footer">
			<!-- <i class="fa fa-wordpress"></i> <i class="fa fa-rebel"></i> -->
			<p><a href="http://widelyinteractive.com" target="blank">Website and Mobile App Development</a> by Widely Interactive</p>
		</div>

	</div>

	<?php
}



function widely_render_footer_widget_areas() {
	global $widely_controller;

	echo '<div class="widely-footer-container">';
	for ($i = 1; $i <= $widely_controller->get_footer_widget_num(); $i++) {
		$widget = 'footer-' . $i;
		if (is_active_sidebar($widget)) {
			?>
			<div class="footer-widget footer-<?php echo $i;?>">
				<?php dynamic_sidebar($widget);?>
			</div>
			<?php
		}
	}
	echo '</div>';
}


function widely_register_footer_widgets() {
	global $widely_controller;

	if (function_exists('register_sidebar')) {
		for ($i = 1; $i <= $widely_controller->get_footer_widget_num(); $i++) {
			register_sidebar(array(
				'name'=> 'Footer ' . $i,
				'id' => 'footer-' . $i,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>',
			));
		}
	}
}


widely_register_footer_widgets();
