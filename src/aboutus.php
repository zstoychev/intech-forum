<?php
require_once 'includes/Controller.php';

class AboutUsController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}

	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_ABOUT_US;
	}
}

$controller = new AboutUsController();

include 'includes/ControllerExecutor.php';
?>
