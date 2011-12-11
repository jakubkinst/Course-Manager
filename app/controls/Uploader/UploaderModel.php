<?php

/**
 * Nette Control Model used for uploading files. Allows user to upload multiple files
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Controls/Models
 */
class UploaderModel extends Object {

    /**
     * @var string Upload directory (relative to base directory)
     */
    public static $UPLOAD_DIR = "uploads";

    /**
     * Uploads single file
     * @param array $values Values of a single file input
     * @param int $cid Course ID
     * @param DateTime $added
     * @param int $lid Lesson ID (may be null)
     * @return boolean 
     */
    public static function uploadFile($values, $cid, $added, $lid = null) {
	// check if lesson id corresponds to course id
	if ($lid != null && CourseModel::getCourseIDByLessonID($lid) != $cid)
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

    /**
     * Uploads list of files
     * @param array $values Values from form
     * @param int $lid Lesson ID (may be null)
     */
    public static function uploadFiles($values, $lid = null) {

	$added = new DateTime;
	$cid = $values['Course_id'];
	unset($values['Course_id']);
	foreach ($values as $value) {
	    self::uploadFile($value, $cid, $added, $lid);
	}
    }

}

?>
