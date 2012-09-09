<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class RegisterController implements Controller {
	public function getPOSTTextFields() {
		return array(
			'username',
			'password',
			'passwordComfirm',
			'email',
			'antiBot',
		);
	}

	public function isUserForUpdate() {
		return FALSE;
	}
	
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_REGISTER;
		
		if (isset($requestData['user'])) {
			redirect("index.php");
			unset($requestData['content']);
			return;
		}
		
		$requestData['forum_input_errors'] = array();

		$fields = array(
			'username',
			'password',
			'passwordComfirm',
			'email',
			'language',
			'style',
			'antiBot',
		);
		$username = "";
		$password = "";
		$passwordComfirm = "";
		$email = "";
		$language = $requestData['defaultLanguage'];
		$style = $requestData['defaultStyle'];
		$antiBot = "";
		
		if (isset($_GET['mode']) && $_GET['mode'] == "register") {
			if (!isset($_SESSION['registration_bot_check'])) {
				throw new InvalidRequestSyntaxException();
			}
			foreach($fields as $field) {
				if (!isset($_POST[$field])) {
					throw new InvalidRequestSyntaxException();
				} else {
					$$field = $_POST[$field];
				}
			}
			
			if (containsHTMLSpecialCharacter($username) || strlen($username) < 5 || strlen($username) > 255) {
				$requestData['forum_input_errors'][] = FIE_INVALID_USERNAME;
			}
			if (User::getUser($databaseConnection, $username) !== FALSE) {
				$requestData['forum_input_errors'][] = FIE_USERNAME_EXISTS;
			}
			if (strlen($password) < 5 || strlen($password) > 255) {
				$requestData['forum_input_errors'][] = FIE_INVALID_PASSWORD_LENGTH;
			}
			if ($password != $passwordComfirm) {
				$requestData['forum_input_errors'][] = FIE_PASSWORDS_DISMATCH;
			}
			if (!isValidEmail($email) || strlen($email) > 255) {
				$requestData['forum_input_errors'][] = FIE_INVALID_EMAIL;
			}
			if (!isANumber($language) || (int) $language < 0 || (int) $language >= NUMBER_OF_LANGUAGES) {
				throw new InvalidRequestSyntaxException();
			}
			if (!isANumber($style) || (int) $style < 0 || (int) $style >= NUMBER_OF_STYLES) {
				throw new InvalidRequestSyntaxException();
			}
			if ($_SESSION['registration_bot_check'] != $antiBot) {
				$requestData['forum_input_errors'][] = FIE_INVALID_ANTIBOT_INPUT;
			}
			
			unset($_SESSION['registration_bot_check']);

			if (empty($requestData['forum_input_errors'])) {
				$password = hashString($password);
				$userType = User::USER_TYPE;
				if ($config['setUp']) {
					$userType = User::ADMIN_TYPE;
				}
				$user = User::create($databaseConnection, $username, $email, $password, $userType, $language, $style);
				$_SESSION['user_id'] = $user->id;
				$requestData['content'] = CONTENT_MESSAGE;
				$requestData['message'] = MESSAGE_SUCCESSFUL_REGISTRATION;
				return;
			}
			
			$password = "";
			$passwordComfirm = "";
			$antiBot = "";
		}
		$requestData['registration_bot_check'] = rand(pow(10, ANTI_BOT_LEN - 1), pow(10, ANTI_BOT_LEN) - 1);
		$_SESSION['registration_bot_check'] = $requestData['registration_bot_check'];
		
		$requestData['fields'] = array();
		foreach ($fields as $field) {
			$requestData['fields'][$field] = $$field;
		}
	}
}

$controller = new RegisterController();

include 'includes/ControllerExecutor.php';
?>
