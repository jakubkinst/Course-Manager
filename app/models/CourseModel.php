<?php

/**
 * CourseModel
 *
 * @author Jakub Kinst
 */
class CourseModel extends Object {

    /**
     * Adds Course to DB and connects this Course with actual user as teacher.
     * @param type $user
     * @param type $values 
     */
    public static function addCourse($user, $values) {
	$array = array(
	    'name' => $values['name'],
	    'description' => $values['description']
	);
	dibi::query('INSERT INTO course', $array);
	$course_id = dibi::getInsertId();
	$values2['User_id'] = UserModel::getUserID($user);
	$values2['Course_id'] = $course_id;
	
	dibi::query('INSERT INTO teacher', $values2);
    }
    
    public static function editCourse($cid, $values) {	
	$array = array(
	    'name' => $values['name'],
	    'description' => $values['description']
	);
	return dibi::query('UPDATE course SET', $array, 'WHERE id=%i', $cid);
    }
    
    public static function deleteCourse($cid) {	
	return dibi::query('DELETE FROM course WHERE id=%i', $cid);
    }

    /**
     * Adds lesson to DB by $values
     * @param type $values 
     */
    public static function addLesson($values, $cid) {
	$array = array(
	    'topic' => $values['topic'],
	    'description' => $values['description'],
	    'Course_id' =>$cid,
	    'date' => CommonModel::convertFormDate($values['date'])
	);
	dibi::query('INSERT INTO lesson', $array);
    }

    public static function editLesson($lid, $values) {	
	$array = array(
	    'topic' => $values['topic'],
	    'description' => $values['description']
	);
	dibi::query('UPDATE lesson SET', $array, 'WHERE id=%i', $lid);
    }

    public static function deleteLesson($lid) {
	dibi::query('DELETE FROM lesson WHERE id=%i', $lid);
	return true;
    }

    /**
     * Adds lesson to DB by $values
     * @param type $values 
     */
    public static function addComment($values,$lid) {	
	$values['added'] = new DateTime;
	$values['user_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
	$values['Lesson_id'] = $lid;
	return dibi::query('INSERT INTO comment', $values);
    }

    /**
     * Returns Course info in array according to the $id
     * @param type $id
     * @return type 
     */
    public static function getCourseByID($id) {
	return dibi::fetch('SELECT * FROM course WHERE id=%i', $id);
    }

    /**
     * Security check - returns true if $user is registered as teacher for course. False otherwise.
     * @param type $user
     * @param type $courseid
     * @return boolean 
     */
    public static function isTeacher($uid, $courseid) {
	$approved = false;
	foreach (CourseListModel::getTeacherCourses($uid) as $course) {
	    if ($course['id'] == $courseid)
		$approved = true;
	}
	return $approved;
    }

    /**
     * Security check - returns true if $user is registered as student in course. False otherwise.
     * @param type $user
     * @param type $courseid
     * @return boolean 
     */
    public static function isStudent($uid, $courseid) {
	$approved = false;
	foreach (CourseListModel::getStudentCourses($uid) as $course) {
	    if ($course['id'] == $courseid)
		$approved = true;
	}
	return $approved;
    }

    /**
     * Returns list of teachers connected with course
     * @param type $cid
     * @return type 
     */
    public static function getLectors($cid) {
	return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN teacher ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id', $cid);
    }

    /**
     * Returns list of students connected with course
     * @param type $cid
     * @return type 
     */
    public static function getStudents($cid) {
	return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN student ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id', $cid);
    }

    /**
     * Returns list of lessons of specific course
     * @param type $courseID
     * @return type 
     */
    public static function getLessons($courseID) {
	return dibi::fetchAll('SELECT * FROM lesson WHERE Course_id=%i ORDER BY date DESC', $courseID);
    }

    /**
     * Returns Course id to specific lesson
     * @param type $lid
     * @return type 
     */
    public static function getCourseIDByLessonID($lid) {
	return dibi::fetchSingle('SELECT Course_id FROM lesson WHERE id=%i', $lid);
    }

    /**
     * Returns lesson info according to lesson id
     * @param type $lid
     * @return type 
     */
    public static function getLessonByID($lid) {
	return dibi::fetch('SELECT * FROM lesson WHERE id=%i', $lid);
    }

    /**
     * Returns list of comments for specific lesson
     * @param type $lid
     * @return type 
     */
    public static function getComments($lid, $offset, $limit) {
	$comments = dibi::fetchAll('SELECT * FROM comment WHERE lesson_id=%i ORDER BY added DESC LIMIT %i OFFSET %i ', $lid, $limit, $offset);
	foreach ($comments as $comment) {
	    $comment['user'] = UserModel::getUser($comment['User_id']);
	}
	return $comments;
    }
    
    public static function countComments($lid) {
	return dibi::fetchSingle('SELECT COUNT(*) FROM comment WHERE Lesson_id=%i',$lid);
    }

    /**
     * Adds student to a course
     * @param type $values 
     */
    public static function inviteStudent($values) {
	$values2['email'] = $values['email'];
	$values2['Course_id'] = $values['Course_id'];
	$values2['invitedBy'] = UserModel::getLoggedUser()->id;
	$uid = UserModel::getUserIDByEmail($values2['email']);
	if (self::isStudent($uid, $values2['Course_id']) || self::isTeacher($uid, $values2['Course_id']) || CourseListModel::isInvited($values2['email'], $values2['Course_id']))
	    return false;
	dibi::begin();
	$result = dibi::query('INSERT INTO invite', $values2);
	MailModel::sendInvite($values2['email'], $values2['Course_id'], $values2['invitedBy']);
	dibi::commit();
	return $result;
    }

}

?>
