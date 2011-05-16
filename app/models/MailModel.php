<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailModel
 *
 * @author JerRy
 */
class MailModel extends Object {
    public static function sendMail($to,$subject,$msg) {
        // not working in debug
        $mail = new Mail;
        $mail->setFrom('Franta <info@cm.kinst.cz>');
        $mail->addTo($to);
        $mail->setSubject($subject);
        $mail->setBody($msg);
        $mail->send();
    }
}

?>
