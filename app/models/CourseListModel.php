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
    public static function getCourses(){
        return dibi::fetchAll('SELECT * FROM course');
    }
}

?>
