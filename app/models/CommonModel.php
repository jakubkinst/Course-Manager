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
class CommonModel extends Object{
    public static function convertFormDate($formDate) {	
        $phpdate = strtotime( $formDate );
        return date( 'Y-m-d', $phpdate );
    }
}

?>
