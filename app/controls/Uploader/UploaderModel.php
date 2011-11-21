<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploaderModel
 *
 * @author JerRy
 */
class UploaderModel extends Object{
    
    public static $UPLOAD_DIR = "uploads";
    
 public static function uploadFile($values,$cid,$added,$lid = null) {
	// check if lesson id corresponds to course id
        if ($lid!=null && CourseModel::getCourseIDByLessonID($lid) != $cid)
            throw new BadRequestException;
        $file = $values;
        $data['size'] = $file->getSize();
	$data['added'] = $added;
	$data['Course_id'] = $cid;
	$data['name'] = $file->getName();
	$data['Lesson_id'] = $lid;
        $hashfilename = md5($_SERVER["REMOTE_ADDR"] . time()) . "_" . $file->getName();
        if ($file->isOK()) {
            if ($file->move(WWW_DIR . "/../" . ResourceModel::$UPLOAD_DIR . "/" . $hashfilename)) {
                $data['filename'] = $hashfilename;
                dibi::query('INSERT INTO resource', $data);
                return true;
            }
        }
        return false;
    }


    public static function uploadFiles($values,$lid = null){
	
        $added = new DateTime;
	$cid = $values['Course_id'];
	unset($values['Course_id']);
	foreach ($values as $value) {
	    self::uploadFile($value,$cid,$added,$lid);
	}
    }
}
?>
