<?php
/*
 * This returns a json array of boxes for given starting string
 */
/*String*/ function _save_message()
{
    $ref = $_POST['ref'];
    $to = $_POST['to'];

    $redis = new Redis();

    try{
      $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
      //put request in a queue

      echo json_encode(array($ref,$to));
      die;
    }
      catch(Exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        die;
    }
}