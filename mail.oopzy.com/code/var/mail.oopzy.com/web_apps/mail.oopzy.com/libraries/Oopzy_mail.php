<?php
require_once 'vendors/swift/lib/swift_required.php';

$mail = new Oopzy_Mail();

Class Oopzy_Mail
{
    function send_email($from,$to,$subject,$message='',$message_html='')
    {
        try{

          $transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT)
              ->setUsername(SMTP_USER)
              ->setPassword(SMTP_PASSWORD);

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
          ->setBody($message);

          if(!empty($message_html))
          {
              // And optionally an alternative body
              $msg->addPart($message_html, 'text/html');
          }

          $res = $mailer->send($msg);

          return $res;
       }
         catch(Exception $e)
       {
          echo $e->getMessage();
       }
    }
}