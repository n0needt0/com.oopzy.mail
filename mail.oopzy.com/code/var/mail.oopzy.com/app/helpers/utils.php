<?php
Class utils
{
  public function json_indent($json) {

          $result      = '';
          $pos         = 0;
          $strLen      = strlen($json);
          $indentStr   = '  ';
          $newLine     = "\n";
          $prevChar    = '';
          $outOfQuotes = true;

          for ($i=0; $i<=$strLen; $i++) {

                  // Grab the next character in the string.
                  $char = substr($json, $i, 1);

                  // Are we inside a quoted string?
                  if ($char == '"' && $prevChar != '\\') {
                          $outOfQuotes = !$outOfQuotes;

                  // If this character is the end of an element,
                  // output a new line and indent the next line.
                  } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                          $result .= $newLine;
                          $pos --;
                          for ($j=0; $j<$pos; $j++) {
                                  $result .= $indentStr;
                          }
                  }

                  // Add the character to the result string.
                  $result .= $char;

                  // If the last character was the beginning of an element,
                  // output a new line and indent the next line.
                  if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                          $result .= $newLine;
                          if ($char == '{' || $char == '[') {
                                  $pos ++;
                          }

                          for ($j = 0; $j < $pos; $j++) {
                                  $result .= $indentStr;
                          }
                  }

                  $prevChar = $char;
          }

          return $result;
      }

      public function box_quality($box)
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

      public function error_echo_die($error)
      {
           echo json_encode(array('error'=>$error));
           die;
      }
}
