<?php
if (!isset($requestData['user'])) {
?>
	<form id="loginForm" class="fastLoginInf" action="login.php?mode=login" method="post">
		<fieldset>
			<legend><?php echo $translation['login'];?></legend>
			<label for="fastLoginUsername"><?php echo $translation['forms.username'];?>: </label><input type="text" name="username" id="fastLoginUsername" />
			<label for="fastLoginPassword"><?php echo $translation['forms.password'];?>: </label><input type="password" name="password" id="fastLoginPassword" />
			<input type="submit" value="<?php echo $translation['forms.actions.login'];?>" />
		</fieldset>	
	</form>
<?php
} else {
?>
<p class="fastLoginInf"><?php echo $translation['loggedInAs'] . ': ' . $requestData['user']->name . ' (<a href="logout.php">' . $translation['logout'] . '</a>)';?></p>
<?php
} 
?>