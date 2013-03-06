<?php

require_once APP_PATH. 'models/oopzy_model.php';

/*String*/ function _save_message()
{
    $ref = $_POST['ref'];

    $tmp = explode(':',$ref);
    $tmp = explode('@',$tmp[0]);
    $box = $tmp[0];

    $toemail = $_POST['to'];

    $redis = new Redis();

    try{

      $redis->connect($GLOBALS['REDISHOST'], $GLOBALS['REDISPORT']);

      $oopzy = new Oopzy_model();

      $message = '';

      //see if link is established already and we can process
      $res = $oopzy->is_linked($box, $toemail);

      if( !$res)
      {
          //send verification emai;

          $v = $oopzy->verify_email($toemail, $box);

          if(!$v)
          {
              $message = utils::get_message($oopzy->get_error());
              utils::error_echo_die($message);
          }

          $message = utils::get_message('MSG_SAVE_MESSAGE_VERIFY');
      }

      $oopzy->do_remail_queue($ref, $box, $toemail);

      //set autoremail
      //if box quality is low not much we can do box_quality($box)
      //also here where we can set $key request

    }
      catch(Exception $e)
    {
        $message = $e->getMessage();
    }

    utils::error_echo_die($message);

}