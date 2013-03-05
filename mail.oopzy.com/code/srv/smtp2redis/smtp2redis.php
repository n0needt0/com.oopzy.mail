<?php
require(dirname(__FILE__) .'/config.php');

set_time_limit(0);

$smtp2redis = new Smtp2redis();
$smtp2redis->run();

Class Smtp2redis
{
    public function __construct()
    {

    }

    public function run()
    {
        $this->ALLOWED_HOSTS = explode(',', ALLOWED_HOSTS);
        $this->GM_ERROR = false;
        $this->next_id = 1;

        $this->clients = array();

        $this->socket = stream_socket_server('tcp://0.0.0.0:25', $errno, $errstr);

        if (!$this->socket)
        {
            $this->log_line( "ERROR: Can't create Socket: $errstr ($errno)", 3 );
            //no reason to live withour socket
            die;
        }

        stream_set_blocking($this->socket, 0);
        $this->base = event_base_new();
        $this->event = event_new();
        event_set($this->event, $this->socket, EV_READ | EV_PERSIST, array($this,'event_accept'), $this->base);
        event_base_set($this->event, $this->base);
        event_add($this->event);

        $this->log_line("Mail Daemon started on port 25", 1);

        // drop down to user level after opening the smptp port
        $this->user = posix_getpwnam(SERVICE_USER);
        posix_setgid($this->user['gid']);
        posix_setuid($this->user['uid']);
        $this->user = null;

        event_base_loop($this->base);

    }

    private function event_error($buffer, $error, $id)
    {

      //just to see whats up on errors, it really should be stringed in extension
      $event_errors = array(
          //'EVBUFFER_READ' => 1,
          'EVBUFFER_WRITE' => 2,
          //'EVBUFFER_EOF' => 16,
          'EVBUFFER_ERROR' => 32,
          'EVBUFFER_TIMEOUT' => 64
      );

        $errors = array();

        foreach ($event_errors as $error_type => $error_code)
        {
            if ($error  & $error_code)
            {
                array_push($errors, $error_type);
            }
        }

        if(count($errors))
        {
            $err =  'error event #'. join(' | ', $errors)."\n";
            $this->log_line("INFO: $err" , 1);
            event_buffer_disable($this->clients[$id]['ev_buffer'], EV_READ | EV_WRITE);
            event_buffer_free($this->clients[$id]['ev_buffer']);
            fclose($this->clients[$id]['socket']);
            unset($this->clients[$id]);
        }
    }

    private function event_write($buffer, $id)
    {
        if (!empty($this->clients[$id]['kill_time']))
        {
            event_buffer_disable($this->clients[$id]['ev_buffer'], EV_READ | EV_WRITE);
            event_buffer_free($this->clients[$id]['ev_buffer']);
            fclose($this->clients[$id]['socket']);
            unset($this->clients[$id]);
        }
    }

    function event_accept($socket, $flag, $base)
    {
        static $next_id = 0;

        $connection = stream_socket_accept($socket);
        stream_set_blocking($connection, 0);

        $next_id++;

        $buffer = event_buffer_new($connection, array($this,'event_read'), array($this,'event_write'), 'self::event_error', $next_id);

        if(false === $buffer)
        {
            $this->log_line("ERROR: Can not create buffer!");
            return false;
        }
        event_buffer_base_set($buffer, $base);
        event_buffer_timeout_set($buffer, TIMEOUT, TIMEOUT);
        event_buffer_watermark_set($buffer, EV_READ, 0, 0xffffff);
        event_buffer_priority_set($buffer, 10);
        event_buffer_enable($buffer, EV_READ | EV_PERSIST);

        $this->clients[$next_id]['socket'] = $connection; // new socket
        $this->clients[$next_id]['ev_buffer'] = $buffer; // new socket
        $this->clients[$next_id]['state'] = 0;
        $this->clients[$next_id]['mail_from'] = '';
        $this->clients[$next_id]['helo'] = '';
        $this->clients[$next_id]['rcpt_to'] = '';
        $this->clients[$next_id]['error_c'] = 0;
        $this->clients[$next_id]['read_buffer'] = '';
        $this->clients[$next_id]['read_buffer_ready'] = false; // true if the buffer is ready to be fetched

        $this->clients[$next_id]['response'] = ''; // response messages are placed here, before they go on the write buffer
        $this->clients[$next_id]['time'] = time();
        $this->clients[$next_id]['address'] = stream_socket_get_name($this->clients[$next_id]['socket'], true);;

        $this->process_smtp($next_id);

        if (strlen($this->clients[$next_id]['response']) > 0)
        {
            event_buffer_write($buffer, $this->clients[$next_id]['response']);
            $this->add_response($next_id, null);
        }
    }



    function event_read($buffer, $id)
    {

      while ($read = event_buffer_read($buffer, 1024))
      {
          $this->clients[$id]['read_buffer'] .= $read;
      }

      // Determine if the buffer is ready
      // The are two states when we determine if the buffer is ready.
      // State 1 is the command state, when we wait for a command from
      // the client
      // State 2 is the DATA state, when the client sends the data
      // for the email.

      if ($this->clients[$id]['state'] === 1)
      {
          // command state, strings terminate with \r\n
          if (strpos($this->clients[$id]['read_buffer'], "\r\n", strlen($this->clients[$id]['read_buffer']) - 2) !== false)
          {
              $this->clients[$id]['read_buffer_ready'] = true;
          }
      }
        elseif ($this->clients[$id]['state'] === 2)
      {
          // DATA reading state
          // not ready unless you get a \r\n.\r\n at the end
          $len = strlen($this->clients[$id]['read_buffer']);

          if (($len > MESSAGE_MAX_SIZE_GOOD) || (($len > 4) && (strpos($this->clients[$id]['read_buffer'], "\r\n.\r\n", $len - 5)) !== false) )
          {
               $this->clients[$id]['read_buffer_ready'] = true; // finished
               $this->clients[$id]['read_buffer'] = substr($this->clients[$id]['read_buffer'], 0, $len - 5);
          }
      }

      $this->process_smtp($id);

      if (strlen($this->clients[$id]['response']) > 0)
      {
          event_buffer_write($buffer, $this->clients[$id]['response']);
          $this->add_response($id, null);
      }
  }

  function process_smtp($client_id)
  {

    switch ($this->clients[$client_id]['state'])
    {
      case 0:

        $this->add_response($client_id, '220 ' . SERVICE_HOST_NAME .
        ' SMTP Oopzyd #' . $client_id . ' (' . sizeof($this->clients) . ') ' . gmdate('r'));
        $this->clients[$client_id]['state'] = 1;

        break;
      case 1:

        $input = $this->read_line( $client_id);

        if ($input)
        {

          if (stripos($input, 'HELO') !== false)
          {
                $temp = explode(' ', $input);
                $this->clients[$client_id]['helo'] = trim($temp[1]);
                $this->add_response($client_id, '250 ' . SERVICE_HOST_NAME . ' Hello ' . trim($temp[1]) .
                ' [' . $this->clients[$client_id]['address'] . '], got some mail?');
          }
            elseif (stripos($input, 'EHLO') !== false)
          {
                $temp = explode(' ', $input);

                $this->clients[$client_id]['helo'] = trim($temp[1]);
                $this->add_response($client_id, '250-' . SERVICE_HOST_NAME . ' Hello ' . trim($temp[1]) .
                '[' . $this->clients[$client_id]['address'] . ']' . "\r\n" . "250-SIZE  TOO BIG\r\n" . "250 HELP");
          }
            elseif (stripos($input, 'MAIL FROM:') === 0)
          {
                $this->clients[$client_id]['mail_from'] = substr($input, 10);
                $this->add_response($client_id, '250 Ok');
          }
            elseif ((stripos($input, 'RCPT TO:') !== false))
          {

                $email = $this->extract_email(substr($input, 8));

                // do not allow CC, RCPT TO is allowed only once
                if (empty($this->clients[$client_id]['rcpt_to']) && ($email))
                {
                      $this->clients[$client_id]['rcpt_to'] = $email;
                      $this->add_response($client_id, '250 Accepted');
                }
                 else
                {
                      $this->log_line('mailbox unavailable[' . array_pop(explode('@', $input)) . '] input:' .
                          $input, 1);
                          // do not let CC.
                          $this->kill_client($client_id,
                          '550 Requested action not taken: mailbox unavailable');
                }

          }
            elseif (stripos($input, 'DATA') !== false)
          {
                $this->add_response($client_id,
                '354 Enter message, ending with "." on a line by itself');
                $this->clients[$client_id]['state'] = 2;

                $this->clients[$client_id]['read_buffer'] = '';
          }
            elseif (stripos($input, 'QUIT') !== false)
          {
                $this->kill_client($client_id, '221 Bye');
                continue;

          }
            elseif (stripos($input, 'NOOP') !== false)
          {

                $this->log_line("client NOOP from client", 1);
                $this->add_response($client_id, '250 OK');
          }
            elseif (stripos($input, 'RSET') !== false)
          {

                $this->clients[$client_id]['read_buffer'] = '';
                $this->clients[$client_id]['rcpt_to'] = '';
                $this->clients[$client_id]['mail_from'] = '';
                $this->add_response($client_id, '250 OK');

          }
            else
          {
                $this>log_line('[' . $client_id . ']unrecoginized cmd:' . $input, 1);
                $this->add_response($client_id, '500 unrecognized command');
                $this->$clients[$client_id]['error_c']++;
                if (($this->clients[$client_id]['error_c'] > 3))
                {
                    $this->kill_client($client_id, '500 Too many unrecognized commands');
                    continue;
                }
          }
        }
        break;
      case 2:

        $input = $this->read_line($client_id);

        if ($input)
        {

              list($id, $to) = $this->save_email($input, $this->clients[$client_id]['rcpt_to'], $this->clients[$client_id]['helo'],
                  $this->clients[$client_id]['address']);

          if ($id)
          {
                $this->add_response($client_id, '250 OK : queued as ' . $id);
                // put client back to state 1
                $this->clients[$client_id]['state'] = 1;
                $this->clients[$client_id]['read_buffer'] = '';
                $this->clients[$client_id]['error_c'] = 0;
          }
           else
          {

                // The email didn't save properly, usualy because it was in
                // an incorrect mime format or bad recipient

                $this->kill_client($client_id, "554 Transaction failed (".strlen($input).") ".
                    $this->clients[$client_id]['rcpt_to']." !$id! \{" . $this->GM_ERROR . " \} ");
                    $this->log_line("Message for client: [$client_id] failed to [$to] {"
                    . $this->clients[$client_id]['rcpt_to'] . "}, told client to exit.",
                    1);
          }
          continue;
        }
        break;
    }
  }

  function log_line($l, $log_level = 2)
  {
      $l = trim($l);
      echo $l . "\n";
      return;
  }

  function extract_email($str)
  {
      $arr = mailparse_rfc822_parse_addresses($str);

      foreach ($arr as $item)
      {
          $hostname = '';
          $pos = strpos($item['address'], '@');
          if ($pos)
          {
              $hostname = substr($item['address'], $pos + 1);
          }
          if (in_array(strtolower($hostname), $this->ALLOWED_HOSTS))
          {
              return strtolower($item['address']);
          }
      }
      return false;
  }

  function add_response($client_id, $str = null)
  {
      if (strlen($str) > 0)
      {
          if (substr($str, -2) !== "\r\n")
          {
            $str .= "\r\n";
          }

          $this->clients[$client_id]['response'] .= $str;

      }
        elseif ($str === null)
      {
        // clear
        $this->clients[$client_id]['response'] = null;
      }
  }

  /**
   * @param $client_id
   * @param $clients
   * @param null|string $msg message to the client. Do not kill untill all is sent
   */
  function kill_client($client_id, $msg = null)
  {
      if (isset($this->clients[$client_id]))
      {

          $this->clients[$client_id]['kill_time'] = time();

          if (strlen($msg) > 0)
          {
             $this->add_response($client_id, $msg);
          }
      }
  }


  function read_line($client_id)
  {
      if ($this->clients[$client_id]['read_buffer_ready'])
      {
          // clear the buffer and return the data
          $buf = $this->clients[$client_id]['read_buffer'];
          $this->clients[$client_id]['read_buffer'] = '';
          $this->clients[$client_id]['read_buffer_ready'] = false;
          return $buf;
      }
      return false;
  }

  function save_email($email, $rcpt_to, $helo, $helo_ip)
  {
    if(preg_match('/name=[^>]*\.(.+)/', $email))
    {
      $this->GM_ERROR = 'ERROR: ments not supported';
      return array(false, false);
    }

    $this->log_line("rcp_to $rcpt_to", 1);

    list($mail_user, $mail_host) = explode('@', $rcpt_to);

    if (!in_array($mail_host, $this->ALLOWED_HOSTS))
    {
      $this->GM_ERROR = " -$mail_host- not in allowed hosts:".$mail_host." ";
      return array(false, false);
    }

    $to = $mail_user . '@' . PRIMARY_MAIL_HOST; // change it to the primary host


    $key = $to ;

    $box_quality = $this->boxquality($to);

    if($box_quality < 2)
    {
        $ttl = REDIS_TTL_BAD;
        $mmax = MESSAGE_MAX_SIZE_BAD;
    }
    elseif($box_quality > 4)
    {
        $ttl = REDIS_TTL_GOOD;
        $mmax = MESSAGE_MAX_SIZE_GOOD;
    }
    else
    {
        $ttl = REDIS_TTL_OK;
        $mmax = MESSAGE_MAX_SIZE_BAD;
    }

    if( strlen($email) >= $mmax)
    {
      $this->GM_ERROR = 'Message too large ' . $mmax . ' bytes max';
      return array(false, false);
    }


    $debug = array('bq'=>$box_quality, 'mm'=>$mmax);

    $sdata = serialize(array('email'=>$email, 'debug'=>$debug));

    $res = $this->to_redis($to,$sdata, $ttl);

    if(!$res)
    {
      return array(false, false);
    }

    return array($res, $to);
  }

  public function boxquality($box)
  {
      $quality = 0;
      //if the password length is less than 6, return message.
      if (strlen($box) < 6)
      {
          return $quality;
      }

      if(!filter_var($box . '@oopzy.com', FILTER_VALIDATE_EMAIL))
      {
          return 0;
      }

      if (strlen($box) > 6)
      {
          $quality += 1;
      }

      //if password contains both lower and uppercase characters, increase strength str
      if (preg_match('/([a-z].*[A-Z])|([A-Z].*[a-z])/',$box))
      {
          $quality += 1;
      }

      //if it has numbers and characters, increase strength str
      if (preg_match('/([a-zA-Z])/',$box) && preg_match('/([0-9])/',$box))
      {
          $quality += 1;
      }

      //if it has one special character, increase strength str
      if (preg_match('/([!,%,&,#,$,^,*,?,_,~])/',$box))
      {
          $quality += 1;
      }

      //if it has two special characters, increase strength str
      if (preg_match('/(.*[!,%,&,#,$,^,*,?,_,~].*[!,%,&,#,$,^,*,?,_,~])/',$box))
      {
          $quality += 1;
      }

      return $quality;

  }

  function to_redis($to, $data, $ttl)
  {
    try{

      $redisdb = new Redis();

      //add db shard switch based on a key

      $redisdb->connect( REDIS_HOST, REDIS_PORT );

      if(false == $redisdb)
      {
        $this->GM_ERROR = 'ERROR: Could not get a redis connection';
        return false;
      }

      $key = $to . ':' .time();

      $res = $redisdb->setex($key, $ttl, $data );

      if($res)
      {
        $this->log_line("stored $to", 1);
        return md5($key);
      }
      else
      {
        $this->log_line("failed storing $to", 1);
        return false;
      }

    }
    catch(Exception $e)
    {
      $this->GM_ERROR = 'ERROR:' . $e->getMessage() ;
      return false;
    }
  }
}