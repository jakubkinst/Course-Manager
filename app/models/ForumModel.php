<?php

/**
 * Model class with static methods dedicated to Forum module
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class ForumModel extends Object {

	/**
	 * Adds a topic into db
	 * @param array $values
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function addTopic($values, $cid) {
		$values['created'] = new DateTime;
		$values['Course_id'] = $cid;
		$values['User_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
		return dibi::query('INSERT INTO topic', $values);
	}

	/**
	 * Adds a reply to a topic into db
	 * @param array $values
	 * @param int $tid Topic ID
	 * @return boolean
	 */
	public static function addReply($values, $tid) {
		$values['created'] = new DateTime;
		$values['Topic_id'] = $tid;
		$values['User_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
		return dibi::query('INSERT INTO reply', $values);
	}

	/**
	 * Returns array of topics hosted by given course
	 * @param int $cid Course ID
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getTopics($cid, $offset, $limit) {
		$topics = dibi::fetchAll('SELECT * FROM topic WHERE Course_id=%i LIMIT %i OFFSET %i', $cid, $limit, $offset);
		foreach ($topics as $topic) {
			$topic['author'] = UserModel::getUser($topic['User_id']);
			$topic['replies'] = ForumModel::countReplies($topic['id']);
			$topic['lastreply'] = ForumModel::getLastReply($topic['id']);
		}

		// sort topics by last reply or created date
		function cmp($a, $b) {
			$aC = $a->lastreply != null ? $a->lastreply->created : $a->created;
			$bC = $b->lastreply != null ? $b->lastreply->created : $b->created;
			if ($aC == $bC) {
				return 0;
			}
			return ($aC < $bC) ? 1 : -1;
		}

		usort($topics, "cmp");

		return $topics;
	}

	/**
	 * returns a topic
	 * @param int $tid Topic ID
	 * @return array
	 */
	public static function getTopic($tid) {
		$topic = dibi::fetch('SELECT * FROM topic WHERE id=%i', $tid);
		$topic['author'] = UserModel::getUser($topic['User_id']);
		return $topic;
	}

	/**
	 * Counts replies to a given topic
	 * @param int $tid Topic ID
	 * @return int
	 */
	public static function countReplies($tid) {
		return dibi::fetchSingle('SELECT COUNT(*) FROM reply WHERE Topic_id=%i', $tid);
	}

	/**
	 * Counts all topics of a course
	 * @param int $cid Topic ID
	 * @return int
	 */
	public static function countTopics($cid) {
		return dibi::fetchSingle('SELECT COUNT(*) FROM topic WHERE Course_id=%i', $cid);
	}

	/**
	 * Returns an array of replies to a given topic
	 * @param int $tid Topic ID
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getReplies($tid, $offset, $limit) {
		$replies = dibi::fetchAll('SELECT * FROM reply WHERE Topic_id=%i ORDER BY created ASC LIMIT %i OFFSET %i ', $tid, $limit, $offset);
		foreach ($replies as $reply) {
			$reply['author'] = UserModel::getUser($reply['User_id']);
		}
		return $replies;
	}

	/**
	 * returns last reply to a given topic
	 * @param int $tid Topic ID
	 * @return array
	 */
	public static function getLastReply($tid) {
		return dibi::fetch('SELECT * FROM reply WHERE Topic_id=%i ORDER BY created DESC LIMIT 1', $tid);
	}

	/**
	 * Returns course id of a course hosting given topic
	 * @param int $tid Topic ID
	 * @return int
	 */
	public static function getCourseIDByTopicID($tid) {
		return dibi::fetchSingle('SELECT Course_id FROM topic WHERE id=%i', $tid);
	}

}

?>
