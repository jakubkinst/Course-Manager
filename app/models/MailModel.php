<?php

/**
 * MailModel
 *
 * @author Jakub Kinst
 */
class MailModel extends Object {

    public static function getMailer() {
	return new SmtpMailer(array(
	    'host' => 'smtp.gmail.com',
	    'username' => 'cm@kinst.cz',
	    'password' => 'Ug6}\17MH<0_X:c',
	    'secure' => 'ssl',
	));
    }

    public static function sendMailToCurrentUser($subject,$msg) {
	$user = Environment::getUser()->getIdentity();	
	return MailModel::sendMail($user->email, $subject, $msg);
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

}

?>
