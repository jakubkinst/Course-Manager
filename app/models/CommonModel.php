<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommonModel
 *
 * @author JerRy
 */
class CommonModel extends Object {

    public static function convertFormDate($formDate) {
	$phpdate = strtotime($formDate);
	return date('Y-m-d H:i:s', $phpdate);
    }

    public static function array_push_assoc($array, $key, $value) {
	$array[$key] = $value;
	return $array;
    }

}

?>
