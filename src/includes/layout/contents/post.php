<?php
$withTopic = isset($requestData['forum']) || (isset($requestData['post']) && $requestData['post']->isFirstInTheTopic());
$headerLink = '';
$header = '';
if (isset($requestData['forum'])) {
	$header = $translation['post.newTopic'] . ' (' . $requestData['forum']->name . ')';
	$headerLink = "post.php?forumid=" . $requestData['forum']->id;
} else if (isset($requestData['topic'])) {
	$header = $translation['post.newPost'] . ' (' . $requestData['topic']->title . ')';
	$headerLink = "post.php?topicid=" . $requestData['topic']->id;
} else {
	$header = $translation['post.edit'] . ' (' . $requestData['post']->getTopic()->title . ')';
	$headerLink = "post.php?postid=" . $requestData['post']->id;	
}
?>
<h2><a href="<?php echo $headerLink;?>"><?php echo $header;?></a></h2>
<div class="contentBody">
	<?php
	include 'fiemessages.php'; 
	
	$fields = $requestData['fields'];
	?>
	<form id="postForm" action="<?php echo $requestData['postlink'];?>" method="post">
		<fieldset>
			<legend><?php echo $translation['forms.post'];?></legend>
			<?php
			if ($withTopic) {
			?>
			<dl>
			<dt><label for="title"><?php echo $translation['post.title'];?>:</label></dt>
			<dd><input type="text" name="title" id="title" value="<?php echo $fields['title'];?>" /></dd>
			</dl>
			<?php
			} 
			?>
			<input type="checkbox" name="isTextile" id="isTextile"<?php echo $fields['isTextile'] ? ' checked="checked"' : '';?> /><label for="isTextile"> <?php echo $translation['post.enableTextile'];?></label>
			<dl>
				<dt><label for="message"><?php echo $translation['post.message'];?>:</label></dt>
				<dd><textarea name="message" id="message" rows="20" cols="80"><?php echo $fields['message'];?></textarea></dd>
			</dl>
			<p><input type="submit" value="<?php echo $translation['post.submit'];?>" /> <input type="reset" value="<?php echo $translation['form.reset'];?>" /></p>
		</fieldset>
	</form>
	<?php
	if (isset($requestData['post'])) {
	?>
	<p class="buttonContainer"><?php echo '<a href="post.php?postid=' . $requestData['post']->id . '&amp;mode=delete">' . $translation['post.delete'] . '</a>'?></p>
	<?php
	}
	?>
</div>