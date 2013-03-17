<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class verify extends MY_Controller {

     function __construct()
     {
         parent::__construct();
         $this->set_template('main');

     }

     function token($token)
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