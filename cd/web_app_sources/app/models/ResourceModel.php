<?php

/**
 * Model class with static methods dedicated to Resources
 *
 * Responsible mostly for communication with database via dibi and handling with files
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class ResourceModel extends Object {

	/**
	 * @var string Upload directory (relative to a base directory)
	 */
	public static $UPLOAD_DIR = "uploads";

	/**
	 * Returns a needed resource
	 * @param int $rid Resource ID
	 * @return array
	 */
	public static function getResource($rid) {
		$file = dibi::fetch('SELECT * FROM resource WHERE id=%i', $rid);
		return $file;
	}

	/**
	 * Deletes a resource
	 * @param int $rid Resource ID
	 */
	public static function deleteResource($rid) {
		$filename = dibi::fetchSingle('SELECT filename FROM resource WHERE id=%i', $rid);
		unlink(WWW_DIR . '/../uploads/' . $filename);
		dibi::query('DELETE FROM resource WHERE id=%i', $rid);
	}

	/**
	 * Returns all resources of a given course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getResources($cid) {
		$files = dibi::fetchAll('SELECT * FROM resource WHERE Course_id=%i', $cid);

		return $files;
	}

	/**
	 * Returns resources attached to a lesson
	 * @param int $lid Lesson ID
	 * @return array
	 */
	public static function getLessonFiles($lid) {
		$files = dibi::fetchAll('SELECT * FROM resource WHERE Lesson_id=%i', $lid);

		return $files;
	}

	/**
	 * Returns course id of a course hosting given resource
	 * @param int $rid Resource ID
	 * @return int
	 */
	public static function getCourseIDByResourceID($rid) {
		$file = dibi::fetchSingle('SELECT Course_id FROM resource WHERE id=%i', $rid);
		return $file;
	}

}

?>
