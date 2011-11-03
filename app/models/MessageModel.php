<?php

/**
 * Description of MessageModel
 *
 * @author JerRy
 */
class MessageModel extends Object {

    public static function sendMessage($values) {
        $values['sent'] = new DateTime;
        $values['from'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        $values['to'] = UserModel::getUserIDByEmail($values['to']);
        //dump($values);
        //return true;
        return dibi::query('INSERT INTO message', $values);
    }

    public static function addReply($values, $tid) {
        $values['created'] = new DateTime;
        $values['Topic_id'] = $tid;
        $values['User_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        return dibi::query('INSERT INTO reply', $values);
    }

    public static function getInbox() {
        $uid = UserModel::getUserID(Environment::getUser()->getIdentity());
        $messages = dibi::fetchAll('SELECT * FROM message WHERE (message.to=%i) ORDER BY sent DESC', $uid);
        foreach ($messages as $message) {
            $message['from'] = UserModel::getUser($message['from']);
        }
        return $messages;
    }
    public static function getOutbox() {
        $uid = UserModel::getUserID(Environment::getUser()->getIdentity());
        $messages = dibi::fetchAll('SELECT * FROM message WHERE (message.from=%i) ORDER BY sent DESC', $uid);
        foreach ($messages as $message) {
            $message['to'] = UserModel::getUser($message['to']);
        }
        return $messages;
    }

    public static function setRead($mid) {
        $arr = array(
            'read' => TRUE,
        );
        return dibi::query('UPDATE `message` SET ', $arr, 'WHERE `id`=%i', $mid);
    }

    public static function getMessage($mid) {
        $message = dibi::fetch('SELECT * FROM message WHERE id=%i', $mid);
        $message['from'] = UserModel::getUser($message['from']);
        $message['to'] = UserModel::getUser($message['to']);
        return $message;
    }

    public static function countUnread() {
        $uid = UserModel::getUserID(Environment::getUser()->getIdentity());
        $messages = dibi::fetchSingle('SELECT COUNT(*) FROM message WHERE (message.to=%i) AND (message.read=FALSE)', $uid);
        return $messages;
    }
}

?>
