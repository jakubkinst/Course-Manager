<?php

/**
 * Model class with static methods dedicated to Message-related db methods
 * 
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models
 */
class MessageModel extends Object {

    /**
     * Sends a new message
     * @param array $values
     * @return boolean 
     */
    public static function sendMessage($values) {
	$array = array(
	    'to' => UserModel::getUserIDByEmail($values['to']),
	    'subject' => $values['subject'],
	    'content' => $values['content'],
	    'from' => UserModel::getUserID(Environment::getUser()->getIdentity()),
	    'sent' => new DateTime
	);
	return dibi::query('INSERT INTO message', $array);
    }

    /**
     * Returns an array of incoming messages
     * @return array
     */
    public static function getInbox() {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$messages = dibi::fetchAll('SELECT * FROM message WHERE (message.to=%i) ORDER BY sent DESC', $uid);
	foreach ($messages as $message) {
	    $message['from'] = UserModel::getUser($message['from']);
	}
	return $messages;
    }

    /**
     * Returns an array of outgoing messages
     * @return array
     */
    public static function getOutbox() {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$messages = dibi::fetchAll('SELECT * FROM message WHERE (message.from=%i) ORDER BY sent DESC', $uid);
	foreach ($messages as $message) {
	    $message['to'] = UserModel::getUser($message['to']);
	}
	return $messages;
    }

    /**
     * Sets a read parameter of a message to true
     * @param int $mid Message ID
     * @return boolean 
     */
    public static function setRead($mid) {
	$arr = array(
	    'read' => TRUE,
	);
	return dibi::query('UPDATE `message` SET ', $arr, 'WHERE `id`=%i', $mid);
    }

    /**
     * Returns a single message
     * @param int $mid Message ID
     * @return array 
     */
    public static function getMessage($mid) {
	$message = dibi::fetch('SELECT * FROM message WHERE id=%i', $mid);
	$message['from'] = UserModel::getUser($message['from']);
	$message['to'] = UserModel::getUser($message['to']);
	return $message;
    }

    /**
     * Counts unread messages
     * @return type 
     */
    public static function countUnread() {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$messages = dibi::fetchSingle('SELECT COUNT(*) FROM message WHERE (message.to=%i) AND (message.read=FALSE)', $uid);
	return $messages;
    }

    /**
     * Inserts > sign before each line in input string
     * @param string $msg
     * @return string 
     */
    public static function wrapReply($msg) {
	return ">" . str_replace(array("\n"), "\n" . ">", $msg);
    }

}

?>
