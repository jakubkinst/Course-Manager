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

    public static function sendInvite($email,$cid,$invitedBy){
	$course = CourseModel::getCourseByID($cid);
	$invitedBy = UserModel::getUser($invitedBy);	
	if (UserModel::userExists($email))
	    $msg = 'Hi, you have been invited to course <b>'.$course->name.'</b> at 
		CourseManager by '.$invitedBy->firstname.' '.$invitedBy->lastname.
		'. To accept invitation register at <a href="'.self::$hostUrl.'">'.self::$hostUrl.'</a> with this e-mail address.';
	else
	    $msg = 'Hi, you have been invited to course <b>'.$course->name.'</b> at 
		CourseManager by '.$invitedBy->firstname.' '.$invitedBy->lastname.
		'. To accept invitation login at <a href="'.self::$hostUrl.'">'.self::$hostUrl.'</a> with this e-mail address.';
	
	self::sendMail($email, 'You have been invited to '.$course->name, $msg);
    }
}

?>
