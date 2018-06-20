<h2>Grid Styles</h2>


<?php

/**
* One Halves
*/

ob_start();

$title = 'One Half Divs <h6 class="headline-alt">(.grid-size-always and strict version available for all grid items)</h6>';
?>

<div class="grid-1-2 grid-size-always">
	<div class="debug-border">One Half Always Grid Item</div>
</div>
<div class="grid-1-2 grid-size-always">
	<div class="debug-border">One Half Always Grid Item</div>
</div>

<div class="grid-1-2-strict grid-size-always">
	<div class="debug-border">One Half Always Grid Item</div>
</div>
<div class="grid-1-2-strict grid-size-always">
	<div class="debug-border">One Half Always Grid Item</div>
</div>

<div class="clear"></div>

<div class="grid-1-2">
	<div class="debug-border">One Half Grid Item</div>
</div>
<div class="grid-1-2">
	<div class="debug-border">One Half Grid Item</div>
</div>
<div class="clear"></div>


<div class="grid-1-2-strict">
	<div class="debug-border">One Half Grid Item</div>
</div>
<div class="grid-1-2-strict">
	<div class="debug-border">One Half Grid Item</div>
</div>
<div class="clear"></div>


<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);


/**
* One Fourths
*/

ob_start();

$title = 'One Thirds Divs';
?>

<div class="grid-1-3rd">
	<div class="debug-border">One Third Grid Item</div>
</div>
<div class="grid-1-3rd">
	<div class="debug-border">One Third Grid Item</div>
</div>
<div class="grid-1-3rd">
	<div class="debug-border">One Third Grid Item</div>
</div>

<div class="clear"></div>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);




/**
* One Fourths
*/

ob_start();

$title = 'One Fourths Divs';
?>


<div class="grid-1-4th">
	<div class="debug-border">One Fourth Grid Item</div>
</div>
<div class="grid-1-4th">
	<div class="debug-border">One Fourth Grid Item</div>
</div>
<div class="grid-1-4th">
	<div class="debug-border">One Fourth Grid Item</div>
</div>
<div class="grid-1-4th">
	<div class="debug-border">One Fourth Grid Item</div>
</div>

<div class="clear"></div>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);






/**
* One Fifth
*/

ob_start();

$title = 'One Fifths Divs';
?>

<div class="grid-1-5th">
	<div class="debug-border">One Fifth Grid Item</div>
</div>
<div class="grid-1-5th">
	<div class="debug-border">One Fifth Grid Item</div>
</div>
<div class="grid-1-5th">
	<div class="debug-border">One Fifth Grid Item</div>
</div>
<div class="grid-1-5th">
	<div class="debug-border">One Fifth Grid Item</div>
</div>
<div class="grid-1-5th">
	<div class="debug-border">One Fifth Grid Item</div>
</div>

<div class="clear"></div>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);


/**
* One Sixth
*/

ob_start();

$title = 'One Sixth Divs';
?>

<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>
<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>
<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>
<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>
<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>
<div class="grid-1-6th">
	<div class="debug-border">One Sixth Grid Item</div>
</div>

<div class="clear"></div>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);
