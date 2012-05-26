<?php
 error_reporting(E_ALL);

 ini_set('display_errors', true);
 ini_set('html_errors', false);

 $debug = true; // set true to print debug information during execution

 /*
  *
  */
 function debugMsg($msg)
 {
    echo "DEBUG MESSAGE: {$msg}<br>\n";
 }

 /*
  *
  */
 function error($errMsg)
 {
    echo "ERROR: {$errMsg}<br>\n";
    exit();
 }

 /*
  *
  */
 function MySQLerror($errMsg, $MySQLerrNo, $MySQLerrMsg)
 {
    echo   "MySQL ERROR: $errMsg  "
         , "MySQL error number: $MySQLerrNo  "
         , "MySQL error message: $MySQLerrMsg<br>\n";
    exit();
 }

 /*
  *
  */
 function is_integerString($var)
 {
    if (ctype_digit($var))
      return true;
    else
    {
       if ($var[0] == '-')
       {
          $var[0] = '0';

          if (ctype_digit($var))
            return true;
          else
            return false;
       }
       else
         return false;
    }
 }
?>
