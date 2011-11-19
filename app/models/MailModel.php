<?php

/**
 * MailModel
 *
 * @author Jakub Kinst
 */
class MailModel extends Object {
    public static $hostUrl = 'http://localhost/CourseMan/www/';

   
    public static function sendMailToCurrentUser($subject,$msg) {
	$user = Environment::getUser()->getIdentity();	
	return MailModel::sendMail($user->email, $subject, $msg);
    }
    public static function sendMailToUser($user,$subject,$msg) {
	$email = UserModel::getUser($user)->email;
	return MailModel::sendMail($email, $subject, $msg);
    }
    
    public static function sendMailToStudents($cid,$subject,$msg){
	foreach (CourseModel::getStudents($cid) as $student) {
	    MailModel::sendMail($student->email, $subject, $msg);
	}
    }    
    
    /**
     * Sends E-Mail
     * @param type $to
     * @param type $subject
     * @param type $msg 
     */
    public static function sendMail($to, $subject, $msg) {
	$mail = new Mail;
	$mail->setFrom('CourseManager <cm@kinst.cz>');
	$mail->addTo($to);
	$mail->setSubject($subject);
	$mail->setHTMLBody($msg);
	return Environment::getVariable('mailer')->send($mail);
    } 
     
    public static function sendRegisterHash($uid) {
	$hash = dibi::fetchSingle('SELECT seclink FROM user WHERE id=%i',$uid);
	$link = self::$hostUrl.'user/check?hash='.$hash;
	$msg = 'Welcome to CourseManager. Your registration is almost complete. To cemplete the
		registration process, click to this link: <a href="'.$link.'">'.$link.'</a>.';
	self::sendMailToUser($uid, 'Complete your registration at CourseManager', $msg);
    }

}

?>
