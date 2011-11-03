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
    public static function getTeacherCourses($user) {
        $id = UserModel::getUserID($user);
        $courses = dibi::fetchAll('SELECT * FROM (course JOIN teacher ON Course_id=id) WHERE User_id=%i', $id);
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
    public static function getStudentCourses($user) {
        $id = UserModel::getUserID($user);
        $courses = dibi::fetchAll('SELECT * FROM (course JOIN student ON Course_id=id) WHERE User_id=%i', $id);
        // adds list of teachers to course objects
        foreach ($courses as $course) {
            $course['lectors'] = CourseModel::getLectors($course['id']);
        }
        return $courses;
    }

}

?>
