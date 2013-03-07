<?php

require_once APP_PATH. 'vendors/swift/lib/swift_required.php';

Class Oopzy_Mail
{
    function send_email($from,$to,$subject,$message='',$message_html='')
    {
          // Create the Transport
          $transport = Swift_SmtpTransport::newInstance($GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT']);

          // Create the Mailer using your created Transport
          $mailer = Swift_Mailer::newInstance($transport);

          // Create the message
          $msg = Swift_Message::newInstance()

          // Give the message a subject
          ->setSubject($subject)

          // Set the From address with an associative array
          ->setFrom(array('Info@' . $GLOBALS['host_name'] => 'Info@' . $GLOBALS['host_name']))

          // Set the To addresses with an associative array
          ->setTo(array($to))

          // Give it a body
          ->setBody($message)

          // And optionally an alternative body
          ->addPart($message_html, 'text/html');

          $res_mail = $mailer->send($msg);

          return $res;
    }
}