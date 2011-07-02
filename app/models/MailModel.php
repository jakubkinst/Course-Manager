<?php


/**
 * MailModel
 *
 * @author Jakub Kinst
 */
class MailModel extends Object {
    
    /**
     * Sends E-Mail
     * @param type $to
     * @param type $subject
     * @param type $msg 
     */
    public static function sendMail($to, $subject, $msg) {
        $mail = new Mail;
        $mail->setFrom('Course-Manager <info@cm.kinst.cz>');
        $mail->addTo($to);
        $mail->setSubject($subject);
        $mail->setBody($msg);
        $mail->send();
    }
}

?>
