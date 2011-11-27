<?php

/**
 * UserModel
 *
 * @author JerRy
 */
class UserModel extends Object implements IAuthenticator {

    /**
     * Performs an authentication
     * @param  array
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) {
	list($username, $password) = $credentials;
	$row = dibi::fetch('SELECT * FROM user WHERE email=%s', $username);

	if ((!$row) || (!$row->checked)) {
	    throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
	}

	if ($row->password !== self::calculateHash($password)) {
	    throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
	}

	unset($row->password);
	return new Identity($row->id, $row->email, $row);
    }

    /**
     * Computes salted password hash.
     * @param  string
     * @return string
     */
    static public function calculateHash($password) {
	return md5($password . str_repeat('hbguyti76bv56c764vb98n', 10));
    }

    /**
     * Adds new user to db
     * @param type $values 
     */
    static public function addUser($values) {
	dibi::begin();
	$values['created'] = new DateTime;
	$values['password'] = UserModel::calculateHash($values['password']);
	$values['seclink'] = sha1($values['email'] . time() . 'yjtbvb678b987n5c4');
	$result = dibi::query('INSERT INTO user', $values);
	$id = dibi::getInsertId();
	MailModel::sendRegisterHash($id);
	dibi::commit();
	return $result;
    }
    
     static public function editUser($values) {
	$uid = Environment::getUser()->getIdentity()->id;
	dibi::begin();	
	$result = dibi::query('UPDATE user SET', $values,'WHERE id=%i',$uid);
	//create settings record
	dibi::query('INSERT INTO settings',array('User_id'=>$id));
	dibi::commit();
	return $result;
    }

    public static function checkUser($hash) {
	dibi::begin();
	$result = dibi::query('UPDATE user SET checked=1 WHERE seclink=%s', $hash);
	dibi::commit();
	return $result;
    }

    /**
     * Returns userID according to user object
     * @param type $user
     * @return type 
     */
    public static function getUserID($user) {
	return dibi::fetchSingle('SELECT id FROM user WHERE email=%s', $user->email);
    }

    /**
     * Returns userID according to user email
     * @param type $user
     * @return type 
     */
    public static function getUserIDByEmail($email) {
	return dibi::fetchSingle('SELECT id FROM user WHERE email=%s', $email);
    }

    /**
     * Returns user by ID
     * @param type $uid
     * @return type 
     */
    public static function getUser($uid) {
	return dibi::fetch('SELECT * FROM user WHERE id=%i', $uid);
    }
    
    public static function getLoggedUser(){
	$uid = Environment::getUser()->getIdentity()->id;
	return dibi::fetch('SELECT * FROM user WHERE id=%i',$uid);
    }
    
    public static function userExists($email){
	return dibi::fetch('SELECT * FROM user WHERE email=%s',$email);
    }
    public static function deleteUncheckedUsers(){
	dibi::begin();
	$users = dibi::fetchAll('SELECT * FROM user WHERE checked=0');
	foreach ($users as $user) {
	    if (new DateTime($user->created) < date_sub(new DateTime, date_interval_create_from_date_string('1 day')))
		    dibi::query ('DELETE FROM user WHERE id=%i',$user->id);
	}
	dibi::commit();
    }	

}

?>
