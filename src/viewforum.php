<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class ViewForumController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}
	
	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_VIEW_FORUM;
		
		if (!isset($_GET['forumid']) || !isANumber($_GET['forumid'])) {
			throw new InvalidRequestSyntaxException();
		}

		$requestData['forum'] = new Forum($databaseConnection, (int) $_GET['forumid']);
		
		$requestData['page'] = 1;
		if (isset($_GET['page']) && isANumber($_GET['page'])) {
			$requestData['page'] = (int) $_GET['page'];
			if ($requestData['page'] < 1 || $requestData['page'] > $requestData['forum']->getNumberOfPages(TOPICS_PER_PAGE)) {
				redirect($requestData['forum']->getLink());
				unset($requestData['content']);
				return;
			}
		}
	}
}

$controller = new ViewForumController();

include 'includes/ControllerExecutor.php';
?>
