<?php

/**
 * Model class with static methods dedicated to Event module
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class EventModel extends Object {

	/**
	 * Adds an event to db
	 * @param array $values
	 * @param int $cid Course ID
	 * @return boolean
	 */
	public static function addEvent($values, $cid) {
		$values['added'] = new DateTime;
		$values['Course_id'] = $cid;
		$values['date'] = CommonModel::convertFormDate($values['date']);
		return dibi::query('INSERT INTO event', $values);
	}

	/**
	 * Edits existing event in db
	 * @param int $eid Event ID
	 * @param array $values
	 * @return boolean
	 */
	public static function editEvent($eid, $values) {
		$array = array(
			'name' => $values['name'],
			'date' => CommonModel::convertFormDate($values['date']),
			'description' => $values['description'],
		);
		return dibi::query('UPDATE event SET', $array, 'WHERE id=%i', $eid);
	}

	/**
	 * Deletes an event from db
	 * @param int $eid Event ID
	 * @return boolean
	 */
	public static function deleteEvent($eid) {
		return dibi::query('DELETE FROM event WHERE id=%i', $eid);
	}

	/**
	 * Returns array of all events of a given course
	 * @param int $cid Course ID
	 * @return array
	 */
	public static function getEvents($cid) {
		$events = dibi::fetchAll('SELECT * FROM event WHERE Course_id=%i ORDER BY date ASC', $cid);
		return $events;
	}

	/**
	 * Returns array of events between today and today + $days
	 * @param int $cid Course id
	 * @param int $days Days
	 * @return array
	 */
	public static function getUpcomingEvents($cid, $days) {
		$now = date("Y-m-d");
		$limit = date("Y-m-d", strtotime(date("Y-m-d", strtotime($now)) . " +$days days"));
		return dibi::fetchAll('
			SELECT * FROM event WHERE Course_id=%i AND date>=%d AND date<=%d ORDER BY date ASC
		', $cid, $now, $limit);
	}

	/**
	 * Returns single event
	 * @param int $eid Event ID
	 * @return array
	 */
	public static function getEvent($eid) {
		$topic = dibi::fetch('SELECT * FROM event WHERE id=%i', $eid);
		return $topic;
	}

	/**
	 * Returns course id of a course hosting given event
	 * @param int $eid Event ID
	 * @return int
	 */
	public static function getCourseIDByEventID($eid) {
		return dibi::fetchSingle('SELECT Course_id FROM event WHERE id=%i', $eid);
	}

}

?>
