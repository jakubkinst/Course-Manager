<?php

/**
 * Description of SettingsModel
 *
 * @author JerRy
 */
class SettingsModel extends Object {
    public static function getMySettings() {
        $uid = UserModel::getUserID(Environment::getUser()->getIdentity());
        return SettingsModel::getSettings($uid);
    }
    public static function getSettings($uid) {
        return dibi::fetch('SELECT * FROM settings WHERE User_id=%i', $uid);
    }
    public static function setSettings($values) {
        $uid = UserModel::getUserID(Environment::getUser()->getIdentity());        
        return dibi::query('UPDATE `settings` SET ', $values, 'WHERE `User_id`=%i', $uid);
    }
}

?>
