<?php

/**
 * Model class with static methods dedicated to Settings module
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class SettingsModel extends Object {

	/**
	 * Returns array of current user settings
	 * @return array
	 */
	public static function getMySettings() {
		$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
		return SettingsModel::getSettings($uid);
	}

	/**
	 * Returns settings of a given user
	 * @param int $uid User ID
	 * @return array
	 */
	public static function getSettings($uid) {
		return dibi::fetch('SELECT * FROM settings WHERE User_id=%i', $uid);
	}

	/**
	 * Updates settings of a current user in db
	 * @param array $values
	 * @return boolean
	 */
	public static function setSettings($values) {
		$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
		return dibi::query('UPDATE `settings` SET ', $values, 'WHERE `User_id`=%i', $uid);
	}

}

?>
