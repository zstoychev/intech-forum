<script type="text/javascript" src="scripts/events.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
var isIE = navigator.appName == 'Microsoft Internet Explorer' && navigator.userAgent.indexOf('Opera') < 1 ? true : false;

var stylesNames = [<?php
$stylesStrings = array();
foreach($styles as $style) {
	$stylesStrings[] = "\"$style\"";
}
echo implode(", ", $stylesStrings);?>];

<?php
if ($requestData['content'] == CONTENT_VIEW_TOPIC) {
	$lastPost = $requestData['topic']->getLastPost();
	echo 'var topicID = ' . $requestData['topic']->id . ";\n";
	echo 'var currentResourceLink = "' . html_entity_decode($requestData['topic']->getPageLink($requestData['page'])) . "\";\n";
	echo 'var lastPostID = ' . $lastPost->id . ";\n";
	echo 'var lastPostPostedTime = "' . strtotime($lastPost->posted_time) . "\";\n";
	echo 'var newMessageString = "' . $translation['newMessage'] . "\";\n";
	echo 'var reloadCurrentPageString = "' . $translation['reloadCurrentPage'] . "\";\n";
	echo 'var goToPostString = "' . $translation['goToPost'] . "\";\n";
	echo 'var closeThisBoxString = "' . $translation['closeThisBox'] . "\";\n";
	echo 'var viewingTopic = true;';
} else {
	echo 'var viewingTopic = false;';
}
?>


if (document.implementation.hasFeature("HTML", "2.0") && document.implementation.hasFeature("HTMLEvents", "2.0")) {
	window.addEventListener("load", init, false);
} else if  (isIE && document.attachEvent) {
	window.attachEvent("onload", init);
}
/* ]]> */
</script>
