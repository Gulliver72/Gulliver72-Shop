<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2016 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2016  Bigware LTD
  
  $Id: class Password.php 4200 2017-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

/*

  Funktionen in /class/this/Password.php verlagert
  
  Funktionsaufruf bereits umgestellt
  
*/  
/*   
  function go_validate_password($plain, $encrypted) {
    if (go_not_null($plain) && go_not_null($encrypted)) { 
      $stack = explode(':', $encrypted);
      if (sizeof($stack) != 2) return false;
      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }
    return false;
  }  
  function go_encrypt_password($plain) {
    $password = '';
    for ($i=0; $i<10; $i++) {
      $password .= go_rand();
    }
    $salt = substr(md5($password), 0, 2);
    $password = md5($salt . $plain) . ':' . $salt;
    return $password;
  }
*/
?>
