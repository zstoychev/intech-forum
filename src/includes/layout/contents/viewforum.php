<?php
	$forum = $requestData['forum'];
	$numberOfPages = $forum->getNumberOfPages(TOPICS_PER_PAGE);
	$topics = $forum->getTopicsOnPage($requestData['page'], TOPICS_PER_PAGE);
?>
<h2><a href="index.php"><?php echo $translation['index'];?></a> &gt;&gt; <a href="<?php echo $forum->getLink();?>"><?php echo $forum->name;?></a></h2>
<div class="contentBody">
	<?php
	if (isset($requestData['user'])) { 
	?>
	<p class="buttonContainer"><?php echo '<a href="post.php?forumid=' . $forum->id . '">' . $translation['newTopic'] . '</a>'?></p>
	<?php
	} 
	?>
	<p><?php
	echo $translation['pages'];
	for ($i = 1; $i <= $numberOfPages; $i++) {
		if ($i == $requestData['page']) {
			echo " <strong>$i</strong>";
		} else {
			echo " <a href=\"" . $forum->getPageLink($i) . "\">$i</a>";
		}
	}
	?></p>
	<?php
	if (empty($topics)) {
		echo '<p>' . $translation['forum.noTopics'] . '</p>';
	} else {
	?>
		<table class="dataTable" summary="<?php echo $translation['forum.forums'];?>">
			<caption><?php echo $translation['forum.forums'];?></caption>
			<col class="title odd" />
			<col class="even" />
			<col class="odd" />
			<col class="even" />
			<thead>
				<tr>
					<th><?php echo $translation['topic.title'];?></th>
					<th><?php echo $translation['view.totalPosts'];?></th>
					<th><?php echo $translation['view.totalViews'];?></th>
					<th><?php echo $translation['view.lastPost'];?></th>
				</tr>
			</thead>
			<tbody>
		<?php
			foreach($topics as $topic) {
				echo "<tr>";
				echo "<td><a href=\"" . $topic->getLink() . "\">$topic->title</a></td>";
				echo "<td>" . $topic->getNumberOfPosts() . "</td>";
				echo "<td>" . $topic->views_count . "</td>";
				echo "<td>";
				$lastPost = $topic->getLastPost();
				if ($lastPost === FALSE) {
					echo $translation['view.lastPost.noPost'];
				} else {
					$lastPostPoster = $lastPost->getPoster();
					echo "<a href=\"" . $lastPost->getLink(POSTS_PER_PAGE) . "\">$lastPost->posted_time</a><br />";
					echo $translation['view.lastPost.by'] . " <a href=\"" . $lastPostPoster->getLink() . "\">$lastPostPoster->name</a>";
				}
				echo "</td>";
				echo "</tr>";
			} 
		?>
			</tbody>
		</table>
	<?php
	}
	echo $translation['pages'];
	for ($i = 1; $i <= $numberOfPages; $i++) {
		if ($i == $requestData['page']) {
			echo " <strong>$i</strong>";
		} else {
			echo " <a href=\"" . $forum->getPageLink($i) . "\">$i</a>";
		}
	}
	if (isset($requestData['user'])) { 
	?>
	<p class="buttonContainer"><?php echo '<a href="post.php?forumid=' . $forum->id . '">' . $translation['newTopic'] . '</a>'?></p>
	<?php
	} 
	?>
</div>
