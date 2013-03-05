<?php
require_once APP_PATH. 'models/oopzy_model.php';
/*
 * This returns a json array of boxes for given starting string
 */
/*void*/ function _verify_token($obj='')
{

    if(isset($_POST['obj']))
    {
        $obj = urldecode($_POST['obj']);
    }
      else
    {
        $obj = urldecode($obj);
    }

    $verify = new Oopzy_model();

    if(!$verify->verify_token($obj))
    {
        utils::error_echo_die($verify->get_error());
    }

    //TODO , Token redirect

    utils::error_echo_die("");
}