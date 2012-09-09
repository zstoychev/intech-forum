<?php
require_once 'includes/constants.php';
require_once 'includes/SQLConnection.php';
require_once 'includes/Model.php';
require_once 'includes/Controller.php';
require_once 'includes/common_functions.php';

class PostController implements Controller {
	public function getPOSTTextFields() {
		return array(
			'title',
			'message',
		);
	}

	public function isUserForUpdate() {
		return FALSE;
	}

	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_POST;
		
		if (!isset($requestData['user'])) {
			redirect("index.php");
			unset($requestData['content']);
			return;
		}
		
		$requestData['forum_input_errors'] = array();
		
		if (isset($_GET['forumid'])) {
			if (!isANumber($_GET['forumid'])) {
				throw new InvalidRequestSyntaxException();
			}
			
			$requestData['forum'] = new Forum($databaseConnection, (int) $_GET['forumid']);
			$requestData['postlink'] = htmlentities("post.php?forumid=" . $requestData['forum']->id . "&mode=post");
		} else if (isset($_GET['topicid'])) {
			if (!isANumber($_GET['topicid'])) {
				throw new InvalidRequestSyntaxException();
			}
			
			$requestData['topic'] = new Topic($databaseConnection, (int) $_GET['topicid'], TRUE);
			$requestData['postlink'] = htmlentities("post.php?topicid=" . $requestData['topic']->id . "&mode=post");
		} else if (isset($_GET['postid'])) {
			if (!isANumber($_GET['postid'])) {
				throw new InvalidRequestSyntaxException();
			}
			
			$requestData['post'] = new Post($databaseConnection, (int) $_GET['postid'], TRUE);
			$requestData['postlink'] = htmlentities("post.php?postid=" . $requestData['post']->id . "&mode=edit");
			
			if (!$requestData['user']->hasModaratingAccessTo($requestData['post'])) {
				redirect("index.php");
				unset($requestData['content']);
				return;
			}
		} else {
			throw new InvalidRequestSyntaxException();
		}
		
		if (isset($_GET['mode'])) {
			if ($_GET['mode'] == "delete" || $_GET['mode'] == 'edit') {
				if (!isset($requestData['post'])) {
					throw new InvalidRequestSyntaxException();
				}				
			}
			
			if ($_GET['mode'] != "delete") {
				if (empty($_POST['message'])) {
					$requestData['forum_input_errors'][] = FIE_INVALID_POST_INPUT;
					$requestData['fields'] = array('title' => $_POST['title'], 'isTextile' => isset($_POST['isTextile']) ? TRUE : FALSE, 'message' => '');
					return;
				}
			}
			
			if ($_GET['mode'] == 'post') {
				if (isset($requestData['forum'])) {
					if (empty($_POST['title'])) {
						$requestData['forum_input_errors'][] = FIE_INVALID_POST_INPUT;
						$requestData['fields'] = array('title' => '', 'isTextile' => isset($_POST['isTextile']) ? TRUE : FALSE, 'message' => $_POST['message']);
						return;
					}
					
					$topic = Topic::create($databaseConnection, $requestData['forum'], $requestData['user'], htmlspecialchars($_POST['title']), TRUE);
					$post = Post::create($databaseConnection, $topic, $requestData['user'], $_POST['message'], isset($_POST['isTextile']) ? 'true' : 'false');
				} else if (isset($requestData['topic'])) {
					$requestData['topic']->last_updated = "now()";
					$requestData['topic']->save();
					$post = Post::create($databaseConnection, $requestData['topic'], $requestData['user'], $_POST['message'], isset($_POST['isTextile']) ? 'true' : 'false');
				} else {
					throw new InvalidRequestSyntaxException();
				}
				$requestData['message'] = MESSAGE_SUCCESSFUL_POSTING;
				$requestData['message_links'] = array(LINK_POST => $post->getLink(POSTS_PER_PAGE));
			} else if ($_GET['mode'] == "delete") {
				if ($requestData['post']->isFirstInTheTopic()) {
					$requestData['post']->getTopic(TRUE)->delete();
				} else {
					$requestData['post']->delete();
				}
				$requestData['message'] = MESSAGE_SUCCESSFUL_POST_DELETION;
			} else if ($_GET['mode'] == "edit") {
				if ($requestData['post']->isFirstInTheTopic()) {
					if (empty($_POST['title'])) {
						if (empty($_POST['title'])) {
							$requestData['forum_input_errors'][] = FIE_INVALID_POST_INPUT;
							$requestData['fields'] = array('title' => '', 'isTextile' => isset($_POST['isTextile']) ? TRUE : FALSE, 'message' => $_POST['message']);
							return;
						}
					}
					
					$topic = $requestData['post']->getTopic(TRUE);
					$topic->title = htmlspecialchars($_POST['title']);
					$topic->save();
				}
				$requestData['post']->content = $_POST['message'];
				$requestData['post']->is_textile = isset($_POST['isTextile']) ? 'true' : 'false';
				$requestData['post']->last_updated_time = "now()";
				$requestData['post']->save();
				$requestData['message'] = MESSAGE_SUCCESSFUL_POST_UPDATE;
				$requestData['message_links'] = array(LINK_POST => $requestData['post']->getLink(POSTS_PER_PAGE));
			} else {
				throw new InvalidRequestSyntaxException();
			}
			
			if (empty($requstData['forum_input_error'])) {
				$requestData['content'] = CONTENT_MESSAGE;
				return;
			}
		}
		
		if (isset($requestData['post'])) {
			$requestData['fields'] = array('title' => $requestData['post']->getTopic()->title, 'isTextile' => $requestData['post']->is_textile, 'message' => $requestData['post']->content);
		} else {
			$requestData['fields'] = array('title' => "", 'isTextile' => FALSE,  'message' => "");
		}
	}
}

$controller = new PostController();

include 'includes/ControllerExecutor.php';
?>
