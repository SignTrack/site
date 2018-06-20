<?php

function widely_hero_image( $atts ) {
	$params = shortcode_atts( array(
		'url' => FALSE,
		'parallax' => FALSE,
	), $atts );

	extract($params);


	if (!$url) {
		return;
	}

	ob_start();

	if ($parallax && $parallax != 'false') {
		?>
		<script charset="utf-8">
		widelyAddHeroParallax(<?php echo "'" . $url . "'"; ?>);
		</script>
		<?php
	}
	else {
		?>
		<script charset="utf-8">
		widelyAddHero(<?php echo "'" . $url . "'"; ?>);
		</script>
		<?php
	}

	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}



add_shortcode( 'hero_image', 'widely_hero_image' );
