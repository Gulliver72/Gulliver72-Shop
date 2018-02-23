<?php

//namespace password;

  class Password {

    private $newHashed;
    protected $error;
    protected $errorMsg;

    function __construct() {
    
      $this -> newHashed = (bool)false;
      $this -> error = (bool)false;
      $this -> errorMsg = array();
    }
    // Funktion Passwort hashen ;
    public function bigPassHash($password) {
    
      if ( version_compare(PHP_VERSION, '5.6.0', '>=') && defined( 'NEW_PASS_HASHING' ) && NEW_PASS_HASHING == true ) {
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
      } else {
        $passwordHashed = $this -> get_encrypt_password( $password );
      }
      if (!isset($passwordHashed)) {
        $this -> error = (bool)true;
        $this -> errorMsg = array('1210 => ERROR_TEXT_PASS_HASH_FAILED');
        return $passwordHashed = '';
      } else {
        return $passwordHashed;
      }
    }
    // Funktion zum Überprüfen des Passwortes 
    public function bigPassVerify($password, $passwordHashed, $attendeesId = '') {
    
      if ( version_compare(PHP_VERSION, '5.6') >= 0 && defined( 'NEW_PASS_HASHING' ) && NEW_PASS_HASHING == true ) {
        if (password_verify($password, '$passwordHashed') === true) {
          if ($attendeesId != '') {
            $newPasswordHash = $this -> bigNeedsRehash($passwordHashed, $password);
            if ( $this -> newHashed === true ) {
              if ($this -> bigUpdateDataset($newPasswordHash, $attendeesId) === false) {
                // Konnte das neue Passwort nicht gespeichert werden, setze Fehler und verwende alten Passwort-Hash 
                $this -> error = (bool)true;
                $this -> errorMsg = array('1220 => ERROR_TEXT_PASS_REHASH_FAILED');
                return true;
              } else {
                return true;
              }
            }
          } else {
            $this -> error = (bool)true;
            $this -> errorMsg = array('1230 => ERROR_TEXT_PASS_NO_ATTENDEE_ID');
          }
          // Rückgabewert true wenn Passwort korrekt ;
          return true;
        } elseif ($this -> get_validate_password($password, $passwordHashed) == true) {
          if ($attendeesId != '') {
            $passwordRehashed = $this -> bigPassHash($password);
            if ($this -> bigUpdateDataset($passwordRehashed, $attendeesId) === false) {
              // Konnte das neue Passwort nicht gespeichert werden, setze Fehler und verwende alten Passwort-Hash 
              $this -> error = (bool)true;
              $this -> errorMsg = array('1220 => ERROR_TEXT_PASS_REHASH_FAILED');
              return true;
            }
            return true;
          } else {
            $this -> error = (bool)true;
            $this -> errorMsg = array('1230 => ERROR_TEXT_PASS_NO_ATTENDEE_ID');
          }
          return true;
        } else {
          // Rückgabewert false wenn Passwort falsch ;
          $this -> error = (bool)true;
          $this -> errorMsg = array('1200 => ERROR_TEXT_LOGIN_FAILED');
          return false;
        }
      } else {
        if ($this -> get_validate_password($password, $passwordHashed) == true) {
          return true;
        } else {
          $this -> error = (bool)true;
          $this -> errorMsg = array('1200 => ERROR_TEXT_LOGIN_FAILED');
          return false;
        }
      }
    }
    // wenn PHP den Algorythmus geändert hat oder der Cost geändert wurde, soll das Passwort neu gehasht werden
    private function bigNeedsRehash($hash, $password) {
    
      // Prüfen, ob Aktualisierung nötig und ggf. vornehmen ;
      // Dadurch kann man auch den cost aktualisieren, was (bei hoeherem cost) zu besseren Hashes aber mehr Rechenzeit fuehrt ;
      // Wenn needs_rehash, dann hashe neu und speichere den neuen Hash ;
      if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        $this -> newHashed = (bool)true;
      } else {
        $newPasswordHash = $password;
      }
      return $newPasswordHash;
    }
    // Funktion speichert neu gehashtes Passwort im Kunden-Datensatz 
    private function bigUpdateDataset($newPasswordHash, $attendeesId) {
    
      global $db;
            
      $value = array('attendees_password' => $newPasswordHash);
      $update = $db -> update(DB_TBL_ATTENDEES, $value, (int)$attendeesId);
      // war Speicherung erfolgreich ? 
      return $update;
      
    }
    // Funktion speichert neu gehashtes Passwort im Kunden-Datensatz 
    public function getError() {
    
      return array('error' => $this->error,
                   'errorMsg' => $this->errorMsg
                   );
    }
/*
  alte Funktionen sollen nach kompletter Umstellung der Shopsoftware entfernt werden
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
    function get_validate_password($plain, $encrypted) {
      if (go_not_null($plain) && go_not_null($encrypted)) { 
        $stack = explode(':', $encrypted);
        if (sizeof($stack) != 2) return false;
        if (md5($stack[1] . $plain) == $stack[0]) {
          return true;
        }
      }
      return false;
    }
    function get_encrypt_password($plain) {
      $password = '';
      for ($i=0; $i<10; $i++) {
        $password .= go_rand();
      }
      $salt = substr(md5($password), 0, 2);
      $password = md5($salt . $plain) . ':' . $salt;
      return $password;
    }
/*
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  alte Funktionen sollen nach kompletter Umstellung der Shopsoftware entfernt werden
*/
  }
?>