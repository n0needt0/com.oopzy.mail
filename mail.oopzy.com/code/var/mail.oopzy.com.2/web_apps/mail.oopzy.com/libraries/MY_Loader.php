<?php

class MY_Loader extends CI_Loader {

  function view($view, $data = array(), $return = FALSE)
  {
    if ($return)
    {
      // if returning as a string, do not wrap the template
      return parent::view($view, $data, $return);
    }

    $CI =& get_instance();
    if (empty($CI->template))
    {
      // false/null means don't load into a template
      return parent::view($view, $data, $return);
    }

    if(is_array($data))
    {
        if (!array_key_exists('html_title', $data))
        {
          $data['html_title'] = '';
        }

        $data['version']  = trim((is_file(APPPATH .'../../VERSION')) ? file_get_contents(APPPATH .'../../VERSION') : '');
        $data['revision'] = trim((is_file(APPPATH .'../../REVISION')) ? file_get_contents(APPPATH .'../../REVISION') : 'dev');


        $data['content'] = parent::view($view, $data, true);
    }
     else
    {
        if (empty($data->html_title))
        {
          $data->html_title = '';
        }

        $data->version  = trim((is_file(APPPATH .'../../VERSION')) ? file_get_contents(APPPATH .'../../VERSION') : '');
        $data->revision = trim((is_file(APPPATH .'../../REVISION')) ? file_get_contents(APPPATH .'../../REVISION') : 'dev');

        $data->content = parent::view($view, $data, true);
    }

    return parent::view('templates/'.$CI->template, $data);
  }
}