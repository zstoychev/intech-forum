<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class LogoutController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}

	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		if (isset($_SESSION['user_id'])) {
			unset($_SESSION['user_id']);

		}

		redirect("index.php");
	}
}

$controller = new LogoutController();

include 'includes/ControllerExecutor.php';
?>
