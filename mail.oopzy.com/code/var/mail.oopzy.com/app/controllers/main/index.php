<?php
function _index($box='Enter Your Mail')
{
  $box = urldecode($box);
  $rememberme = '';

  if(isset($_COOKIE['rememberme']))
  {
      $rememberme = " checked='checked' ";
  }

  View::output_with_template('templates/main','mail',array('box'=>$box, 'rememberme'=>$rememberme));
}

