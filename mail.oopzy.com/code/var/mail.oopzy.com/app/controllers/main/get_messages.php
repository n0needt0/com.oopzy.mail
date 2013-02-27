<?php
/*
 * This returns json array of message data for given box
 */
/*String*/ function _get_messages($box='')
{
    if(empty($box))
    {
        die;
    }

    $redis = new Redis();

    try{
      $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
      $res = $redis->get("$box");

      //process messages
      $result = array();

      foreach($res as $key=>$value)
      {
          if($mail = messageparser::parse(unserialize($value)))
          {
              $result[] = $mail;
          }
      }

      if(isset($_REQUEST['callback']))
      {
          echo $_REQUEST['callback'] . '('. json_encode($result) . ')';
      }
      else
      {
          echo json_encode($result);
      }
    }
    catch(Exception $e)
    {
      echo json_encode(array('error' => $e->getMessage()));
    }

    die;

}