<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class GetNewerPostController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}
	
	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		if (!isset($_GET['topicid']) || !isANumber($_GET['topicid']) || !isset($_GET['postid']) || !isANumber($_GET['postid']) || !isset($_GET['postedTime']) || !isANumber($_GET['postedTime'])) {
			throw new InvalidRequestSyntaxException();			
		}
		
		header('Content-type: application/xml');
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo "<post>\n";
		try {
			$topic = new Topic($databaseConnection, (int) $_GET['topicid']);
			$newerPost = $topic->getPostAfter((int) $_GET['postid'], date("Y-m-d G:i:s", (int) $_GET['postedTime']));
			if ($newerPost !== FALSE) {
				echo '<link>' . $newerPost->getLink(POSTS_PER_PAGE) . '</link>' . "\n";
			}
		} catch (NonexistentDatabaseEntryException $e) {
		}
		echo "</post>\n";
	}
}

$controller = new GetNewerPostController();

include 'includes/ControllerExecutor.php';
?>
