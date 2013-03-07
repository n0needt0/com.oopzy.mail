<?php
//===============================================
// Debug
//===============================================
ini_set('display_errors','On');
error_reporting(E_ALL);

//===============================================
// mod_rewrite
//===============================================
//Please configure via .htaccess or httpd.conf

//===============================================
// Madatory KISSMVC Settings (please configure)
//===============================================
define('APP_PATH','app/'); //with trailing slash pls
define('VIEW_PATH','app/views/'); //with trailing slash pls
define('WEB_FOLDER','/kissmvc/'); //with trailing slash pls

//===============================================
// Other Settings
//===============================================
$GLOBALS['site_name']='oopzy spam fighter single use recycle email';
$GLOBALS['jsdebug'] = true;
$GLOBALS['namespace'] = 'oopzy';
$GLOBALS['cache_param'] = 5;

$GLOBALS['allowed_hosts'] = array('oopzy.com');
$GLOBALS['host_name'] = 'oopzy.com';

$GLOBALS['PRIVATE_KEY'] = '#27drue';

$GLOBALS['www_host_name'] = 'www.oopzy.com';

$GLOBALS['production'] = false;

$GLOBALS['REDISHOST'] = '127.0.0.1';
$GLOBALS['REDISPORT'] = 6379;
$GLOBALS['REDISDB'] = 0;

$GLOBALS['SMTP'] = 'basic';
$GLOBALS['SMTP_HOST'] = 'smtp.oopzy.com';
$GLOBALS['SMTP_PORT'] = 465;
$GLOBALS['SMTP_SSL'] = '';
$GLOBALS['SMTP_USER'] = 'hampster@oopzy.com';
$GLOBALS['SMTP_PASSWORD'] = '0ne0rtw0!';


//make it self settable based on language used
$GLOBALS['MESSAGES'] = array(
                                                    'MSG_VERIFY_BODY'=>"Please verify this email (%s) \n by clicking on this link ( %s ), \n if you can not click it cut and paste it into your browser",
                                                    'MSG_VERIFY_BODY_HTML'=>"Please verify this email (%s) \n by clicking on this link ( <a href='%s'>%s</a> ), \n if you can not click it cut and paste it into your browser",
                                                    'MSG_VERIFY_SUBJECT'=>'Verification request from Oopzy.com',
                                                    'MSG_VERIFY_NOSELF'=> 'You can not use to forward ' . $GLOBALS['host_name'] . ' mails emails ',
                                                    'MSG_VERIFY_INVALID_EMAIL'=> 'Invalid Email ',
                                                    'MSG_VERIFY_TOKEN'=> 'Invalid Token ',
                                                    'MSG_VERIFY_INVALID_BOX'=> 'Invalid Box ',
                                                    'MSG_VERIFY_LOW_BOX_QUALITY'=> 'Low Box Quality ',
                                                    'MSG_SAVE_MESSAGE_VERIFY' => "This email needs verification,\n please check it for further instructions.\n Once email ownership verified we will complete this request",
                                                    'MSG_VERIFY_INVALID_TOKEN' => 'Invalid Token'
                                                );

//===============================================
// Includes
//===============================================
require('kissmvc.php');

//===============================================
// Session
//===============================================
/*
session_start();
*/
//===============================================
// Uncaught Exception Handling
//===============================================s
/*
set_exception_handler('uncaught_exception_handler');

function uncaught_exception_handler($e) {
  ob_end_clean(); //dump out remaining buffered text
  $vars['message']=$e;
  die(View::do_fetch(APP_PATH.'errors/exception_uncaught.php',$vars));
}

function custom_error($msg='') {
  $vars['msg']=$msg;
  die(View::do_fetch(APP_PATH.'errors/custom_error.php',$vars));
}
*/

//===============================================
// Database
//===============================================
/*
function getdbh() {
  if (!isset($GLOBALS['dbh']))
    try {
      $GLOBALS['dbh'] = new PDO('sqlite:'.APP_PATH.'db/kissmvc.sqlite');
      //$GLOBALS['dbh'] = new PDO('mysql:host=localhost;dbname=dbname', 'username', 'password');

    } catch (PDOException $e) {
      die('Connection failed: '.$e->getMessage());
    }
  return $GLOBALS['dbh'];
}
*/

//===============================================
// Autoloading for Business Classes
//===============================================
// Assumes Model Classes start with capital letters and Helpers start with lower case letters

function __autoload($classname) {
    $a=$classname[0];
    if ($a >= 'A' && $a <='Z')
    {
        require_once(APP_PATH.'models/'.$classname.'.php');
    }
}

require_once(APP_PATH.'helpers/utils.php');

//===============================================
// Start the controller
//===============================================
$controller = new Controller(APP_PATH.'controllers/',WEB_FOLDER,'main','index');
