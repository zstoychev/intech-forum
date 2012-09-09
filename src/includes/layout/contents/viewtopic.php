<?php
	$topic = $requestData['topic'];
	$forum = $topic->getForum();
	$numberOfPages = $topic->getNumberOfPages(POSTS_PER_PAGE);
	$posts = $topic->getPostsOnPage($requestData['page'], POSTS_PER_PAGE);
?>
<h2><a href="index.php"><?php echo $translation['index'];?></a> &gt;&gt; <a href="<?php echo $forum->getLink();?>"><?php echo $forum->name;?></a> &gt;&gt; <a href="<?php echo $topic->getLink();?>"><?php echo $topic->title;?></a></h2>
<div class="contentBody">
	<h3><a href="<?php echo $topic->getLink();?>"><?php echo $topic->title;?></a></h3>
	<?php
	if (isset($requestData['user'])) { 
	?>
	<p class="buttonContainer"><?php echo '<a href="post.php?topicid=' . $topic->id . '">' . $translation['newReply'] . '</a>'?></p>
	<?php 
	}
	?>
	<p><?php
	echo $translation['pages'];
	for ($i = 1; $i <= $numberOfPages; $i++) {
		if ($i == $requestData['page']) {
			echo " <strong>$i</strong>";
		} else {
			echo " <a href=\"" . $topic->getPageLink($i) . "\">$i</a>";
		}
	}
	?></p>
	<?php
		$textile = new Textile();
	
		foreach($posts as $post) {
			$poster = $post->getPoster();
			echo "<div id=\"post" . $post->id . "\" class=\"post\">";
			$postTitle = $translation['post.postedTime'] . ": " . $post->posted_time;
			if ($post->posted_time != $post->last_updated_time) {
				$postTitle .= " " . $translation['post.lastUpdatedTime'] . ": " . $post->last_updated_time;
			}
			
			echo "<h4><a href=\"" . $post->getLink(POSTS_PER_PAGE) . "\">" . $postTitle . "</a></h4>";
			
			echo "<div class=\"userInfo\">";
			echo "<dl>";
			echo "<dt><a href=\"" . $poster->getLink() . "\">$poster->name</a></dt>";
			echo "<dd>" . $translation[$userTypeToTranslation[$poster->type]] . "</dd>";
			echo '<dd class="avatar"><img src="' . getGravatarURL($poster->email) . '" alt="" /></dd>';
			if ($poster->location != "") {
				echo '<dd>' . $translation['user.location'] . ": " . htmlspecialchars($poster->location, ENT_NOQUOTES) . '</dd>';
			}
			echo "</dl>";
			echo "</div>";
			echo '<div class="messageContent">';
			if ($post->is_textile) {
				echo $textile->TextileThis(htmlspecialchars($post->content, ENT_NOQUOTES));
			} else {
				echo html_post(htmlspecialchars($post->content, ENT_NOQUOTES));
			}
			echo '</div>';
			echo '<hr />';
			if (isset($requestData['user']) && $requestData['user']->hasModaratingAccessTo($post)) {
			?>
			<p class="buttonContainer"><?php echo '<a href="post.php?postid=' . $post->id . '">' . $translation['post.edit'] . '</a>'?> <?php echo '<a href="post.php?postid=' . $post->id . '&amp;mode=delete">' . $translation['post.delete'] . '</a>'?></p>
			<?php
			}
			echo "</div>";
		} 
	?>
	<p><?php
	echo $translation['pages'];
	for ($i = 1; $i <= $numberOfPages; $i++) {
		if ($i == $requestData['page']) {
			echo " <strong>$i</strong>";
		} else {
			echo " <a href=\"" . $topic->getPageLink($i) . "\">$i</a>";
		}
	}
	?></p>
	<?php
	if (isset($requestData['user'])) { 
	?>
	<p class="buttonContainer"><?php echo '<a href="post.php?topicid=' . $topic->id . '">' . $translation['newReply'] . '</a>'?></p>
	<?php 
	}
	?>
	<div id="newerTopicMessage"></div>
</div>
