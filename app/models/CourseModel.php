<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CourseModel
 *
 * @author JerRy
 */
class CourseModel extends Object{
    public static function addCourse($user, $values){
        dibi::query('INSERT INTO course', $values);
        $course_id = dibi::getInsertId();
        $values2['User_id'] = UserModel::getUserID($user);
        $values2['Course_id'] = $course_id;
        
        dibi::query('INSERT INTO teacher',$values2);
    }
    
    public static function addLesson($values){
        dibi::query('INSERT INTO lesson', $values);
    }
    public static function addComment($values){
        dibi::query('INSERT INTO comment', $values);
    }
    
    public static function getCourseByID($id){
        return dibi::fetch('SELECT * FROM course WHERE id=%i',$id);
    }
    public static function isTeacher($user,$courseid){
        $approved = false;
        foreach (CourseListModel::getTeacherCourses($user) as $course){
            if ($course['id'] == $courseid)
                $approved=true;
        }
        return $approved;            
    }
    public static function isStudent($user,$courseid){
        $approved = false;
        foreach (CourseListModel::getStudentCourses($user) as $course){
            if ($course['id'] == $courseid)
                $approved=true;
        }
        return $approved;            
    }

    public static function getLectors($cid){        
        return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN teacher ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id',$cid);
    }
    public static function getStudents($cid){        
        return dibi::fetchAll('SELECT * FROM user RIGHT JOIN (SELECT User_id FROM (course JOIN student ON Course_id=id) WHERE Course_id=%i) AS user2 ON user.id=user2.User_id',$cid);
    }
    public static function getLessons($courseID){
        return dibi::fetchAll('SELECT * FROM lesson WHERE Course_id=%i ORDER BY date DESC',$courseID);
    }
    public static function getCourseIDByLessonID($lid){
        return dibi::fetchSingle('SELECT Course_id FROM lesson WHERE id=%i',$lid);
    }
    public static function getLessonByID($lid){
        return dibi::fetch('SELECT * FROM lesson WHERE id=%i',$lid);
    }
    public static function getComments($lid){
        return dibi::fetchAll('SELECT * FROM comment WHERE lesson_id=%i',$lid);
    }
    public static function addStudent($values){
        
        $values2['User_id'] = UserModel::getUserIDByEmail($values['email']);
        $values2['Course_id'] = $values['Course_id'];
        try {
            dibi::query('INSERT INTO student',$values2);
        } 
        catch (Exception $e) {
            
        }
    }
    
}

?>
