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
    public static function addCourse($values){
        dibi::query('INSERT INTO course', $values);
    }
}

?>
