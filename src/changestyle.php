<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class ChangeStyleController implements Controller {
	public function getPOSTTextFields() {
		return array();
	}
	
	public function isUserForUpdate() {
		return TRUE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		if (!isset($_GET['style']) || !isANumber($_GET['style']) || (int) $_GET['style'] < 0 || (int) $_GET['style'] >= NUMBER_OF_STYLES) {
			throw new InvalidRequestSyntaxException();			
		}
		
		if (isset($requestData['user'])) {
			$requestData['user']->style = (int) $_GET['style'];
			$requestData['user']->save();
		} else {
			setcookie("style", $_GET['style'], time() + 365*24*60*60);
		}
		
		if (!isset($_GET['mode']) || $_GET['mode'] != "ajax") {
			if (!empty($_SERVER['HTTP_REFERER'])) {
				redirect($_SERVER['HTTP_REFERER']);
			} else {
				redirect("index.php");
			}
		}
	}
}

$controller = new ChangeStyleController();

include 'includes/ControllerExecutor.php';
?>
