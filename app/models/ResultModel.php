<?php

/**
 * Model class with static methods dedicated to Result module
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class ResultModel extends Object {

	/**
	 * Returns list of offline assignment results for a course (Grade Assignments)
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOfflineGradeAssignmentsResults($cid) {
		$tasks = dibi::fetchAll('SELECT * FROM offlinetask WHERE Course_id=%i AND grade=1', $cid);
		foreach ($tasks as $task) {
			$results = dibi::fetchAll('SELECT user.id AS User_id,points FROM user LEFT JOIN (SELECT * FROM result WHERE OfflineTask_id=%i) as result ON user.id=result.User_id', $task->id);
			foreach ($results as $result) {
				$task[$result->User_id] = $result->points;
			}
		}
		return $tasks;
	}

	/**
	 * Returns list of offline assignment results for a course (Point Assignments)
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOfflinePointAssignmentsResults($cid) {
		$tasks = dibi::fetchAll('SELECT * FROM offlinetask WHERE Course_id=%i AND grade=0', $cid);
		foreach ($tasks as $task) {
			$results = dibi::fetchAll('SELECT user.id AS User_id,points FROM user LEFT JOIN (SELECT * FROM result WHERE OfflineTask_id=%i) as result ON user.id=result.User_id', $task->id);
			foreach ($results as $result) {
				$task[$result->User_id] = $result->points;
			}
		}
		return $tasks;
	}

	/**
	 * Returns list of online assignment results for a course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOnlinePointAssignmentsResults($cid) {
		$tasks = dibi::fetchAll('SELECT * FROM assignment WHERE Course_id=%i', $cid);
		foreach ($tasks as $task) {
			$results = dibi::fetchAll('SELECT User_id,points FROM onlinesubmission WHERE Assignment_id=%i', $task->id);
			foreach ($results as $result) {
				$task[$result->User_id] = $result->points;
			}
		}
		return $tasks;
	}

	/**
	 * Returns the average of all offline assignment results (only grades) for each student
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOfflineGradesAvgs($cid) {
		$result = dibi::fetchPairs('SELECT * FROM (SELECT user.id AS User_id,AVG(points) AS avg FROM user LEFT JOIN (SELECT User_id,name,points FROM result JOIN offlinetask ON result.OfflineTask_id=offlinetask.id WHERE offlinetask.Course_id=%i AND offlinetask.grade=1) as result ON user.id=result.User_id  GROUP BY user.id) AS tbl WHERE avg IS NOT NULL', $cid);
		return $result;
	}

	/**
	 * Returns the sum of all offline assignment results (only points) for each student
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOnlinePointsSums($cid) {
		$result = dibi::fetchPairs('
			SELECT * FROM
				(SELECT user.id AS User_id,SUM(points) AS sum FROM user LEFT JOIN (SELECT User_id,name,points FROM onlinesubmission JOIN assignment ON onlinesubmission.Assignment_id=assignment.id WHERE assignment.Course_id=%i) as result ON user.id=result.User_id  GROUP BY user.id) AS tbl WHERE sum IS NOT NULL', $cid);
		return $result;
	}

	/**
	 * Returns the sum of all offline assignment results (only points) for each student
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getOfflinePointsSums($cid) {
		$result = dibi::fetchPairs('SELECT * FROM (SELECT user.id AS User_id,SUM(points) AS sum FROM user LEFT JOIN (SELECT User_id,name,points FROM result JOIN offlinetask ON result.OfflineTask_id=offlinetask.id WHERE offlinetask.Course_id=%i AND offlinetask.grade=0) as result ON user.id=result.User_id  GROUP BY user.id) AS tbl WHERE sum IS NOT NULL', $cid);
		return $result;
	}

	/**
	 * Adds result to db and connects it with course
	 * Creates offline task and for each result creates new result entry
	 *
	 * @param int $cid Course ID
	 * @param array $values
	 */
	public static function addResult($cid, $values) {
		$name = $values['name'];
		unset($values['name']);
		$maxpoints = $values['maxpoints'];
		unset($values['maxpoints']);
		$grade = $values['grade'];
		unset($values['grade']);

		try {
			dibi::query('INSERT INTO offlinetask', array(
				'name' => $name,
				'maxpoints' => $maxpoints,
				'grade' => $grade,
				'Course_id' => $cid
			));
			$offlineTaskID = dibi::getInsertId();
			foreach ($values as $k => $v) {
				dibi::query('INSERT INTO result', array(
					'points' => $v,
					'OfflineTask_id' => $offlineTaskID,
					'User_id' => $k
				));
			}
		} catch (Exception $e) {

		}
	}

}

?>
