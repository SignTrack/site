<?php

function wi_render_styleguide_item($title, $html) {
	?>
	<div class="styleguide-item">
		<div class="styleguide-item-header">
			<h3><?php echo $title; ?></h3>
		</div>
		<div class="styleguide-item-content">
			<div class="styleguide-item-example">
				<?php echo $html; ?>
			</div>

			<div class="styleguide-item-code">
				<pre>
					<code>
						<?php

						if (function_exists('esc_html')) {
							echo esc_html($html);
						}
						else {
							$output = str_replace('<', '&#60;', $html);
							$output = str_replace('>', '&#62;', $output);
							echo $output;
						}
						?>
					</code>
				</pre>
			</div>
		</div>
	</div>
	<?php
}


require_once(dirname(__FILE__) . '/styleguide-components/_grid.php');
require_once(dirname(__FILE__) . '/styleguide-components/_text.php');
require_once(dirname(__FILE__) . '/styleguide-components/_common-elems.php');
