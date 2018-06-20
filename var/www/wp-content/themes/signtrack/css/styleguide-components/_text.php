<h2>Text Styles</h2>


<?php

/**
* Headlines
*/

ob_start();

$title = 'Headlines';
?>

<h1>h1 Headline</h1>
<h2>h2 Headline</h2>
<h3>h3 Headline</h3>
<h4>h4 Headline</h4>
<h5>h5 Headline</h5>
<h6>h6 Headline</h6>

<h1 class="headline-alt">h1 Headline</h1>
<h2 class="headline-alt">h2 Headline</h2>
<h3 class="headline-alt">h3 Headline</h3>
<h4 class="headline-alt">h4 Headline</h4>
<h5 class="headline-alt">h5 Headline</h5>
<h6 class="headline-alt">h6 Headline</h6>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);




/**
* Blockquotes, qoutes, and Cites
*/

ob_start();

$title = 'Blockquote/Cite Format';
?>

<blockquote cite="Widely Interactive">
	This is blockquote by Widely Interactive. This is blockquote by Widely Interactive. This is blockquote by Widely Interactive. This is blockquote by Widely Interactive. This is blockquote by Widely Interactive. This is blockquote by Widely Interactive. This is blockquote by Widely Interactive.
	<cite>Widely Interactive</cite>
</blockquote>

<p>Styleguide by: <cite>Widely Interactive</cite></p>


<p><q cite="Widely Interactive">This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. This is a standard quote using a q tag. </q></p>

<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);


/**
* Text Tags
*/

ob_start();

$title = 'Various Text Tags';
?>

<p><strong>Strong Tags</strong> Usually used for bolding</p>

<p><em>em Tags</em> Usually used for emphasis</p>

<p><b>b Tags</b> Alternative to Strong for bolding</p>

<p>Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. Standard p tags. </p>

<p><del>del tags</del> Default strikethrough</p>

<p>This is an example containing <sup>sup</sup> tags.</p>

<p>This is an example containing <sub>sub</sub> tags.</p>

<p><i>i tags</i> Used to represent and alternative voice.</p>

<p><abbr title="Abbreviation Tags">abbr tags</abbr> Default Abbreviation tags</p>

<p><ins>ins tags</ins> for text that is inserted post production</p>

<p><kbd>kbd tags</kbd> for keybord inputs</p>

<p><mark>mark tags</mark> for specially marked content</p>

<p><var>var tags</var> are very rarily used, but they exist.</p>

<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);



/**
* Lists
*/

ob_start();

$title = 'List Types';
?>

<h6>Ordered Lists</h6>

<ol>
	<li>List Item</li>
	<li>List Item</li>
	<li>List Item</li>
	<li>List Item</li>
</ol>


<h6>Unordered Lists</h6>

<ul>
	<li>List Item</li>
	<li>List Item</li>
	<li>List Item</li>
	<li>List Item</li>
</ul>
<?php
$content = ob_get_contents();
ob_end_clean();

wi_render_styleguide_item($title, $content);
