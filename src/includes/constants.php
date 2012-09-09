<?php
$contentConstants = array(
	"CONTENT_MESSAGE",
	"CONTENT_INDEX",
	"CONTENT_VIEW_FORUM",
	"CONTENT_VIEW_TOPIC",
	"CONTENT_REGISTER",
	"CONTENT_LOGIN",
	"CONTENT_VIEW_PROFILE",
	"CONTENT_EDIT_PROFILE",
	"CONTENT_ADMINISTRATE",
	"CONTENT_ABOUT_US",
);

foreach ($contentConstants as $index => $constant) {
	define($constant, $index);
}

$contentTypeToFile = array(
	CONTENT_MESSAGE => 'message.php',
	CONTENT_INDEX => 'index.php',
	CONTENT_VIEW_FORUM => 'viewforum.php',
	CONTENT_VIEW_TOPIC => 'viewtopic.php',
	CONTENT_REGISTER => 'register.php',
	CONTENT_LOGIN => 'login.php',
	CONTENT_VIEW_PROFILE => 'viewprofile.php',
	CONTENT_EDIT_PROFILE => 'editprofile.php',
	CONTENT_POST => 'post.php',
	CONTENT_ADMINISTRATE => 'administrate.php',
	CONTENT_ABOUT_US => 'aboutus.php',
);


$messageConstants = array(
	"MESSAGE_ERROR_SERVER",
	"MESSAGE_ERROR_INVALID_REQUEST_SYNTAX",
	"MESSAGE_ERROR_NONEXISTENT_REQUESTED_RESOURCE",
	"MESSAGE_SUCCESSFUL_REGISTRATION",
	"MESSAGE_SUCCESSFUL_PROFILE_UPDATE",
	"MESSAGE_SUCCESSFUL_FORUM_CREATION",
	"MESSAGE_SUCCESSFUL_FORUM_DELETION",
	"MESSAGE_SUCCESSFUL_FORUM_RENAME",
	"MESSAGE_SUCCESSFUL_FORUM_DESCRIPTION_UPDATE",
	"MESSAGE_SUCCESSFUL_MODERATOR_ASSIGNMENT",
	"MESSAGE_SUCCESSFUL_USER_STATUS_CHANGE",
	"MESSAGE_SUCCESSFUL_USER_DELETION",
	"MESSAGE_SUCCESSFUL_POSTING",
	"MESSAGE_SUCCESSFUL_POST_UPDATE",
	"MESSAGE_SUCCESSFUL_POST_DELETION",

	"FIE_INVALID_LOGIN",
	"FIE_INVALID_USERNAME",
	"FIE_USERNAME_EXISTS",
	"FIE_INVALID_PASSWORD_LENGTH;",
	"FIE_PASSWORDS_DISMATCH",
	"FIE_INVALID_EMAIL",
	"FIE_INVALID_ANTIBOT_INPUT",
	"FIE_INVALID_FORUM_DATA",
	"FIE_USER_NOT_MODERATOR",
	"FIE_INVALID_POST_INPUT",
	"FIE_NOT_MATCHING_PASSWORD",
	"FIE_USER_NOT_MODERATOR",

	"LINK_ADMIN_PANEL",
	'LINK_POST',
);

foreach ($messageConstants as $index => $constant) {
	define($constant, $index);
}

$messageToTranslation = array(
	FIE_INVALID_LOGIN => 'fie.invalidLogin',
	FIE_INVALID_USERNAME => 'fie.invalidUsername',
	FIE_USERNAME_EXISTS => 'fie.usernameExists',
	FIE_INVALID_PASSWORD_LENGTH => 'fie.invalidPasswordLength',
	FIE_PASSWORDS_DISMATCH => 'fie.passwordsDismatch',
	FIE_INVALID_EMAIL => 'fie.invalidEmail',
	FIE_INVALID_ANTIBOT_INPUT => 'fie.invalidAntibotInput',
	FIE_INVALID_POST_INPUT => 'fie.invalidPostInput',
	FIE_NOT_MATCHING_PASSWORD => 'fie.notMatchingPassword',
	FIE_INVALID_FORUM_DATA => 'fie.invalidForumData',
	FIE_USER_NOT_MODERATOR => 'fie.userNotModerator',
	LINK_ADMIN_PANEL => 'link.adminPanel',
	LINK_POST => 'link.post',
	MESSAGE_SUCCESSFUL_REGISTRATION => 'message.successfulRegistration',
	MESSAGE_SUCCESSFUL_POSTING => 'message.successfulPosting',
	MESSAGE_SUCCESSFUL_POST_UPDATE => 'message.successfulPostUpdate',
	MESSAGE_SUCCESSFUL_POST_DELETION => 'message.successfulPostDeletion',
	MESSAGE_SUCCESSFUL_PROFILE_UPDATE => 'message.sucessfulProfileUpdate',
	MESSAGE_SUCCESSFUL_FORUM_CREATION => 'message.successfulForumCreation',
	MESSAGE_SUCCESSFUL_FORUM_DELETION => 'message.successfulForumDeletion',
	MESSAGE_SUCCESSFUL_FORUM_RENAME => 'message.successfulForumRename',
	MESSAGE_SUCCESSFUL_FORUM_DESCRIPTION_UPDATE => 'message.successfulForumDescriptionUpdate',
	MESSAGE_SUCCESSFUL_MODERATOR_ASSIGNMENT => 'message.successfulModeratorAssignment',
	MESSAGE_SUCCESSFUL_USER_STATUS_CHANGE => 'message.sucessfulUserStatusChange',
	MESSAGE_SUCCESSFUL_USER_DELETION => 'message.successfulUserDeletion',
	MESSAGE_ERROR_INVALID_REQUEST_SYNTAX => 'message.error.invalidRequestSyntax',
	MESSAGE_ERROR_NONEXISTENT_REQUESTED_RESOURCE => 'message.error.nonexistentRequestedResource',
	MESSAGE_ERROR_SERVER => 'message.error.server',
);

$languages = array(
	"bulgarian",
	"english"
);
define("NUMBER_OF_LANGUAGES", count($languages));

$styles = array(
	"Modern",
	"Pink",
);
$stylesToFile = array(
	"Modern" => "modern.css",
	"Pink" => "pink.css",
);
define("NUMBER_OF_STYLES", count($styles));

$userTypeToTranslation = array(
	1 => "user.type.user",
	2 => "user.type.moderator",
	3 => "user.type.administrator",
);

define("SITE_HTTP_ROOT", "http://213.240.254.122/ForumTest/");
define("ANTI_BOT_LEN", 6);
define("DEFAULT_USER_AVATAR_URL", SITE_HTTP_ROOT . "images/defaultAvatar.png");
define("HASH_SALT", "Xr*sgu^uGs-TkTE@G*=S5QS42XgWa(K4, 6bb09ddc167c85c0805685baf5239878");
define("POSTS_PER_PAGE", 15);
define("TOPICS_PER_PAGE", 15);
?>
