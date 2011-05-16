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
}

?>
