<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class AdministrateController implements Controller {
	public function getPOSTTextFields() {
		return array(
			'name', 
			'description',
			'username'
		);
	}

	public function isUserForUpdate() {
		return FALSE;
	}

	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_ADMINISTRATE;
		
		if (!isset($requestData['user']) || $requestData['user']->type != User::ADMIN_TYPE) {
			redirect("index.php");
			unset($requestData['content']);
			return;
		}
		
		$requestData['forum_input_errors'] = array();
		
		if (isset($_GET['mode'])) {
			$requestData['message_links'] = array(LINK_ADMIN_PANEL => "administrate.php");

			$modeToFields = array(
				'createforum' => array('name', 'description'),
				'deleteforum' => array('forumid'),
				'renameforum' => array('forumid', 'name'),
				'updatedescriptionforum' => array('forumid', 'description'),
				'assignmoderator' => array('username', 'forumid'),
				'changestatus' => array('username', 'type'),
				'deleteuser' => array('username'),
			);
			
			if (!array_key_exists($_GET['mode'], $modeToFields)) {
				throw new InvalidRequestSyntaxException();
			}
			
			$fields = $modeToFields[$_GET['mode']];
			foreach($fields as $field) {
				if (!isset($_POST[$field])) {
					throw new InvalidRequestSyntaxException();
				}
			}
			
			if (in_array('name', $fields)) {
				if ($_POST['name'] == "") {
					$requestData['forum_input_errors'][] = FIE_INVALID_FORUM_DATA;
				}
				$_POST['name'] = htmlspecialchars($_POST['name']);
			}
			if (in_array('description', $fields)) {
				if ($_POST['description'] == "") {
					$requestData['forum_input_errors'][] = FIE_INVALID_FORUM_DATA;
				}
				$_POST['description'] = htmlspecialchars($_POST['description']);
			}
			if (in_array('forumid', $fields)) {
				if (!isANumber($_POST['forumid']) && (int) $_POST['forumid'] != -1) {
					throw new InvalidRequestSyntaxException();
				}
			}
			if (in_array('username', $fields)) {
				if ($_POST['username'] == "") {
					throw new NonexistentDatabaseEntryException();
				}
			}
			if (in_array('type', $fields)) {
				if ((int) $_POST['type'] < 1 && (int) $_POST['type'] > 3) {
					throw new InvalidRequestSyntaxException();
				}
			}
			
			if (empty($requestData['forum_input_errors'])) {
				if ($_GET['mode'] == "createforum") {
					Forum::create($databaseConnection, $_POST['name'], $_POST['description']);
					$requestData['message'] = MESSAGE_SUCCESSFUL_FORUM_CREATION;
				} else if ($_GET['mode'] == "deleteforum") {
					$forum = new Forum($databaseConnection, (int) $_POST['forumid'], TRUE);
					$forum->delete();
					$requestData['message'] = MESSAGE_SUCCESSFUL_FORUM_DELETION;
				} else if ($_GET['mode'] == "renameforum") {
					$forum = new Forum($databaseConnection, (int) $_POST['forumid'], TRUE);
					$forum->name = $_POST['name'];
					$forum->save();
					$requestData['message'] = MESSAGE_SUCCESSFUL_FORUM_RENAME;
				} else if ($_GET['mode'] == "updatedescriptionforum") {
					$forum = new Forum($databaseConnection, (int) $_POST['forumid'], TRUE);
					$forum->description = $_POST['description'];
					$forum->save();
					$requestData['message'] = MESSAGE_SUCCESSFUL_FORUM_DESCRIPTION_UPDATE;
				} else if ($_GET['mode'] == "assignmoderator") {
					$user = User::getUser($databaseConnection, $_POST['username'], TRUE);
					if ((int) $_POST['forumid'] != -1) {
						$forum = new Forum($databaseConnection, (int) $_POST['forumid']);
					}
					if ($user === FALSE) {
						throw new NonexistentDatabaseEntryException();
					}
					if ($user->type != User::MODERATOR_TYPE) {
						$requestData['forum_input_errors'][] = FIE_USER_NOT_MODERATOR;
					} else {
						$user->assignModeratorAccessTo((int) $_POST['forumid']);
						$requestData['message'] = MESSAGE_SUCCESSFUL_MODERATOR_ASSIGNMENT;
					}
				} else if ($_GET['mode'] == "changestatus") {
					$user = User::getUser($databaseConnection, $_POST['username'], TRUE);
					if ($user === FALSE) {
						throw new NonexistentDatabaseEntryException();
					}
					if ($user->type == USER::MODERATOR_TYPE) {
						$user->revokeModeratorRights();
					}
					$user->type = (int) $_POST['type'];
					$user->save();
					$requestData['message'] = MESSAGE_SUCCESSFUL_USER_STATUS_CHANGE;
				} else if ($_GET['mode'] == "deleteuser") {
					$user = User::getUser($databaseConnection, $_POST['username'], TRUE);
					if ($user === FALSE) {
						throw new NonexistentDatabaseEntryException();
					}
					if ($user->id == $requestData['user']->id) {
						throw new InvalidRequestSyntaxException();
					}
					$user->delete();
					$requestData['message'] = MESSAGE_SUCCESSFUL_USER_DELETION;
				}
			}
			if (empty($requestData['forum_input_errors'])) {
				$requestData['content'] = CONTENT_MESSAGE;
				return;
			}
		}
		
		$requestData['forums'] = getForums($databaseConnection);
	}
}

$controller = new AdministrateController();

include 'includes/ControllerExecutor.php';
?>
