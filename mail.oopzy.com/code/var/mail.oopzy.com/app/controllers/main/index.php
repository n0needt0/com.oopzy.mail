<?php
function _index($box='Enter your mailbox')
{
  $box = urldecode($box);

  View::output_with_template('templates/main','index',array('box'=>$box));
}

