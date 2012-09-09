<h2><a href="administrate.php"><?php echo $translation['administrate'];?></a></h2>
<div class="contentBody">
	<?php
	include 'fiemessages.php'; 
	
	$fields = $requestData['fields'];
	$forums = $requestData['forums'];
	?>
	<form id="createForumForm" action="administrate.php?mode=createforum" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.createForumForm'];?></legend>
			<p>
				<label for="createForumName"><?php echo $translation['forms.forumName'];?>:</label><br />
				<input type="text" name="name" id="createForumName" /><br />
				<label for="createForumDescription"><?php echo $translation['forms.forumDescription'];?>:</label><br />
				<textarea name="description" id="createForumDescription" cols="40" rows="5"></textarea><br />
				<input type="submit" value="<?php echo $translation['forms.action.createForum'];?>" />
			</p>
		</fieldset>
	</form>
	<?php
	if (!empty($forums)) { 
	?>
	<form id="deleteForumForm" action="administrate.php?mode=deleteforum" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.deleteForumForm'];?></legend>
			<p>
				<label for="deleteForumID"><?php echo $translation['forms.forum'];?>:</label><br />
				<?php
					echo '<select name="forumid" id="deleteForumID">';
					foreach ($forums as $forum) {
							echo '<option value="' . $forum->id . '">' . $forum->name . '</option>'; 
					}
					echo '</select><br />'
				?>
				<input type="submit" value="<?php echo $translation['forms.action.deleteForum'];?>" />
			</p>
		</fieldset>
	</form>
	<form id="renameForumForm" action="administrate.php?mode=renameforum" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.renameForumForm'];?></legend>
			<p>
				<label for="renameForumID"><?php echo $translation['forms.forum'];?>:</label><br />
				<?php
					echo '<select name="forumid" id="renameForumID">';
					foreach ($forums as $forum) {
							echo '<option value="' . $forum->id . '">' . $forum->name . '</option>'; 
					}
					echo '</select><br />'
				?>
				<label for="renameForumName"><?php echo $translation['forms.forumName'];?>:</label><br />
				<input type="text" name="name" id="renameForumName" /><br />
				<input type="submit" value="<?php echo $translation['forms.action.renameForum'];?>" />
			</p>
		</fieldset>
	</form>
	<form id="updateForumDescriptionForm" action="administrate.php?mode=updatedescriptionforum" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.updateForumDescriptionForm'];?></legend>
			<p>
				<label for="updateForumDescriptionID"><?php echo $translation['forms.forum'];?>:</label><br />
				<?php
					echo '<select name="forumid" id="updateForumDescriptionID">';
					foreach ($forums as $forum) {
							echo '<option value="' . $forum->id . '">' . $forum->name . '</option>'; 
					}
					echo '</select><br />'
				?>
				<label for="updateForumDescriptionDescription"><?php echo $translation['forms.forumDescription'];?>:</label><br />
				<textarea name="description" id="updateForumDescriptionDescription" cols="40" rows="5"></textarea><br />
				<input type="submit" value="<?php echo $translation['forms.action.updateForumDescription'];?>" />
			</p>
		</fieldset>
	</form>
	<form id="assignModeratorForm" action="administrate.php?mode=assignmoderator" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.assignModeratorForm'];?></legend>
			<p>
				<label for="assignModeratorUsername"><?php echo $translation['forms.username'];?>:</label><br />
				<input type="text" name="username" id="assignModeratorUsername" /><br />
				<label for="assignModeratorID"><?php echo $translation['forms.forum'];?>:</label><br />
				<?php
					echo '<select name="forumid" id="assignModeratorID">';
					foreach ($forums as $forum) {
							echo '<option value="' . $forum->id . '">' . $forum->name . '</option>'; 
					}
					echo '<option value="-1">All Forums</option>';
					echo '</select><br />'
				?>
				<input type="submit" value="<?php echo $translation['forms.action.assignModerator'];?>" />
			</p>
		</fieldset>
	</form>
	<?php
	} 
	?>
	<form id="changeStatusForm" action="administrate.php?mode=changestatus" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.changeStatusForm'];?></legend>
			<p>
				<label for="changeStatusUsername"><?php echo $translation['forms.username'];?>:</label><br />
				<input type="text" name="username" id="changeStatusUsername" /><br />
				<label for="changeStatusType"><?php echo $translation['forms.forum'];?>:</label><br />
				<?php
					echo '<select name="type" id="changeStatusType">';
							echo '<option value="' . User::USER_TYPE . '">' . $translation[$userTypeToTranslation[User::USER_TYPE]] . '</option>';
							echo '<option value="' . User::MODERATOR_TYPE . '">' . $translation[$userTypeToTranslation[USER::MODERATOR_TYPE]] . '</option>'; 
							echo '<option value="' . User::ADMIN_TYPE . '">' . $translation[$userTypeToTranslation[User::ADMIN_TYPE]] . '</option>';  
					echo '</select><br />'
				?>
				<input type="submit" value="<?php echo $translation['forms.action.changeStatus'];?>" />
			</p>
		</fieldset>
	</form>
	<form id="deleteUserForm" action="administrate.php?mode=deleteuser" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.deleteUserForm'];?></legend>
			<p>
				<label for="deleteUserUsername"><?php echo $translation['forms.username'];?>:</label><br />
				<input type="text" name="username" id="deleteUserUsername" /><br />
				<input type="submit" value="<?php echo $translation['forms.action.deleteUser'];?>" />
			</p>
		</fieldset>
	</form>
</div>
