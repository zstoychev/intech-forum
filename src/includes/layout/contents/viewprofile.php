<?php
$user = $requestData['profile'];
?>
<h2><a href="<?php echo $user->getLink();?>"><?php echo $translation['viewingProfile'] . ": " . $user->name;?></a></h2>
<div class="contentBody">
	<?php
	echo "<dl class=\"userprofile\">";
	echo "<dt><a href=\"" . $user->getLink() . "\">$user->name</a></dt>";
	echo "<dd>" . $translation[$userTypeToTranslation[$user->type]] . "</dd>";
	echo '<dd class="avatar"><img src="' . getGravatarURL($user->email) . '" alt="" /></dd>';
	if ($user->location != "") {
		echo '<dd>' . $translation['user.location'] . ": " . htmlspecialchars($user->location, ENT_NOQUOTES) . '</dd>';
	}
	echo '<dd>' . $translation['user.topicsStarted'] . ": " . $user->getNumberOfTopicsStarted() . "</dd>";
	echo '<dd>' . $translation['user.numberOfPosts'] . ": " . $user->getNumberOfPosts() . "</dd>";
	echo "</dl>";
	?>
</div>
