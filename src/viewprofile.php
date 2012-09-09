<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class ViewProfileController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}

	public function isUserForUpdate() {
		return FALSE;
	}

	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_VIEW_PROFILE;
		
		if (!isset($_GET['userid']) || !ctype_digit($_GET['userid'])) {
			throw new InvalidRequestSyntaxException();
		}
		
		$requestData['profile'] = new User($databaseConnection, (int) $_GET['userid']);
	}
}

$controller = new ViewProfileController();

include 'includes/ControllerExecutor.php';
?>
