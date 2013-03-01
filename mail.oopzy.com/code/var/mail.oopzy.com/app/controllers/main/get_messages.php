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
      //first get keys for this box
      $keys = $redis->keys($box . '@' . $GLOBALS['host_name'] . ':*');

      //then all messages
      $res = $redis->mGet($keys);

      //process messages
      $result = array();

      if($res)
      {
          foreach($res as $key=>$email)
          {
              $dt = array();
              $emailParser = new PlancakeEmailParser(unserialize($email));
              $dt['to'] = $emailParser->getTo();
              $dt['key'] = $keys[$key];
              $dt['from'] = $emailParser->getHeader('from');
              $dt['dt'] = $emailParser->getHeader('Date');
              $dt['subject'] = $emailParser->getSubject();

              $emailDeliveredToHeader = $emailParser->getHeader('Delivered-To');

              $dt['body'] = $emailParser->getPlainBody();
              $result[] = $dt;
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