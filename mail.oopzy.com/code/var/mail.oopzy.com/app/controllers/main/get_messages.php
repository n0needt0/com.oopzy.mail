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
          foreach($res as $key=>$data)
          {
              $data = unserialize($data);

              if(isset($data['email']))
              {
                  $email = $data['email'];

                  $dt = array();
                  include_once(APP_PATH . 'vendors/PlancakeEmailParser/PlancakeEmailParser.php');
                  $emailParser = new PlancakeEmailParser($email);
                  $dt['to'] = $emailParser->getTo();
                  $dt['key'] = $keys[$key];
                  $dt['from'] = $emailParser->getHeader('from');
                  $dt['dt'] = $emailParser->getHeader('Date');
                  $dt['subject'] = $emailParser->getSubject();
                  $ttl = $redis->ttl($keys[$key]);

                  $dt['debug'] = $data['debug'];

                  $dt['expirein'] = date('i:s' ,$ttl );
                  $emailDeliveredToHeader = $emailParser->getHeader('Delivered-To');

                  $dt['body'] = $emailParser->getPlainBody();
                  $result[] = $dt;
              }
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