<?php

/**
 * Description of ResourceModel
 *
 * @author JerRy
 */
class ResourceModel extends Object {

    public static $UPLOAD_DIR = "uploads";

   public static function getResource($rid) {
        $file = dibi::fetch('SELECT * FROM resource WHERE id=%i', $rid); 
        return $file;
    }
    public static function deleteResource($rid) {
	$filename = dibi::fetchSingle('SELECT filename FROM resource WHERE id=%i',$rid);	
	unlink(WWW_DIR.'/../uploads/'.$filename);
        dibi::query('DELETE FROM resource WHERE id=%i',$rid);
    }

    public static function getFiles($cid) {
        $files = dibi::fetchAll('SELECT * FROM resource WHERE Course_id=%i', $cid);
       
        return $files;
    }
    public static function getLessonFiles($lid) {
        $files = dibi::fetchAll('SELECT * FROM resource WHERE Lesson_id=%i', $lid);
       
        return $files;
    }
    
   public static function getCourseIDByResourceID($rid) {
        $file = dibi::fetchSingle('SELECT Course_id FROM resource WHERE id=%i', $rid); 
        return $file;
    }

}

?>
