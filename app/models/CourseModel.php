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
        dibi::query('INSERT INTO course', $values);
        $course_id = dibi::getInsertId();
        $values2['User_id'] = UserModel::getUserID($user);
        $values2['Course_id'] = $course_id;

        dibi::query('INSERT INTO teacher', $values2);
    }

    /**
     * Adds lesson to DB by $values
     * @param type $values 
     */
    public static function addLesson($values) {
        dibi::query('INSERT INTO lesson', $values);
    }

    /**
     * Adds lesson to DB by $values
     * @param type $values 
     */
    public static function addComment($values) {
        dibi::query('INSERT INTO comment', $values);
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
    public static function isTeacher($user, $courseid) {
        $approved = false;
        foreach (CourseListModel::getTeacherCourses($user) as $course) {
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
    public static function isStudent($user, $courseid) {
        $approved = false;
        foreach (CourseListModel::getStudentCourses($user) as $course) {
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
    public static function getComments($lid) {
        $comments = dibi::fetchAll('SELECT * FROM comment WHERE lesson_id=%i', $lid);
        foreach ($comments as $comment) {
            $comment['user'] = UserModel::getUser($comment['User_id']);
        }
        return $comments;
    }

    /**
     * Adds student to a course
     * @param type $values 
     */
    public static function addStudent($values) {

        $values2['User_id'] = UserModel::getUserIDByEmail($values['email']);
        $values2['Course_id'] = $values['Course_id'];
        try {
            dibi::query('INSERT INTO student', $values2);
        } catch (Exception $e) {
            
        }
    }

}

?>
