<?php
function _index($box='Mailbox Name') {
  $view = new View(APP_PATH.'views/index.php');
  $view->set('msg',$box);
  $view->dump();
}

/*
 * This returns json array of message data for given box
 */
/*String*/ function _get_json_messages($box)
{

}

/*
 * This returns a json array of boxes for given starting string
 */
/*String*/ function _get_json_boxes($box)
{

}


/*
 * This return redis info
 */
/*String*/ function _get_server_info()
{
    echo "info";
    die;
}
