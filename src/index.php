<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class IndexController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}
	
	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_INDEX;

		$requestData['forums'] = getForums($databaseConnection);
	}
}

$controller = new IndexController();

include 'includes/ControllerExecutor.php';
?>
