<?php
require_once 'SQLConnection.php';

class NonexistentDatabaseEntryException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class ReadOnlyDatabaseFieldException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class NonexistentDatabaseFieldException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class DatabaseObject {
	protected $databaseConnection;
	protected $table;
	protected $id;
	private $fieldToData;
	private $stringFields;
	private $updateableFields;
	private $updatedFields;
	protected $forUpdate;
	
	private function getDataInDatabaseFormat($field) {
		if (in_array($field, $this->stringFields)) {
			return DatabaseObject::getStringInDatabaseFormat($this->databaseConnection, $this->fieldToData[$field]);
		} else {
			return $this->fieldToData[$field];
		}
	}
	
	protected static function getStringInDatabaseFormat($databaseConnection, $string) {
		return "'" . $databaseConnection->escapeString($string) . "'";
	}

	public function __construct($databaseConnection, $table, $id, $updateableFields, $stringFields, $forUpdate = false, $fieldToData = null) {
		$this->databaseConnection = $databaseConnection;
		$this->table = $table;
		$this->id = $id;
		if (in_array($this->id, $stringFields)) {
			$this->id = "'" . $databaseConnection->escapeString($this->id) . "'";
		}
		$this->updateableFields = $updateableFields;
		$this->updatedFields = array();
		$this->forUpdate = $forUpdate;
		$this->stringFields = $stringFields;

		if (empty($fieldToData)) {
			$this->fieldToData = $databaseConnection->fetchRow($databaseConnection->selectAndLock("* FROM $this->table WHERE id = $this->id"), $this->forUpdate);
			
			if ($this->fieldToData === FALSE) {
				throw new NonexistentDatabaseEntryException("Nonexistent entry: table: $table, id: $id");
			}
		} else {
			$this->fieldToData = $fieldToData;
		}
		
		foreach ($this->updateableFields as $updateableField) {
			if (!array_key_exists($updateableField, $this->fieldToData)) {
				throw new NonexistentDatabaseFieldException();
			}
		}
	}
	
	public function __set($name, $value) {
		if (!array_key_exists($name, $this->fieldToData)) {
			throw new NonexistentDatabaseFieldException();
		} else if (!$this->forUpdate || !in_array($name, $this->updateableFields)) {
			throw new ReadOnlyDatabaseFieldException();
		}

		$this->fieldToData[$name] = $value;
		if (!in_array($name, $this->updatedFields)) {
			$this->updatedFields[] = $name;
		}
	}
	
	public function __get($name) {
		if (!array_key_exists($name, $this->fieldToData)) {
			throw new NonexistentDatabaseFieldException();
		}

		return $this->fieldToData[$name];
	}
	
	public function __isset($name) {
		return isset($this->fieldToData[$name]);
	}
	
	public function __unset($name) {
		throw new LogicException();
	}
	
	public function delete() {
		if (!$this->forUpdate) {
			throw new LogicException();
		}
		$this->databaseConnection->sendQuery("DELETE FROM $this->table WHERE id = $this->id;");
	}
	
	public function save() {
		if (!$this->forUpdate) {
			throw new LogicException();
		}
		if (empty($this->updatedFields)) {
			return;
		}

		$updates = array();
		foreach ($this->updatedFields as $updatedField) {
			$updates[] = $updatedField . " = " . $this->getDataInDatabaseFormat($updatedField);
		}

		$this->databaseConnection->sendQuery("UPDATE $this->table SET " . implode(", ", $updates) . " WHERE id = $this->id;");
	}
	
	public static function getInsertStatement($databaseConnection, $table, $fieldToData, $stringFields) {
		foreach($fieldToData as $field => $data) {
			if (in_array($field, $stringFields)) {
				$fieldToData[$field] = DatabaseObject::getStringInDatabaseFormat($databaseConnection, $data);
			}
		}

		return "INSERT INTO $table (" . implode(", ", array_keys($fieldToData)) . ") VALUES (" . implode(", ", array_values($fieldToData)) . ");";
	}
}

class Forum extends DatabaseObject {
	private $numberOfTopics;
	private $numberOfPosts;
	private $numberOfPages;
	private $pageToTopics;
	private $lastPost;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'forums', $id, array('name', 'description'), array('name', 'description'), $forUpdate, $fieldToData);
		$this->pageToTopics = array();
	}
	
	public static function create($databaseConnection, $name, $description, $forUpdate = false) {
		$fieldToData = array(
			'name' => $name,
			'description' => $description,
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement($databaseConnection, 'forums', $fieldToData, array('name', 'description')));
		return new Forum($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getNumberOfTopics() {
		if (!isset($this->numberOfTopics)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM topics WHERE forum_id = $this->id"));
			$this->numberOfTopics = $res[0];
		}
		
		return $this->numberOfTopics;
	}
	
	public function getNumberOfPosts() {
		if (!isset($this->numberOfPosts)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE forum_id = $this->id"));
			$this->numberOfPosts = $res[0];
		}
		
		return $this->numberOfPosts;
	}
	
	public function getNumberOfPages($topicsPerPage) {
		if (!isset($this->numberOfPages)) {
			$numberOfTopicsRow = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM topics WHERE forum_id = $this->id"));
			$this->numberOfPages = ceil($numberOfTopicsRow[0] / ((float) $topicsPerPage)); 
		}
		
		return max($this->numberOfPages, 1);
	}
	
	public function getPageLink($page) {
		return htmlentities("viewforum.php?forumid=$this->id&page=$page");
	}
	
	public function getTopicsOnPage($page, $topicsPerPage, $forUpdate = false) {
		if (!isset($this->pageToTopics[$page])) {
			$this->pageToTopics[$page] = array();
			$topicsRows = $this->databaseConnection->selectAndLock("* FROM topics WHERE forum_id = $this->id ORDER BY last_updated DESC, id DESC LIMIT $topicsPerPage OFFSET " . ($page - 1) * $topicsPerPage);
			while ($topicRow = $this->databaseConnection->fetchRow($topicsRows)) {
				$this->pageToTopics[$page][] = new Topic($this->databaseConnection, $topicRow['id'], $forUpdate, $topicRow);
			}
			if (empty($this->pageToTopics[$page]) && $page != 1) {
				throw new NonexistentDatabaseEntryException();
			}
		}
		
		return $this->pageToTopics[$page];
	}
	
	public function getLastPost() {
		if (!isset($this->lastPost)) {
			$lastPostRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM posts WHERE forum_id = $this->id ORDER BY posted_time DESC, id DESC LIMIT 1"));
			if ($lastPostRow === FALSE) {
				$this->lastPost = FALSE;
			} else {
				$this->lastPost = new Post($this->databaseConnection, $lastPostRow['id']);
			}
		}
		
		return $this->lastPost;
	}
	
	public function getLink() {
		return "viewforum.php?forumid=$this->id";
	}
}

class Topic extends DatabaseObject {
	private $numberOfPosts;
	private $numberOfPages;
	private $lastPost;
	private $pageToPosts;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'topics', $id, array('title', 'last_updated', 'views_count'), array('title'), $forUpdate, $fieldToData);
		$this->pageToPosts = array();
	}
	
	public static function create($databaseConnection, $forum, $poster, $title, $forUpdate = false) {
		$fieldToData = array(
			'forum_id' => $forum->id,
			'poster_id' => $poster->id,
			'title' => $title,
			'last_updated' => "now()",
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement($databaseConnection, 'topics', $fieldToData, array('title')));
		return new Topic($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getForum($forUpdate = false) {
		return new Forum($this->databaseConnection, $this->forum_id, $forUpdate);
	}
	
	public function getNumberOfPosts() {
		if (!isset($this->numberOfPosts)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE topic_id = $this->id"));
			$this->numberOfPosts = $res[0];
		}
		
		return $this->numberOfPosts;
	}
	
	public function getNumberOfPages($postsPerPage) {
		if (!isset($this->numberOfPages)) {
			$numberOfPostsRow = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE topic_id = $this->id"));
			$this->numberOfPages = ceil($numberOfPostsRow[0] / ((float) $postsPerPage)); 
		}
		
		return $this->numberOfPages;
	}
	
	public function getPageLink($page) {
		return htmlentities("viewtopic.php?topicid=$this->id&page=$page");
	}
	
	public function getPostsOnPage($page, $postsPerPage, $forUpdate = false) {
		if (!isset($this->pageToPosts[$page])) {
			$this->pageToPosts[$page] = array();
			$postsRows = $this->databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->id ORDER BY posted_time, id LIMIT $postsPerPage OFFSET " . ($page - 1) * $postsPerPage);
			while ($postRow = $this->databaseConnection->fetchRow($postsRows)) {
				$this->pageToPosts[$page][] = new Post($this->databaseConnection, $postRow['id'], $forUpdate, $postRow);
			}
			if (empty($this->pageToPosts[$page])) {
				throw new NonexistentDatabaseEntryException();
			}
		}
		
		return $this->pageToPosts[$page];
	}
	
	public function getLastPost() {
		if (!isset($this->lastPost)) {
			$lastPostRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->id ORDER BY posted_time DESC, id DESC LIMIT 1"));
			if ($lastPostRow === FALSE) {
				$this->lastPost = FALSE;
			} else {
				$this->lastPost = new Post($this->databaseConnection, $lastPostRow['id']);
			}
		}
		
		return $this->lastPost;
	}
	
	public function getPostAfter($postid, $date, $forUpdate = false) {
		$newerPostRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->id AND (posted_time > '$date' OR (posted_time = '$date' AND id > $postid)) ORDER BY posted_time, id LIMIT 1"));
		if ($newerPostRow === FALSE) {
			return FALSE;
		} else {
			return new Post($this->databaseConnection, $newerPostRow['id'], $forUpdate);
		}
	}
	
	public function getLink() {
		return "viewtopic.php?topicid=$this->id";
	}
}

class Post extends DatabaseObject {
	private $isFirst;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'posts', $id, array('content', 'last_updated_time', 'is_textile'), array('content'), $forUpdate, $fieldToData);
	}
	
	public static function create($databaseConnection, $topic, $poster, $content, $is_textile, $forUpdate = false) {
		$forum = new Forum($databaseConnection, $topic->forum_id);
		$fieldToData = array(
			'forum_id' => $forum->id,
			'topic_id' => $topic->id,
			'poster_id' => $poster->id,
			'content' => $content,
			'is_textile' => $is_textile,
			'posted_time' => "now()",
			'last_updated_time' => "now()",
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement($databaseConnection, 'posts', $fieldToData, array('content')));
		return new Post($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getForum($forUpdate = false) {
		return $this->getTopic()->getForum($forUpdate);
	}
	
	public function getTopic($forUpdate = false) {
		return new Topic($this->databaseConnection, $this->topic_id, $forUpdate);
	}
	
	public function getPoster($forUpdate = false) {
		return new User($this->databaseConnection, $this->poster_id, $forUpdate);
	}
	
	public function isFirstInTheTopic() {
		if (!isset($this->isFirst)) {
			$firstRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->topic_id ORDER BY posted_time, id LIMIT 1"));
			$this->isFirst = $firstRow['id'] == $this->id;
		}
		
		return $this->isFirst;
	}
	
	public function getLink($postsPerPage) {
		$postsRows = $this->databaseConnection->selectAndLock("id FROM posts WHERE topic_id = $this->topic_id ORDER BY posted_time, id");
		$postNumber = 0;
		while ($postRow = $this->databaseConnection->fetchRow($postsRows)) {
			$postNumber++;
			if ($postRow['id'] == $this->id) {
				break;
			}
		}
		
		$page = ceil($postNumber / (float) $postsPerPage);
		
		return htmlentities("viewtopic.php?topicid=$this->topic_id&page=$page#post$this->id");
	}
}

class User extends DatabaseObject {
	const USER_TYPE = 1;
	const MODERATOR_TYPE = 2;
	const ADMIN_TYPE = 3;
	
	private $numberOfTopicsStarted;
	private $numberOfPosts;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'users', $id, array('password', 'email', 'type', 'location', 'language', 'style'), array('password', 'email', 'location'), $forUpdate, $fieldToData);
	}
	
	public static function getUser($databaseConnection, $name, $forUpdate = false) {
		$userRow = $databaseConnection->fetchRow($databaseConnection->selectAndLock("* FROM users WHERE name = " . DatabaseObject::getStringInDatabaseFormat($databaseConnection, $name)));
		if ($userRow === FALSE) {
			return FALSE;
		} else {
			return new User($databaseConnection, $userRow['id'], $forUpdate, $userRow);
		}
	}
	
	public function hasModaratingAccessTo($post) {
		$moderatorTableRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM moderators_rights WHERE user_id = $this->id AND forum_id = $post->forum_id"));
		return $this->type == User::ADMIN_TYPE || $post->poster_id == $this->id || ($this->type == User::MODERATOR_TYPE && $moderatorTableRow !== FALSE);
	}
	
	public static function create($databaseConnection, $name, $email, $password, $type, $language, $style, $forUpdate = false) {
		$fieldToData = array(
			'name' => $name,
			'email' => $email,
			'password' => $password,
			'type' => $type,
			'language' => $language,
			'style' => $style,
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement($databaseConnection, 'users', $fieldToData, array('name', 'email', 'password')));
		return new User($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function assignModeratorAccessTo($forumid) {
		$moderatedForumsRows = $this->databaseConnection->selectAndLock("forum_id FROM moderators_rights WHERE user_id = $this->id");
		$moderatedForums = array();
		while ($moderatedForumsRow = $this->databaseConnection->fetchRow($moderatedForumsRows)) {
			$moderatedForums[] = (int) $moderatedForumsRow['forum_id'];
		}
		if ($forumid == -1) {
			$forums = getForums($this->databaseConnection);
			foreach ($forums as $currentForum) {
				if (!in_array($currentForum->id, $moderatedForums)) {
					$this->databaseConnection->sendQuery(DatabaseObject::getInsertStatement($this->databaseConnection, 'moderators_rights', array('user_id' => $this->id, 'forum_id' => $currentForum->id), array()));
				}
			}
		} else {
			if (!in_array($forumid, $moderatedForums)) {
				$this->databaseConnection->sendQuery(DatabaseObject::getInsertStatement($this->databaseConnection, 'moderators_rights', array('user_id' => $this->id, 'forum_id' => $forumid), array()));
			}
		}
	}
	
	public function revokeModeratorRights() {
		$this->databaseConnection->sendQuery("DELETE FROM moderators_rights WHERE user_id = $this->id;");
	}
	
	public function getNumberOfTopicsStarted() {
		if (!isset($this->numberOfTopicsStarted)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM topics WHERE poster_id = $this->id"));
			$this->numberOfTopicsStarted = $res[0];
		}
		
		return $this->numberOfTopicsStarted;
	}
	
	public function getNumberOfPosts() {
		if (!isset($this->numberOfPosts)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE poster_id = $this->id"));
			$this->numberOfPosts = $res[0];
		}
		
		return $this->numberOfPosts;
	}
	
	public function getLink() {
		return "viewprofile.php?userid=$this->id";
	}
}

class InvalidRequestSyntaxException extends Exception {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}
?>
