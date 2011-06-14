<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserModel
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

        if (!$row) {
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

    static public function addUser($values) {
        $values['password'] = UserModel::calculateHash($values['password']);
        $values['seclink'] = sha1($values['email'] . time() . 'yjtbvb678b987n5c4');
        dibi::query('INSERT INTO user', $values);
        MailModel::sendMail($values['email'],'Welcome to Course Manager','Regisration almost complete follow '.$values['seclink']);
    }
    public static function getUserID($user){
       return dibi::fetchSingle('SELECT id FROM user WHERE email=%s',$user->email);
    }
    public static function getUserIDByEmail($email){
       return dibi::fetchSingle('SELECT id FROM user WHERE email=%s',$email);
    }
    public static function getUser($uid){
       return dibi::fetch('SELECT * FROM user WHERE id=%i',$uid);
    }

}

?>
