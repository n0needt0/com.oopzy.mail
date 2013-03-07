<?php

Class Oopzy_model
{

  function __construct()
  {
      $this->oopzybox='';
      $this->to = '';
  }
  public function do_remail_queue($ref, $box, $toemail)
  {

  }

  /*void*/ function verify_email($obj,$box,$qualityonly=false)
  {

      $this->error_message = '';

      if(empty($obj))
      {
          $this->error_message = utils::get_message('MSG_VERIFY_INVALID_EMAIL');
          return false;
      }

      //if obj is email init verification
      $result = filter_var($obj, FILTER_VALIDATE_EMAIL);

      if(empty($result))
      {
          $this->error_message = utils::get_message('MSG_VERIFY_INVALID_EMAIL');
          return false;
      }

       if(stristr($obj, $GLOBALS['host_name']))
      {
           //we do not verify ourselves
           $this->error_message =utils::get_message('MSG_VERIFY_NOSELF');
           return false;
       }

       if(empty($box))
       {
            $this->error_message = utils::get_message('MSG_VERIFY_INVALID_BOX');
            return false;
        }

        if($qualityonly)
        {
             $quality = utils::box_quality($box);

              if($quality < 3)
              {
                  $this->error_message = utils::get_message('MSG_VERIFY_LOW_BOX_QUALITY') . "[$quality]";
                  return false;
              }
         }

          $token = md5($obj . $GLOBALS['PRIVATE_KEY']); //no need to seed with time
          $url = 'http://' . $GLOBALS['www_host_name'] . '/verify_token/' . $token;
          $message = sprintf(utils::get_message('MSG_VERIFY_BODY'), $obj, $url);
          $message_html = sprintf(utils::get_message('MSG_VERIFY_BODY_HTML'), $obj, $url, $url);
          $subject = utils::get_message('MSG_VERIFY_SUBJECT');

          //set request to redis

          $redis = new Redis();

        try{
          $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
          $res_redis = $redis->setex($token, 60*10 , serialize( array('to'=>$obj,'for'=>$box)) ); //10 min ttl

          #mailer
          require_once('Oopzy_mail.php');

          $mailer = new Oopzy_Mail();

          $res_email = $mailer->send_email($from, $obj, $subject);

          if(!$res_email)
          {
                throw new Exception("SMTP Error [$res_email]");
                return false;
          }

          if(!$res_redis)
          {
                throw new Exception("Redis Error [$res_redis]");
                return false;
          }

          if($res_redis && $res_mail)
          {
              return true;
          }

        }
        catch(Exception $e)
        {
            $this->error_message = 'Exception: ' . $e->getMessage();
            return false;
        }
      }
  }

  public function verify_token($obj)
  {
        $this->error_message = "";

        //lets see if it is token
        if(strlen($obj)!=32)
        {
            //not token
            $this->error_message = utils::get_message('MSG_VERIFY_INVALID_TOKEN');
            return false;
        }

        $redis = new Redis();

        try{
          $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
          $res = $redis->get($obj );


          if($res)
          {
                $res = unserialize($res);

                if(isset($res['to']) && isset($res['for']))
                {

                  $this->oopzybox = $res['for'];
                  $this->to = $res['to'];

                  //otherwise start list
                  $key = self::create_link_key($res['for'] , $res['to']);
                  $res_redis = $redis->setex($key, 60*60*24*30 , 1 ); //set for 30 days

                  return true;
            }
          }

          $this->error_message = utils::get_message('MSG_VERIFY_INVALID_TOKEN');
          return false;

        }
        catch(Exception $e)
        {
           $this->error_message = 'Exception: ' . $e->getMessage();
           return false;
        }
   }

   public function get_error()
   {
       return $this->error_message;
   }

   private function create_link_key($from, $to)
   {
       return md5($from . $to);
   }

   public function is_linked($from, $to)
   {
       $key = self::create_link_key($from, $to);

       $redis = new Redis();

       try{
           $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
           $res = $redis->exists($key);

           if($res)
           {
               return true;
           }

           return false;

       }
       catch(Exception $e)
       {
           $this->error_message = 'Exception: ' . $e->getMessage();
           return false;
       }

   }
}