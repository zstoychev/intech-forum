<h2><a href="register.php"><?php echo $translation['registration'];?></a></h2>
<div class="contentBody">
	<?php
	include 'fiemessages.php'; 
	
	$fields = $requestData['fields'];
	?>
	<form id="registrationForm" action="register.php?mode=register" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.personalInfo'];?></legend>
			<dl>
				<dt><label for="username"><?php echo $translation['forms.username'];?>:</label></dt>
				<dd><input type="text" name="username" id="username" value="<?php echo htmlspecialchars($fields['username'], ENT_NOQUOTES);?>" /></dd>
				<dt><label for="password"><?php echo $translation['forms.password'];?>:</label></dt>
				<dd><input type="password" name="password" id="password" value="<?php echo htmlspecialchars($fields['password'], ENT_NOQUOTES);?>" /></dd>
				<dt><label for="passwordComfirm"><?php echo $translation['forms.passwordComfirm'];?>:</label></dt>
				<dd><input type="password" name="passwordComfirm" id="passwordComfirm" value="<?php echo htmlspecialchars($fields['passwordComfirm'], ENT_NOQUOTES);?>" /></dd>
				<dt><label for="email"><?php echo $translation['forms.email'];?>: </label></dt>
				<dd><input type="text" name="email" id="email" value="<?php echo htmlspecialchars($fields['email'], ENT_NOQUOTES);?>" /></dd>
			</dl>
		</fieldset>
		
		<fieldset>
			<legend><?php echo $translation['forms.forumConfiguration'];?></legend>
			<dl>
				<dt><label for="language"><?php echo $translation['forms.language'];?>:</label></dt>
				<dd>
					<select name="language" id="language">
						<?php
							$selectedLanguage = (int) $fields['language'];
							for ($i = 0; $i < NUMBER_OF_LANGUAGES; $i++) {
								echo '<option value="' . $i . '"' . (($i == $selectedLanguage) ? ' selected="selected"' : '') . '>' . $translation['language.' . $languages[$i]] . '</option>';
							} 
						?>
					</select>
				</dd>
				<dt><label for="style"><?php echo $translation['forms.style'];?>:</label></dt>
				<dd>
					<select name="style" id="style">
						<?php
							$selectedStyle = (int) $fields['style'];
							for ($i = 0; $i < NUMBER_OF_STYLES; $i++) {
								echo '<option value="' . $i . '"' . (($i == $selectedStyle) ? ' selected="selected"' : '') . '>' . $styles[$i] . '</option>';
							} 
						?>
					</select>
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend><?php echo $translation['forms.antispam'];?></legend>
			<dl>
				<dt><label for="antiBot"><?php echo $translation['forms.enterAntispam'];?>:</label></dt>
				<dd><?php echo '<img src="antibotimg.php?str=' . $requestData['registration_bot_check'] . '" alt="" />'?></dd>
				<dd><input type="text" name="antiBot" id="antiBot" value="<?php echo htmlspecialchars($fields['antiBot'], ENT_NOQUOTES);?>" /></dd>
			</dl>
		</fieldset>
		<p><input type="submit" value="<?php echo $translation['register.register'];?>" /> <input type="reset" value="<?php echo $translation['form.reset'];?>" /></p>
	</form>
</div>
