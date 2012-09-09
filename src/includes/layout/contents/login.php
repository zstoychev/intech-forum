<h2><a href="login.php"><?php echo $translation['login'];?></a></h2>
<div class="contentBody">
	<?php
	include 'fiemessages.php'; 
	?>
	<form id="loginForm" action="login.php?mode=login" method="post">
		<fieldset>
			<legend><?php echo $translation['login'];?></legend>
			<label for="username"><?php echo $translation['forms.username'];?>: </label><input type="text" name="username" id="username" />
			<label for="password"><?php echo $translation['forms.password'];?>: </label><input type="password" name="password" id="password" />
			<input type="submit" value="<?php echo $translation['forms.actions.login'];?>" />
		</fieldset>	
	</form>
</div>
