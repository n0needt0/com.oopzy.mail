<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Log extends CI_Log
{
  public function __construct()
  {
    ini_set('log_errors_max_len', 0);

    // Use PHP's log levels
    // @see http://php.net/manual/en/function.syslog.php
    $this->_levels = array(LOG_EMERG   => 'EMERGENCY',  // system is unusable
                           LOG_ALERT   => 'ALERT',     // action must be taken immediately
                           LOG_CRIT    => 'CRITICAL',  // critical conditions
                           LOG_ERR     => 'ERROR',     // error conditions
                           LOG_WARNING => 'WARNING',   // warning conditions
                           LOG_NOTICE  => 'NOTICE',    // normal, but significant, condition
                           LOG_INFO    => 'INFO',      // informational message
                           LOG_DEBUG   => 'DEBUG'      // debug-level message
                          );
    // translate CI's log strings into LOG_ constants
    $this->_old_levels = array('ERROR' => LOG_ERR, 'DEBUG' => LOG_DEBUG,  'INFO' => LOG_INFO, 'ALL' => LOG_DEBUG);

    parent::__construct();
  }

  /**
   * Write Log File
   *
   * Generally this function will be called using the global log_message() function
   *
   * @access  public
   * @param string  the error level
   * @param string  the error message
   * @param bool  whether the error is a native PHP error
   * @return  bool
   */
  function write_log($level = 'error', $msg, $php_error = FALSE)
  {
    // TODO: put in requesting IP Address

    if ($this->_enabled === FALSE)
    {
        return FALSE;
    }

    // if older way (string) $level provided then convert to new (int) $priority
    $priority = (is_int($level)) ? $level : $this->_old_levels[strtoupper($level)];


    if ( !array_key_exists($priority, $this->_levels) OR ($priority > $this->_threshold))
    {
        return FALSE;
    }

    $backtrace = debug_backtrace();
    for ($backstep=1; $backstep <= count($backtrace); $backstep++)
    {
      $class    = (isset($backtrace[$backstep]['class'])) ? $backtrace[$backstep]['class'] : '';
      $function = $backtrace[$backstep]['function'];
      if (in_array($function, array('log_message')))
      {
        continue;
      }
      if (in_array($class, array('CI_DB_active_record','CI_DB_driver','CI_DB_mysql_driver')))
      {
        continue;
      }

      break;
    }
    $remote_addr =@ $_SERVER['REMOTE_ADDR'];

    // format queries
    $sqltest = substr($msg,0,6);
    if (in_array($sqltest, array('SELECT','INSERT','UPDATE','DELETE')))
    {
       $msg = str_replace("\n", ' ', $msg);
    }

    $filepath = $this->_log_path . LOGFILENAME;

    $message  = date($this->_date_fmt) . ' ';
    $message .= str_pad(strtoupper($this->_levels[$priority]),9) . ' - ';
    $message .= $remote_addr . ' ';
    $message .= $class.'::'.$function . ' --> ';
    $message .= $msg."\n";

    // Trial run... we can log *everything* (inc. debug) to syslog and then not use codeigniter
    syslog($priority, $message);
    error_log($message, 3, $filepath);

    return TRUE;
  }
}