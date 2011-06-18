<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CourseListModel
 *
 * @author JerRy
 */
class CourseListModel extends Object {
    public static function getTeacherCourses($user){
        $id = UserModel::getUserID($user);
        return dibi::fetchAll('SELECT * FROM (course JOIN teacher ON Course_id=id) WHERE User_id=%i',$id);
    }
    public static function getStudentCourses($user){
        $id = UserModel::getUserID($user);
        return dibi::fetchAll('SELECT * FROM (course JOIN student ON Course_id=id) WHERE User_id=%i',$id);
    }
    
}

?>
