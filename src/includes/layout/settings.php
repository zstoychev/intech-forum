<div id="settings">
	<h2><?php echo $translation['settings'];?></h2>
	<h3><?php echo $translation['language'];?></h3>
	<ul>
	<?php
	foreach ($languages as $languageid => $language) {
		echo '<li><a href="changelanguage.php?language=' . $languageid . '">' . $translation['language.' . $language] . "</a></li>\n";
	}
	?>
	</ul>
	<h3><?php echo $translation['style'];?></h3>
	<ul id="styleChooser">
	<?php
	foreach ($styles as $styleid => $style) {
		echo '<li><a href="changestyle.php?style=' . $styleid . '">' . $style . "</a></li>\n";
	}
	?>
	</ul>
</div>
