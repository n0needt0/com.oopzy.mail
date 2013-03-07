<?php

require_once APP_PATH. 'vendors/swift/lib/swift_required.php';

Class Oopzy_Mail
{
    function send_email($from,$to,$subject,$message='',$message_html='')
    {
          // Create the Transport
          if($GLOBALS['SMTP'] = 'basic')
          {
              $transport = Swift_SmtpTransport::newInstance($GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT']);
          }
            else
          {
              $transport = Swift_SmtpTransport::newInstance($GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT'], $GLOBALS['SMTP_SSL'])
              ->setUsername($GLOBALS['SMTP_USER'])
              ->setPassword($GLOBALS['SMTP_PASSWORD']);
          }

          // Create the Mailer using your created Transport
          $mailer = Swift_Mailer::newInstance($transport);

          // Create the message
          $msg = Swift_Message::newInstance()

          // Give the message a subject
          ->setSubject($subject)

          // Set the From address with an associative array
          ->setFrom(array($from))

          // Set the To addresses with an associative array
          ->setTo(array($to))

          // Give it a body
          ->setBody($message)

          // And optionally an alternative body
          ->addPart($message_html, 'text/html');

          $res = $mailer->send($msg);

          return $res;
    }
}