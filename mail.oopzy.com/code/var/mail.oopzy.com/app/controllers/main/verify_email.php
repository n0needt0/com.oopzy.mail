<?php
require_once APP_PATH. 'models/oopzy_model.php';
/*
 * This returns a json array of boxes for given starting string
 */
/*void*/ function _verify_email($obj='',$box='')
{

    if(isset($_POST['obj']))
    {
        $obj = urldecode($_POST['obj']);
        $box = urldecode($_POST['box']);
    }
      else
    {
        $obj = urldecode($obj);
        $box = urldecode($box);
    }

    $verify = new Oopzy_model();

    if(!$verify->verify_email($obj, $box))
    {
        utils::error_echo_die($verify->get_error());
    }

    //TODO , Token redirect
    utils::error_echo_die("");
}