<?php
/*
 * This returns a json array of boxes for given starting string
 */
/*String*/ function _get_server_info()
{
    $redis = new Redis();

    try{
      $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
      $res = $redis->info();

      echo json_encode($res);
      die;
    }
      catch(Exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        die;
    }
}