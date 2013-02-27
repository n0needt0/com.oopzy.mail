<?php
Class messageparser
{

  function __construct()
  {
      $this->$iconv_error = false;
  }


  function iconv_error_handler($errno, $errstr, $errfile, $errline)
  {
      $this->iconv_error = true;
  }

  /**
   * mail_body_decode()
   * Decode the mail body to binary. Then convert to UTF-8 if not already
   * @param string $str string to decode
   * @param string $encoding_type eg. 'quoted-printable' or 'base64'
   * @param string $charset and of the charsets supported by iconv()
   * @return string decoded message in a string of UTF-8
   */

  function mail_body_decode($str, $encoding_type, $charset = 'UTF-8')
  {
      $iconv_error = false;

      if ($encoding_type == 'base64') {
          $str = base64_decode($str);
      } elseif ($encoding_type == 'quoted-printable') {
          $str = quoted_printable_decode($str);
      }
      $charset = strtolower($charset);
      $charset=preg_replace("/[-:.\/\\\]/", '-', strtolower($charset));
      // Fix charset
      // borrowed from http://squirrelmail.svn.sourceforge.net/viewvc/squirrelmail/trunk/squirrelmail/include/languages.php?revision=13765&view=markup
      // OE ks_c_5601_1987 > cp949
      $charset=str_replace('ks_c_5601_1987','cp949',$charset);
      // Moz x-euc-tw > euc-tw
      $charset=str_replace('x_euc','euc',$charset);
      // Moz x-windows-949 > cp949
      $charset=str_replace('x_windows_','cp',$charset);
      // windows-125x and cp125x charsets
      $charset=str_replace('windows_','cp',$charset);
      // ibm > cp
      $charset=str_replace('ibm','cp',$charset);
      // iso-8859-8-i -> iso-8859-8
      // use same cycle until they'll find differences
      $charset=str_replace('iso_8859_8_i','iso_8859_8',$charset);

      if (strtoupper($charset) != 'UTF-8')
      {
          set_error_handler("iconv_error_handler");
          $str = @iconv(strtoupper($charset), 'UTF-8', $str);
          if ($this->iconv_error)
          {
              $this->iconv_error = false;
              // there was iconv error
              // attempt mbstring concersion
              $str = mb_convert_encoding($str, 'UTF-8', $charset);
          }
          restore_error_handler();
      }

      return $str;

  }

  /**
   * extract_email()
   * Extract an email address from a header string
   * @param string $str
   * @return string email address, false if none found
   */
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
          if (in_array(strtolower($hostname), $GLOBALS['allowed_hosts']))
          {
              return strtolower($item['address']);
          }
      }
      return false;

  }

  function extract_from_email($str)
  {

      $arr = mailparse_rfc822_parse_addresses($str);
      if (!empty($arr))
      {
          return $arr[0]['address'];
      }
      return false;

  }


  /**
   * save_email()
   * Accepts an email received from a client during the DATA command.
   * This email is processed, the recipient host is verified, the body is
   * decoded, then saved to the database.
   *
   * @param string $email
   * @return array, with the following elements array($hash, $recipient)
   * where the $hash is a unique id for this email.
   */
  function parse($email)
  {
      $email .= "\r\n";

      list($to, $from, $subject) = $this->get_email_headers($email, array('To', 'From', 'Subject'));

      $rcpt_to = $this->extract_email($rcpt_to);

      $from = extract_from_email($from);

      list($mail_user, $mail_host) = explode('@', $to);

      $id = ''; // generated message id

      if(preg_match('/name=[^>]*\.(.+)/', $email))
      {
           return false;
      }

      if (in_array($mail_host, $GLOBALS['allowed_hosts']))
      {

          $data = array(
              'time' =>time(),
              'from'=>$from,
              'subject'=>$subject,
              'mail'=>$email
          );

      }

      return $data;
  }

  function get_email_headers($email, $header_names = array())
  {
      $ret = array();
      $pos = strpos($email, "\r\n\r\n");
      if (!$pos)
      {
          // incorrectly formatted email with missing \r?
          $pos = strpos($email, "\n\n");
      }

      $headers_str = substr($email, 0, $pos);

      $headers = iconv_mime_decode_headers($headers_str, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'utf-8');

      foreach($header_names as $i =>$name)
      {
          if (is_array($headers[$name]))
          {
              $headers[$name] = implode (', ', $headers[$name]);
          }

          if ((strpos($headers[$name], '=?')===0))
          {
              // workaround if iconv_mime_decode_headers() - sometimes more than one to decode
              if (preg_match_all('#=\?(.+?)\?([QB])\?(.+?)\?=#i', $headers[$name], $matches))
              {
                  $decoded_str = '';
                  foreach($matches[1] as $index => $encoding)
                  {
                      //if ($matc)
                      if (strtolower($matches[2][$index])==='b')
                      {
                          $decoded_str =
                              mail_body_decode($matches[3][$index], 'base64', $encoding);
                      }
                        elseif (strtolower($matches[2][$index])==='q')
                      {
                          $decoded_str =
                              mail_body_decode($matches[3][$index], 'quoted-printable',  $encoding);
                      }

                      if (!empty($decoded_str))
                      {
                          $headers[$name] = str_replace($matches[0][$index], $decoded_str, $headers[$name]);
                      }
                  }
              }
          }
          $ret[$i] = $headers[$name];

          }
          return $ret;
    }
}
