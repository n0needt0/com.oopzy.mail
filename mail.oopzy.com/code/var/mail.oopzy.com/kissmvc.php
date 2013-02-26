<?php
require('kissmvc_core.php');

//===============================================================
// Model/ORM
//===============================================================
class Model extends KISS_Model
{
}

//===============================================================
// Controller
//===============================================================
class Controller extends KISS_Controller
{
    //Example of overriding a core class method with your own
    function request_not_found() {
        header("HTTP/1.0 404 Not Found");
        die(View::do_fetch(VIEW_PATH.'errors/404.php'));
    }
}

//===============================================================
// View
//===============================================================
class View extends KISS_View
{
    function output_with_template($template, $view, $data)
    {
        $data[$GLOBALS['namespace']]['served_from'] = $_SERVER['SERVER_ADDR'];
        $data[$GLOBALS['namespace']]['cache_param'] = $GLOBALS['cache_param'];
        $data[$GLOBALS['namespace']]['jsdebug'] = $GLOBALS['jsdebug'];
        $data[$GLOBALS['namespace']]['site_name'] = $GLOBALS['site_name'];

        $data['content']=self::do_fetch(VIEW_PATH . $view . '.php',$data);
        self::do_dump(VIEW_PATH .  $template . '.php',$data);
    }
}