<h2><?php echo $translation['forumMessage'];?></h2>
<div class="contentBody">
	<?php
		echo '<p class="message">' . $translation[$messageToTranslation[$requestData['message']]] . '</p>';
		if (isset($requestData['message_links'])) {
			foreach ($requestData['message_links'] as $linkType => $link) {
				echo '<p><a href="' . $link . '">' . $translation[$messageToTranslation[$linkType]] . '</a>';
			}
		}
	?>
	<p><a href="index.php"><?php echo $translation['returnToIndex'];?></a></p>
</div>
