<?php

/**
 * Model class with static methods dedicated to Course-related db methods
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class CourseModel extends Object {

	/**
	 * Adds Course to DB and connects this Course with actual user as teacher.
	 * @param type $values
	 * @return boolean
	 */
	public static function addCourse($values) {
		$array = array(
			'name' => $values['name'],
			'description' => $values['description']
		);
		dibi::begin();
		dibi::query('INSERT INTO course', $array);
		$course_id = dibi::getInsertId();
		$values2['User_id'] = UserModel::getLoggedUser()->id;
		$values2['Course_id'] = $course_id;
		dibi::query('INSERT INTO teacher', $values2);
		return dibi::commit();
	}

	public static function leaveCourse($cid) {
		$uid = UserModel::getLoggedUser()->id;
		return dibi::query('DELETE FROM student WHERE User_id=%i AND Course_id=%i', $uid, $cid);
	}

	/**
	 * Promotes student to a teacher role
	 * @param int $cid Course ID
	 * @param int $uid User ID
	 * @return boolean
	 */
	public static function makeTeacher($cid, $uid) {
		dibi::begin();
		$array = array('User_id' => $uid, 'Course_id' => $cid);
		dibi::query('INSERT INTO teacher', $array);
		dibi::query('DELETE FROM student WHERE User_id=%i AND Course_id=%i', $uid, $cid);
		return dibi::commit();
	}

	/**
	 * Edits course in database
	 * @param int $cid Course ID
	 * @param array $values
	 * @return boolean
	 */
	public static function editCourse($cid, $values) {
		$array = array(
			'name' => $values['name'],
			'description' => $values['description']
		);
		return dibi::query('UPDATE course SET', $array, 'WHERE id=%i', $cid);
	}

	/**
	 * Deletes course from db
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function deleteCourse($cid) {
		return dibi::query('DELETE FROM course WHERE id=%i', $cid);
	}

	/**
	 * Adds lesson to a given course
	 * @param array $values
	 * @param int $cid Course ID
	 */
	public static function addLesson($values, $cid) {
		$array = array(
			'topic' => $values['topic'],
			'description' => $values['description'],
			'Course_id' => $cid,
			'date' => CommonModel::convertFormDate($values['date'])
		);
		dibi::query('INSERT INTO lesson', $array);
	}

	/**
	 * Edits lesson in a db
	 * @param int $lid Lesson ID
	 * @param values $values
	 * @return boolean
	 */
	public static function editLesson($lid, $values) {
		$array = array(
			'topic' => $values['topic'],
			'description' => $values['description']
		);
		return dibi::query('UPDATE lesson SET', $array, 'WHERE id=%i', $lid);
	}

	/**
	 * Deletes lesson from a db
	 * @param int $lid Lesson ID
	 * @return boolean
	 */
	public static function deleteLesson($lid) {
		dibi::query('DELETE FROM lesson WHERE id=%i', $lid);
		return true;
	}

	/**
	 * Adds comment to a lesson
	 * @param array $values
	 * @param int $lid Lesson ID
	 * @return boolean
	 */
	public static function addComment($values, $lid) {
		$values['added'] = new DateTime;
		$values['user_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
		$values['Lesson_id'] = $lid;
		return dibi::query('INSERT INTO comment', $values);
	}

	/**
	 * Returns Course info in array according to the $id
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getCourse($cid) {
		return dibi::fetch('SELECT * FROM course WHERE id=%i', $cid);
	}

	/**
	 * Security check - returns true if user is registered as teacher for course. False otherwise.
	 * @param int $uid User ID
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function isTeacher($uid, $cid) {
		$approved = false;
		foreach (CourseListModel::getTeacherCourses($uid) as $course) {
			if ($course['id'] == $cid)
				$approved = true;
		}
		return $approved;
	}

	/**
	 * Security check - returns true if user is registered as student in course. False otherwise.
	 * @param int $uid User ID
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function isStudent($uid, $cid) {
		$approved = false;
		foreach (CourseListModel::getStudentCourses($uid) as $course) {
			if ($course['id'] == $cid)
				$approved = true;
		}
		return $approved;
	}

	/**
	 * Returns list of teachers of a course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getTeachers($cid) {
		return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN teacher ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id', $cid);
	}

	/**
	 * Returns list of students of a course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getStudents($cid) {
		return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN student ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id', $cid);
	}

	/**
	 * Returns list of lessons of specific course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getLessons($cid) {
		return dibi::fetchAll('SELECT * FROM lesson WHERE Course_id=%i ORDER BY date DESC', $cid);
	}

	/**
	 * Returns Course id to specific lesson
	 * @param int $lid Lesson ID
	 * @return int
	 */
	public static function getCourseIDByLessonID($lid) {
		return dibi::fetchSingle('SELECT Course_id FROM lesson WHERE id=%i', $lid);
	}

	/**
	 * Returns lesson info according to lesson id
	 * @param int $lid Lesson ID
	 * @return array
	 */
	public static function getLessonByID($lid) {
		return dibi::fetch('SELECT * FROM lesson WHERE id=%i', $lid);
	}

	/**
	 * Returns list of comments for specific lesson
	 * @param int $lid Lesson ID
	 * @return array
	 */
	public static function getComments($lid, $offset, $limit) {
		$comments = dibi::fetchAll('SELECT * FROM comment WHERE lesson_id=%i ORDER BY added DESC LIMIT %i OFFSET %i ', $lid, $limit, $offset);
		foreach ($comments as $comment) {
			$comment['user'] = UserModel::getUser($comment['User_id']);
		}
		return $comments;
	}

	/**
	 * Counts all comments of a lesson
	 * @param int $lid Lesson ID
	 * @return int
	 */
	public static function countComments($lid) {
		return dibi::fetchSingle('SELECT COUNT(*) FROM comment WHERE Lesson_id=%i', $lid);
	}

	/**
	 * Invite student to a course
	 * @param array $values
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function inviteStudent($values, $cid,$link) {
		$values2['email'] = $values['email'];
		$values2['Course_id'] = $cid;
		$values2['invitedBy'] = UserModel::getLoggedUser()->id;
		$uid = UserModel::getUserIDByEmail($values2['email']);
		if (self::isStudent($uid, $values2['Course_id']) || self::isTeacher($uid, $values2['Course_id']) || CourseListModel::isInvited($values2['email'], $values2['Course_id']))
			return false;
		dibi::begin();
		$result = dibi::query('INSERT INTO invite', $values2);
		MailModel::sendInvite($values2['email'], $values2['Course_id'], $values2['invitedBy'],$link);
		dibi::commit();
		return $result;
	}

}

?>
