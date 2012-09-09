<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class EditProfileController implements Controller {
	public function getPOSTTextFields() {
		return array(
			'currentPassword',
			'password',
			'passwordComfirm',
			'email',
			'location',
		);
	}

	public function isUserForUpdate() {
		return TRUE;
	}

	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_EDIT_PROFILE;
		
		if (!isset($requestData['user'])) {
			redirect("index.php");
			unset($requestData['content']);
			return;
		}
		
		$requestData['forum_input_errors'] = array();
		
		$fields = array(
			'currentPassword',
			'password',
			'passwordComfirm',
			'email',
			'language',
			'style',
			'location',
		);
		$currentPassword = "";
		$password = "";
		$passwordComfirm = "";
		$email = $requestData['user']->email;
		$language = $requestData['user']->language;
		$style = $requestData['user']->style;
		$location = $requestData['user']->location;
		
		if (isset($_GET['mode']) && $_GET['mode'] == "edit") {
			foreach($fields as $field) {
				if (!isset($_POST[$field])) {
					throw new InvalidRequestSyntaxException();
				} else {
					$$field = $_POST[$field];
				}
			}
			
			if ($currentPassword != "") {
				if (hashString($currentPassword) != $requestData['user']->password) {
					$requestData['forum_input_errors'][] = FIE_NOT_MATCHING_PASSWORD;
				}
				if ($password != "" && (strlen($password) < 5 || strlen($password) > 255)) {
					$requestData['forum_input_errors'][] = FIE_INVALID_PASSWORD_LENGTH;
				}
				if ($password != $passwordComfirm) {
					$requestData['forum_input_errors'][] = FIE_PASSWORDS_DISMATCH;
				}
			}
			if (!isValidEmail($email) || strlen($email) > 255) {
				$requestData['forum_input_errors'][] = FIE_INVALID_EMAIL;
			}
			if (!isANumber($language) || (int) $language < 0 || (int) $language >= NUMBER_OF_LANGUAGES) {
				$requestData['forum_input_errors'][] = FIE_INVALID_LANGUAGE_ID;
			}
			if (!isANumber($style) || (int) $style < 0 || (int) $style >= NUMBER_OF_STYLES) {
				$requestData['forum_input_errors'][] = FIE_INVALID_STYLE_ID;
			}
			if (strlen($location) > 255) {
				$requestData['forum_input_errors'][] = FIE_INVALID_LOCATION_LENGTH;
			}

			if (empty($requestData['forum_input_errors'])) {
				$user = $requestData['user'];
				if ($currentPassword != "") {
					$user->password = hashString($password);
				}
				$user->email = $email;
				$user->language = $language;
				$user->style = $style;
				$user->location = $location;
				$user->save();
				$requestData['content'] = CONTENT_MESSAGE;
				$requestData['message'] = MESSAGE_SUCCESSFUL_PROFILE_UPDATE;
				return;
			}
			
			$currentPassword = "";
			$password = "";
			$passwordComfirm = "";
		}
		$requestData['fields'] = array();
		foreach ($fields as $field) {
			$requestData['fields'][$field] = $$field;
		}
	}
}

$controller = new EditProfileController();

include 'includes/ControllerExecutor.php';
?>
