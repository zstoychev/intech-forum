<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';
require_once 'includes/libs/textile/classTextile.php';

class ViewTopicController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}

	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_VIEW_TOPIC;

		if (!isset($_GET['topicid']) || !isANumber($_GET['topicid'])) {
			throw new InvalidRequestSyntaxException();
		}

		$requestData['topic'] = new Topic($databaseConnection, (int) $_GET['topicid']);
		
		$requestData['page'] = 1;
		if (isset($_GET['page']) && isANumber($_GET['page'])) {
			$requestData['page'] = (int) $_GET['page'];
			if ($requestData['page'] < 1 || $requestData['page'] > $requestData['topic']->getNumberOfPages(POSTS_PER_PAGE)) {
				redirect($requestData['topic']->getLink());
				unset($requestData['content']);
				return;
			}
		} else {
			$requestData['topic'] = new Topic($databaseConnection, (int) $_GET['topicid'], TRUE);
			$requestData['topic']->views_count = $requestData['topic']->views_count + 1;
			$requestData['topic']->save();
		}
	}
}

$controller = new ViewTopicController();

include 'includes/ControllerExecutor.php';
?>
