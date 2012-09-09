<?php
require_once 'configuration.php';
require_once 'constants.php';
require_once 'SQLConnection.php';
require_once 'Model.php';
require_once 'Controller.php';
require_once 'common_functions.php';

$requestData = array();
$databaseRolledback = FALSE;

foreach ($controller->getPOSTTextFields() as $POSTTextField) {
	if (isset($_POST[$POSTTextField])) {
		$_POST[$POSTTextField] = stripslashes($_POST[$POSTTextField]);
	}
}

try {
	session_start();

	$languageToInclude = $config['defaultLanguageIndex'];
	$requestData['defaultStyle'] = $config['defaultStyleIndex'];

	try {
		$databaseConnection = new MySQLConnection($config['databaseServer'], $config['databaseUsername'], $config['databasePassword'], $config['databaseName']);
		$databaseConnection->sendQuery("START TRANSACTION;");
		
		if (isset($_COOKIE['language']) && isANumber($_COOKIE['language']) && (int) $_COOKIE['language'] >= 0 && (int) $_COOKIE['language'] < NUMBER_OF_LANGUAGES) {
			$languageToInclude = (int) $_COOKIE['language'];
		}
		if (isset($_COOKIE['style']) && isANumber($_COOKIE['style']) && (int) $_COOKIE['style'] >= 0 && (int) $_COOKIE['style'] < NUMBER_OF_STYLES) {
			$requestData['defaultStyle'] = (int) $_COOKIE['style'];
		}
		
		if (isUserLoggedIn()) {
			try {
				$requestData['user'] = new User($databaseConnection, $_SESSION['user_id'], $controller->isUserForUpdate());
				$languageToInclude = $requestData['user']->language;
				$requestData['defaultStyle'] = $requestData['user']->style;
			} catch (NonexistentDatabaseEntryException $e) {
				unset($_SESSION['user_id']);
			}
		}
	} catch (Exception $e) {
		require_once 'languages/' . $languages[$languageToInclude] . '.php';
		$config['name'] = $languageToForumName[$languageToInclude];
		$requestData['defaultLanguage'] = $languageToInclude;
		throw $e;
	}
	
	require_once 'languages/' . $languages[$languageToInclude] . '.php';
	$config['name'] = $languageToForumName[$languageToInclude];
	$requestData['defaultLanguage'] = $languageToInclude; 

	$controller->execute($databaseConnection, $config, $requestData);
} catch (InvalidRequestSyntaxException $e) {
	header("HTTP/1.1 400 Bad Request");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_INVALID_REQUEST_SYNTAX;
	$databaseConnection->sendQuery("ROLLBACK;");
	$databaseRolledback = TRUE;
} catch (NonexistentDatabaseEntryException $e) {
	header("HTTP/1.1 404 Not Found");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_NONEXISTENT_REQUESTED_RESOURCE;
	$databaseConnection->sendQuery("ROLLBACK;");
	$databaseRolledback = TRUE;
} catch (Exception $e) {
	header("HTTP/1.1 500 Internal Server Error");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_SERVER;
	if (isset($databaseConnection)) {
		$databaseConnection->sendQuery("ROLLBACK;");
	}
	$databaseRolledback = TRUE;
}

if (isset($requestData['content'])) {
	include 'layout.php';
}
if (!$databaseRolledback) {
	$databaseConnection->sendQuery("COMMIT;");
}
if (isset($databaseConnection)) {
	$databaseConnection->close();
}
?>
