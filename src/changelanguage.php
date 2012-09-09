<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class ChangeLanguageController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}
	
	public function isUserForUpdate() {
		return TRUE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		if (!isset($_GET['language']) || !isANumber($_GET['language']) || (int) $_GET['language'] < 0 || (int) $_GET['language'] >= NUMBER_OF_LANGUAGES) {
			throw new InvalidRequestSyntaxException();			
		}
		
		if (isset($requestData['user'])) {
			$requestData['user']->language = (int) $_GET['language'];
			$requestData['user']->save();
		} else {
			setcookie("language", $_GET['language'], time() + 365*24*60*60);
		}
		
		if (!empty($_SERVER['HTTP_REFERER'])) {
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect("index.php");
		}
	}
}

$controller = new ChangeLanguageController();

include 'includes/ControllerExecutor.php';
?>
