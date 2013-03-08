<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Resolves codeigniter session handling bugs.
 * @see http://codeigniter.com/bug_tracker/bug/7145/
 */

class MY_Session extends CI_Session
{
  function MY_Session()
  {
      parent::__construct();
  }

  function _serialize($data)
  {
      return serialize($data);
  }

  function _unserialize($data)
  {
      return @unserialize($data);
  }

  function sess_update()
  {
    // We only update the session every five minutes by default
    if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
    {
      return;
    }

    $sessid = $this->userdata['session_id'];

    // Update the session data in the session data array
    $this->userdata['last_activity'] = $this->now;

    // _set_cookie() will handle this for us if we aren't using database sessions
    // by pushing all userdata to the cookie.
    $cookie_data = NULL;

    // Update the session ID and last_activity field in the DB if needed
    if ($this->sess_use_database === TRUE)
    {
      // set cookie explicitly to only have our session data
      $cookie_data = array();
      foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
      {
        $cookie_data[$val] = $this->userdata[$val];
      }

      $this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now), array('session_id' => $sessid)));
    }

    // Write the cookie
    $this->_set_cookie($cookie_data);
  }
}