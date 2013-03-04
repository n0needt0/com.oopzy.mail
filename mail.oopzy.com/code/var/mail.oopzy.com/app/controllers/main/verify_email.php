<?php
/*
 * This returns a json array of boxes for given starting string
 */
/*String*/ function _verify_email($obj='')
{
    if(empty($obj))
    {
        utils::error_echo_die($GLOBALS['MESSAGES']['MSG_VERIFY_INVALID']);
    }

    //if obj is email init verification
    if(filter_var($obj, FILTER_VALIDATE_EMAIL))
    {
         if(stristr($obj, $GLOBALS['host_name']))
         {
             //we do not verify ourselves
             utils::error_echo_die($GLOBALS['MESSAGES']['MSG_VERIFY_NOSELF']) ;
         }

         $token = md5($obj . time());
         $url = 'http://' . $GLOBALS['www_host_name'] . '/verify_email/' . $token;
         $message = sprintf($GLOBALS['MESSAGES']['MSG_VERIFY_BODY'], $obj, $url);
         $message_html = sprintf($GLOBALS['MESSAGES']['MSG_VERIFY_BODY_HTML'], $obj, $url, $url);
         $subject = $GLOBALS['MESSAGES']['MSG_VERIFY_SUBJECT'];

         //set request to redis

         $redis = new Redis();

         try{
             $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
             $res_redis = $redis->setex($token, 60*10 , $obj ); //10 min ttl

              require_once APP_PATH. 'vendors/swift/lib/swift_required.php';

              // Create the Transport
              $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

              // Create the Mailer using your created Transport
              $mailer = Swift_Mailer::newInstance($transport);

              // Create the message
              $msg = Swift_Message::newInstance()

                // Give the message a subject
                ->setSubject($subject)

                // Set the From address with an associative array
                ->setFrom(array('nobody@' . $GLOBALS['host_name'] => 'Info'))

                // Set the To addresses with an associative array
                ->setTo(array($obj))

                // Give it a body
                ->setBody($message)

                // And optionally an alternative body
                ->addPart($message_html, 'text/html')
                ;

              $res_mail = $mailer->send($msg);

             if($res_redis && $res_mail)
             {
                 utils::error_echo_die("");
             }
         }
         catch(Exception $e)
         {
             utils::error_echo_die('Exception ' . $e->getMessage());
         }

    }
      else
    {
       //lets see if it is token
       if(strlen($obj)!=32)
       {
           //not token
           utils::error_echo_die($GLOBALS['MESSAGES']['MSG_VERIFY_INVALID']);
       }

       //ok it is token lets see if we have request for on
    }
}