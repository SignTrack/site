<?php

function widely_grid_shortcode( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'first' => FALSE,
		'second' => FALSE,
	), $atts, 'halves' );

	$params = shortcode_atts( array(
		'first' => FALSE,
		'second' => FALSE,
	), $atts, 'half' );
	extract($params);
	ob_start();

	?>

	<div class="grid-1-2">
		<?php echo $params['first']; ?>
	</div>
	<div class="grid-1-2">
		<?php echo $params['second']; ?>
	</div>
	<div class="clear"></div>

	<?php

	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function widely_grid_halves( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'first' => FALSE,
		'second' => FALSE,
	), $atts);

	extract($params);
	ob_start();

	?>

	<div class="grid-1-2">
		<?php echo $params['first']; ?>
	</div>
	<div class="grid-1-2">
		<?php echo $params['second']; ?>
	</div>
	<div class="clear"></div>

	<?php

	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

add_shortcode( 'halves', 'widely_grid_halves' );



function widely_grid_half( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'classes' => '',
	), $atts );
	extract($params);
	ob_start();
	?>

	<div class="grid-1-2 <?php echo $classes; ?>">
		<?php echo $content; ?>
	</div>

	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
add_shortcode( 'half', 'widely_grid_half' );


function widely_grid_third( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'classes' => '',
	), $atts );
	extract($params);
	ob_start();
	?>

	<div class="grid-1-3rd <?php echo $classes; ?>">
		<?php echo $content; ?>
	</div>

	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
add_shortcode( 'third', 'widely_grid_third' );

function widely_grid_fourth( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'classes' => '',
	), $atts );
	extract($params);
	ob_start();
	?>

	<div class="grid-1-4th <?php echo $classes; ?>">
		<?php echo $content; ?>
	</div>

	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
add_shortcode( 'fourth', 'widely_grid_fourth' );


function widely_grid_sixth( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'classes' => '',
	), $atts );
	extract($params);
	ob_start();
	?>

	<div class="grid-1-6th <?php echo $classes; ?>">
		<?php echo apply_filters('the_content', $content); ?>
	</div>

	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
add_shortcode( 'sixth', 'widely_grid_sixth' );



function widely_grid_clear( $atts, $content = NULL ) {
	$params = shortcode_atts( array(
		'fat' => FALSE
	), $atts );
	extract($params);
	ob_start();
	$class = 'clear';
	if ($fat) {
		$class = 'fat-clear';
	}
	?>

	<div class="<?php echo $class; ?>"></div>

	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
add_shortcode( 'clear', 'widely_grid_clear' );
