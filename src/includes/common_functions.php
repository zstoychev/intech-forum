<?php
require_once 'Model.php';

function getForums($databaseConnection, $forUpdate = false) {
	$result = array();
	
	$forumsRows = $databaseConnection->selectAndLock("* FROM forums ORDER BY id", $forUpdate);
	while ($forumRow = $databaseConnection->fetchRow($forumsRows)) {
		$result[] = new Forum($databaseConnection, $forumRow['id'], $forUpdate, $forumRow);
	}
	
	return $result;
}

function redirect($location) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $location");
}

function isUserLoggedIn(){
  return isset($_SESSION['user_id']) and is_numeric($_SESSION['user_id']);
}

function hashString($string){
	return md5(HASH_SALT.$string);
}

function isValidEmail($string) {
	return preg_match("/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i", $string) != 0;
}

function isANumber($string) {
	return strlen($string) > 0 && ctype_digit($string);
}

function containsHTMLSpecialCharacter($string) {
	return strpos($string, '<') !== FALSE || strpos($string, '>') !== FALSE || strpos($string, '&') !== FALSE;
}

function getGravatarURL($email, $size=80){
	return htmlentities("http://www.gravatar.com/avatar.php?gravatar_id=".md5(strtolower($email)).
		"&default=".urlencode(DEFAULT_USER_AVATAR_URL).
		"&size=".$size); 
}

function urls_to_links( $string ){
  return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $string);
}
 
function emails_to_links( $string ){
  return preg_replace('@([\w\d-\._]{3,}\@[\w\d-\._]{3,}\.[\w\d-\._]{2,})@', '<a href="mailto:$1">$1</a>', $string);
}
 
function html_post( $string ){
  $string = "<p>".str_replace("\n","</p>\n<p>",$string)."</p>";
  $string = urls_to_links($string);
  $string = emails_to_links($string);
  return preg_replace('/<p>\s*?<\/p>/', "", $string);
}
?>
