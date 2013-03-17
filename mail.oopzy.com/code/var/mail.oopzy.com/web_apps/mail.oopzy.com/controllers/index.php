<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {

     function __construct()
     {
         parent::__construct();
         $this->set_template('main');

     }

     function index($box='')
     {
          $box = urldecode($box);
          $rememberme = '';

          if(isset($_COOKIE['rememberme']))
          {
              $rememberme = " checked='checked' ";
          }

          $data = array('rememberme'=>" checked='checked' ", 'box'=>$box);

          $this->load->view('index', $data);
     }

     function about()
     {
         $data = array();
         $this->load->view('about', $data);
     }

     function price_list()
     {
         $data = array();
         $this->load->view('price_list', $data);
     }

     function verify_token($token)
     {
         $token = urldecode($token);

         include(APPPATH. '/libraries/Oopzy_lib.php');

          $verify = new Oopzy_lib();

          if(!$verify->verify_token($token))
          {
              $this->response($verify->get_error());
              die;
          }

          $data = array('status'=>'verified', 'oopzybox'=>$verify->oopzybox,'to'=>$verify->to);
          $this->load->view('verified', $data);
     }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */