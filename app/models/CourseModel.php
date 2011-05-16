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
    
    public static function getCourseByID($id){
        return dibi::fetch('SELECT * FROM course WHERE id=%i',$id);
    }
    public static function approvedUser($user,$courseid){
        $approved = false;
        foreach (CourseListModel::getCourses($user) as $course){
            if ($course['id'] == $courseid)
                $approved=true;
        }
        return $approved;
            
    }
}

?>
