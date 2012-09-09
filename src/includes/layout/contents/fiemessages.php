<?php
foreach ($requestData['forum_input_errors'] as $inputError) {
	echo '<p class="fie">' . $translation[$messageToTranslation[$inputError]] . '</p>'; 
}
?>
