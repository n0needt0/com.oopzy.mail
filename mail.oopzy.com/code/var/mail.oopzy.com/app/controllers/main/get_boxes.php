<?php
/*
 * This returns a json array of boxes for given starting string
 */
/*String*/ function _get_boxes($str='')
{

    if(isset($_REQUEST['term']))
    {
        $str = $_REQUEST['term'];
    }

    if(strlen($str) < 2 )
    {
        return array();
    }

    $redis = new Redis();

    try{
      $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);
      $res = $redis->keys("$str*");

      if(isset($_REQUEST['callback']))
      {
          echo $_REQUEST['callback'] . '('. json_encode($res) . ')';
      }
        else
      {
          echo json_encode($res);
      }
    }
      catch(Exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
    }

    die;
}