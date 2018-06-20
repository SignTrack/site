<h2>Common Elements</h2>


<?php

/**
* Icon Item
*/

ob_start();

$title = 'Icon Item';
?>

<div class="icon-container">
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(50, 700);?>x<?php echo rand(50, 700);?>" alt="Placehold Image" />
	</div>
	<div class="icon-text">
		<p>Text to be centered below icon.</p>
	</div>
</div>
<div class="clear"></div>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);





/**
* Icon group
*/

ob_start();

$title = 'Icon Group';
?>

<div class="icon-group four-icons">
	<div class="icon-container">
		<div class="icon-img">
			<img src="http://placehold.it/<?php echo rand(50, 700);?>x<?php echo rand(50, 700);?>" alt="Placehold Image" />
		</div>
		<div class="icon-text">
			<p>Text to be centered below icon.</p>
		</div>
	</div><div class="icon-container">
		<div class="icon-img">
			<img src="http://placehold.it/<?php echo rand(50, 700);?>x<?php echo rand(50, 700);?>" alt="Placehold Image" />
		</div>
		<div class="icon-text">
			<p>Text to be centered below icon.</p>
		</div>
	</div><div class="icon-container">
		<div class="icon-img">
			<img src="http://placehold.it/<?php echo rand(50, 700);?>x<?php echo rand(50, 700);?>" alt="Placehold Image" />
		</div>
		<div class="icon-text">
			<p>Text to be centered below icon.</p>
		</div>
	</div><div class="icon-container">
		<div class="icon-img">
			<img src="http://placehold.it/<?php echo rand(50, 700);?>x<?php echo rand(50, 700);?>" alt="Placehold Image" />
		</div>
		<div class="icon-text">
			<p>Text to be centered below icon.</p>
		</div>
	</div>
</div>

<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);



/**
* Icon Only Group
*/

ob_start();

$title = 'Icon Only Group';
?>

<div class="icon-only-group">
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>" alt="Placehold Image" />
	</div>
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>" alt="Placehold Image" />
	</div>
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>" alt="Placehold Image" />
	</div>
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>" alt="Placehold Image" />
	</div>
	<div class="icon-img">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>" alt="Placehold Image" />
	</div>
</div>

<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);








ob_start();

$title = 'Flags';
?>

<div class="flag">
	<div class="flag-stars">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=Flag+Stars" alt="Placehold Image" />
	</div>

	<div class="flag-stripes">
		<h3>Flag Stripes</h3>
		<p>This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. </p>
	</div>

</div>

<div class="flag flag-reverse">
	<div class="flag-stars">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=Flag+Stars" alt="Placehold Image" />
	</div>

	<div class="flag-stripes">
		<h3>Flag Stripes</h3>
		<p>This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. </p>
	</div>

</div>


<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);















ob_start();

$title = 'Lightbox';
?>

<div class="widebox">
	<div class="widebox-trigger">
		<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
	</div>

	<div class="widebox-action">
		<h3>Widebox Action</h3>
		<img src="http://placehold.it/<?php echo rand(1000, 3000);?>x<?php echo rand(1000, 3000);?>&text=widebox-action" alt="Placehold Image" />
		<p>This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. This is a stripe on a flag. </p>
	</div>

</div>


<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);














ob_start();

$title = 'Fade In';
?>

<div class="widely-fade fade-in">
	<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
</div>

<div class="widely-fade fade-in-up">
	<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
</div>

<div class="widely-fade fade-in-down">
	<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
</div>

<div class="widely-fade fade-in-left">
	<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
</div>

<div class="widely-fade fade-in-right">
	<img src="http://placehold.it/<?php echo rand(100, 300);?>x<?php echo rand(100, 300);?>&text=widebox-trigger" alt="Placehold Image" />
</div>

<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);
