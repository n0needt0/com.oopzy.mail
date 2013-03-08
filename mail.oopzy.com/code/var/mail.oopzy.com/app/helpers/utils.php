<?php
Class utils
{

    public function get_message($key)
    {
        if(isset($GLOBALS['MESSAGES'][$key]))
        {
            return $GLOBALS['MESSAGES'][$key];
        }
          else
        {
           return $key;
        }
    }

    public function get_redis($key=false)
    {

        $redis = new Redis();

        try{
                //here where we get proper connection

                $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
                return $redis;

          }
            catch(Exception $e)
          {
              throw new Exception($e->getMessage());
          }
    }

      public function box_quality($box)
      {
          $quality = 0;

          //if the password length is less than 6, return message.
          if (strlen($box) < 6)
          {
              return $quality;
          }

          $result = filter_var($box . '@oopzy.com', FILTER_VALIDATE_EMAIL);

          if(empty($result))
          {
             return -1;
          }

          if (strlen($box) > 6)
          {
              $quality += 1;
          }

          //if password contains both lower and uppercase characters, increase strength str
          if (preg_match('/([a-z].*[A-Z])|([A-Z].*[a-z])/',$box))
          {
              $quality += 1;
          }

          //if it has numbers and characters, increase strength str
          if (preg_match('/([a-zA-Z])/',$box) && preg_match('/([0-9])/',$box))
          {
              $quality += 1;
          }

          //if it has one special character, increase strength str
          if (preg_match('/([!,%,&,#,$,^,*,?,_,~])/',$box))
          {
              $quality += 1;
          }

          //if it has two special characters, increase strength str
          if (preg_match('/(.*[!,%,&,#,$,^,*,?,_,~].*[!,%,&,#,$,^,*,?,_,~])/',$box))
          {
              $quality += 1;
          }
          return $quality;
      }

      public function error_echo_die($error)
      {
           echo json_encode(array('error'=>$error));
           die;
      }
