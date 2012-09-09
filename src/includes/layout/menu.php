<ul id="menu">
	<li><a href="index.php"><?php echo $translation['index'];?></a></li>
	<li><a href="aboutus.php"><?php echo $translation['aboutus'];?></a></li>
	<?php
	if (!isset($requestData['user'])) {
	?>
		<li><a href="register.php"><?php echo $translation['register'];?></a></li>
		<li><a href="login.php"><?php echo $translation['login'];?></a></li>
	<?php
	} else { 
	?>
		<li><a href="<?php echo $requestData['user']->getLink();?>"><?php echo $translation['profile'];?></a></li>
		<li><a href="editprofile.php"><?php echo $translation['editProfile'];?></a></li>
		<?php
		if ($requestData['user']->type == User::ADMIN_TYPE) {
			echo '<li><a href="administrate.php">' . $translation['administrate'] . '</a></li>';
		}
		?>
		<li><a href="logout.php"><?php echo $translation['logout'];?></a></li>
	<?php
		} 
	?>
</ul>
