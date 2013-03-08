<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Resolves codeigniter session handling bugs.
 * @see http://codeigniter.com/bug_tracker/bug/7145/
 */

class MY_Email extends CI_Email
{
  function MY_Email()
  {
    // default initialization parameters
    $initialization = array(
                           'newline'  => "\r\n",
                           'protocol' => 'sendmail',
                           'mailpath' => '/usr/sbin/sendmail',
                           'charset'  => 'iso-8859-1',
                           'wordwrap' => TRUE
                           );
    parent::__construct($initialization);
  }

  function send()
  {

    //'Reply-To: '
    //'X-Mailer: sendmail/1.0';


    $logmessage  = print_r($this->_recipients,1);
    $logmessage .= "\n".print_r($this->_headers,1);
    $logmessage .= "\n".$this->_body;
    Utils::log_message(LOG_NOTICE, $logmessage);

    return parent::send();
  }
}