<?php

/**
 * Model class with static methods used in Mailing services
 *
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models/Tools
 */
class MailModel extends Object {

	/**
	 * Sends an e-mail to a current (logged) user
	 * @param string $subject
	 * @param string $msg
	 * @return boolean
	 */
	public static function sendMailToCurrentUser($subject, $msg) {
		$user = Environment::getUser()->getIdentity();
		return MailModel::addMail($user->email, $subject, $msg);
	}

	/**
	 * Sends an e-mail to given user
	 * @param int $uid
	 * @param string $subject
	 * @param string $msg
	 * @return boolean
	 */
	public static function sendMailToUser($uid, $subject, $msg) {
		$email = UserModel::getUser($uid)->email;
		return MailModel::addMail($email, $subject, $msg);
	}

	/**
	 * Sends e-mails to all students attending a course
	 * @param int $cid Course ID
	 * @param string $subject
	 * @param string $msg
	 */
	public static function sendMailToStudents($cid, $subject, $msg) {
		foreach (CourseModel::getStudents($cid) as $student) {
			MailModel::addMail($student->email, $subject, $msg);
		}
	}

	/**
	 * Adds e-mail to a db to be sent
	 * @param string $to
	 * @param strng $subject
	 * @param string $msg
	 */
	public static function addMail($to, $subject, $msg) {

		return dibi::query('INSERT INTO mail', array('to' => $to, 'subject' => $subject, 'msg' => $msg));
	}

	/**
	 * Actually sends an e-mail via mailer
	 * @param string $to
	 * @param string $subject
	 * @param string $msg
	 * @return boolean
	 */
	public static function sendMail($to, $subject, $msg) {
		$mail = new Mail;
		$mail->setFrom('CourseManager <cm@kinst.cz>');
		$mail->addTo($to);
		$mail->setSubject($subject);
		$mail->setHTMLBody($msg);
		Environment::getVariable('mailer')->send($mail);
		return true;
	}

	/**
	 * Sends a hash code to a user to confirm his e-mail address
	 * @param int $uid User ID
	 */
	public static function sendRegisterHash($uid,$baseUri) {
		$hash = dibi::fetchSingle('SELECT seclink FROM user WHERE id=%i', $uid);
		$link = $baseUri . 'user/check?hash=' . $hash;
		$msg = 'Welcome to CourseManager. Your registration is almost complete. To cemplete the
		registration process, click to this link: <a href="' . $link . '">' . $link . '</a>.';
		self::sendMailToUser($uid, 'Complete your registration at CourseManager', $msg);
	}

	/**
	 * Sends a course invitation to a user
	 * @param string $email
	 * @param int $cid Course ID
	 * @param id $invitedBy User ID
	 */
	public static function sendInvite($email, $cid, $invitedBy,$link) {
		$course = CourseModel::getCourse($cid);
		$invitedBy = UserModel::getUser($invitedBy);
		if (UserModel::userExists($email))
			$msg = 'Hi, you have been invited to course <b>' . $course->name . '</b> at
		CourseManager by ' . $invitedBy->firstname . ' ' . $invitedBy->lastname .
					'. To accept invitation register at <a href="' . $link . '">' . $link . '</a> with this e-mail address.';
		else
			$msg = 'Hi, you have been invited to course <b>' . $course->name . '</b> at
		CourseManager by ' . $invitedBy->firstname . ' ' . $invitedBy->lastname .
					'. To accept invitation login at <a href="' . $link . '">' . $link . '</a> with this e-mail address.';

		self::addMail($email, 'You have been invited to ' . $course->name, $msg);
	}

	/**
	 * Sends all unsent mails from db.
	 */
	public static function sendMailsNow() {
		$mails = dibi::fetchAll('SELECT * FROM mail WHERE sent IS NULL');
		foreach ($mails as $mail) {
			if (self::sendMail($mail->to, $mail->subject, $mail->msg))
				dibi::query('UPDATE mail SET sent=NOW() WHERE id=%i', $mail->id);
		}
	}

}

?>
