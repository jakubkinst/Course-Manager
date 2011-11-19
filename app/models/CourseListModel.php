<?php

/**
 * CourseListModel
 * Model for ist of courses
 *
 *
 * @author Jakub Kinst
 * 
 */
class CourseListModel extends Object {

    /**
     * Returns list of courses teachered by $user
     * @param type $user
     * @return type Array
     */
    public static function getTeacherCourses($uid) {
	$courses = dibi::fetchAll('SELECT * FROM (course JOIN teacher ON Course_id=id) WHERE User_id=%i', $uid);
	// adds list of teachers to course objects
	foreach ($courses as $course) {
	    $course['lectors'] = CourseModel::getLectors($course['id']);
	}
	return $courses;
    }

    /**
     *  Returns list of courses where $user is listed as student
     * @param type $user
     * @return type Array
     */
    public static function getStudentCourses($uid) {
	$courses = dibi::fetchAll('SELECT * FROM (course JOIN student ON Course_id=id) WHERE User_id=%i', $uid);
	// adds list of teachers to course objects
	foreach ($courses as $course) {
	    $course['lectors'] = CourseModel::getLectors($course['id']);
	}
	return $courses;
    }

    public static function getMyInvites() {
	$email = UserModel::getLoggedUser()->email;
	$results = dibi::fetchAll('SELECT * FROM invite WHERE email=%s', $email);
	foreach ($results as $result) {
	    $result['invitedBy'] = UserModel::getUser($result['invitedBy']);
	    $result['course'] = CourseModel::getCourseByID($result['Course_id']);
	}
	return $results;
    }
    
    public static function getInvites($cid) {
	$results = dibi::fetchAll('SELECT * FROM invite WHERE Course_id=%i', $cid);
	foreach ($results as $result) {
	    $result['invitedBy'] = UserModel::getUser($result['invitedBy']);
	    $result['course'] = CourseModel::getCourseByID($result['Course_id']);
	}
	return $results;
    }

    public static function getInvite($iid) {
	$result = dibi::fetch('SELECT * FROM invite WHERE id=%i', $iid);
	$result['invitedBy'] = UserModel::getUser($result['invitedBy']);
	$result['user'] = userModel::getUser(UserModel::getUserIDByEmail($result['email']));
	$result['course'] = CourseModel::getCourseByID($result['Course_id']);
	return $result;
    }

    public static function acceptInvite($iid) {
	$actUser = UserModel::getLoggedUser();
	$invite = self::getInvite($iid);
	if ($actUser->email != $invite->email)
	    return false;
	dibi::begin();
	dibi::query('DELETE FROM invite WHERE id=%i', $iid);
	$result = dibi::query('INSERT INTO student', array('User_id' => $invite['user']->id, 'Course_id' => $invite['course']->id));
	dibi::commit();
	return $result;
    }

    public static function declineInvite($iid) {
	$actUser = UserModel::getLoggedUser();
	$invite = self::getInvite($iid);
	if ($actUser->email != $invite->email)
	    return false;
	return dibi::query('DELETE FROM invite WHERE id=%i', $iid);
    }
    public static function isInvited($email,$cid){
	return dibi::fetch('SELECT * FROM invite WHERE email=%s AND Course_id=%i',$email,$cid)!=null;
    }

}

?>
