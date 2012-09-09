<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class LoginController implements Controller {
	public function getPOSTTextFields() {
		return array(
			'username',
			'password',
		);
	}

	public function isUserForUpdate() {
		return FALSE;
	}

	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_LOGIN;
		
		if (isset($requestData['user'])) {
			redirect("index.php");
			unset($requestData['content']);
			return;
		}
		
		$requestData['forum_input_errors'] = array();
		
		if (isset($_GET['mode']) && $_GET['mode'] == "login") {
			if (!isset($_POST['username']) || !isset($_POST['password'])) {
				throw new InvalidRequestSyntaxException();
			}
			$user = User::getUser($databaseConnection, $_POST['username']);
			if ($user === FALSE || $user->password != hashString($_POST['password'])) {
				$requestData['forum_input_errors'][] = FIE_INVALID_LOGIN;
			} else {
				$_SESSION['user_id'] = $user->id;
				redirect("index.php");
				unset($requestData['content']);
				return;
			}
		}
	}
}

$controller = new LoginController();

include 'includes/ControllerExecutor.php';
?>
