<h2><a href="index.php"><?php echo $translation['index'];?></a></h2>
<div class="contentBody">
	<?php
	if (empty($requestData['forums'])) {
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
					<th><?php echo $translation['forum.name'];?></th>
					<th><?php echo $translation['view.totalTopics'];?></th>
					<th><?php echo $translation['view.totalPosts'];?></th>
					<th><?php echo $translation['view.lastPost'];?></th>
				</tr>
			</thead>
			<tbody>
		<?php
			foreach($requestData['forums'] as $forum) {
				echo "<tr>";
				echo "<td><dl><dt><a href=\"" . $forum->getLink() . "\">$forum->name</a></dt><dd>$forum->description</dd></dl></td>";
				echo "<td>" . $forum->getNumberOfTopics() . "</td>";
				echo "<td>" . $forum->getNumberOfPosts() . "</td>";
				echo "<td>";
				$lastPost = $forum->getLastPost();
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
	?>
</div>

