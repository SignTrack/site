<?php


function widely_flag( $atts ) {
	$atts = shortcode_atts( array(
		'stars' => '',
		'stripes' => '',
		'reverse' => FALSE
	), $atts, 'widely_flag' );


	ob_start();

	if ($atts['reverse']) {
		?>
		<div class="flag flag-reverse">
			<div class="flag-stars">
				<?php echo $atts['stars']; ?>
			</div>
			<div class="flag-stripes">
				<?php echo $atts['stripes']; ?>
			</div>
		</div>

		<?php
	}
	else {
		?>
		<div class="flag">
			<div class="flag-stars">
				<?php echo $atts['stars']; ?>
			</div>
			<div class="flag-stripes">
				<?php echo $atts['stripes']; ?>
			</div>
		</div>

		<?php
	}



	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'widely_flag', 'widely_flag' );
