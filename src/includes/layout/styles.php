<?php
	echo '<link rel="stylesheet" type="text/css" href="styles/' . $stylesToFile[$styles[$requestData['defaultStyle']]] . '" title="' . $styles[$requestData['defaultStyle']] . '" />';
	foreach($styles as $index => $style) {
		if ($index != $requestData['defaultStyle']) {
			echo '<link rel="alternate stylesheet" type="text/css" href="styles/' . $stylesToFile[$style] . '" title="' . $style . '" />';
		}
	} 
?>