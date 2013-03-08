<?php
/**
 * Extending the Core Controller class
 *
 * code in /libraries/MY_Controller.php allow you to use your controllers normally,
 * and it will (in fact) extend your MY_Controller.
 * @see http://codeigniter.com/user_guide/general/core_classes.html
 */
class MY_Controller extends CI_Controller {

  public function MY_Controller()
  {
      parent::__construct();
      Utils::log_message( LOG_NOTICE, Utils::curPageURL() );
      $this->load->library('session');
  }

  protected function set_template($template_name)
  {
      $this->template = $template_name;
  }

  public function add_notification($type, $message, &$data=null)
  {
    if (!in_array($type, array('error','info','success','alert')))
    {
        $type = 'info';
    }

    $message = trim($message);
    $notification = array('message' => $message, 'class'=>$type);

    $this->notifications[] = $notification;

    if (null !== $data)
    {
      if (!array_key_exists('notifications', $data))
      {
          $data['notifications'] = array();
      }

      $data['notifications'][] = array('class'=>$type, 'message'=>$message);
    }
  }

  public function redirect_with_notifications($path)
  {
    $data = array();
    $data['notifications'] = $this->notifications;
    Utils::log_message(LOG_INFO, "Redirecting to $path");
    redirect_with_notifications($path, $data);
  }

}