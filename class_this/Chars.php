<?php

class Chars {

  // Angabe der Stringlänge ($stringlength) 
  private static $stringLength = TYPE_IN_PASSWORD_MIN_LENGTH;
  // Menge der einzubindenden Sonderzeichen ($numNonAlpha) 
  private static $numNonAlpha = NEW_PASS_NUMNONALPHA;
  // Menge der einzubindenden Zahlen ($numNumberChars) 
  private static $numNumberChars = NEW_PASS_NUMNUMBERCHARS;
  // Nutzung von Großbuchstaben ($useCapitalLetter) 
  private static $useCapitalLetter = NEW_PASS_USECAPITALLETTER;
  // verwendete Ziffern festlegen 
  private static $numberChars = '123456789';
  // verwendete Sonderzeichen festlegen 
  private static $specialChars = '!$%&=?*-:;.,+~@_';
  // verwendete Buchstaben festlegen 
  private static $secureChars = 'abcdefghjkmnpqrstuvwxyz';

  protected function __construct() {

  }

  // Die Funktion erzeugt aus den Parametern einen zufälligen String 
  // mit der festgelegten Menge an Zeichen ($stringLength) und den einzubindenden Sonderzeichen und Zahlen. 
  public static function generateRandomString ( $stack = '' ) {
    
    $stack = '';
    // Stack für String-Erzeugung füllen 
    $stack = self::$secureChars;
    // sollen Großbuchstaben verwendet werden ? 
    if ( self::$useCapitalLetter == true ) {
      $stack .= strtoupper ( self::$secureChars );
    }
    // Anzahl der Buchstaben bestimmen, die in den String sollen 
    $count = self::$stringLength - self::$numNonAlpha - self::$numNumberChars;
    // Buchstaben durchwürfeln 
    $temp = str_shuffle ( $stack );
    // Stack für String-Erzeugung füllen 
    $stack = substr ( $temp , 0 , $count );
    // sollen Sonderzeichen verwendet werden ? 
    if ( self::$numNonAlpha > 0 ) {
      // Sonderzeichen durchwürfeln 
      $temp = str_shuffle ( self::$specialChars );
      // Sonderzeichen dem String-Stack hinzufügen 
      $stack .= substr ( $temp , 0 , self::$numNonAlpha );
    }
    // sollen Ziffern verwendet werden ? 
    if ( self::$numNumberChars > 0 ) {
      // Ziffern durchwürfeln 
      $temp = str_shuffle ( self::$numberChars );
      // Ziffern dem String-Stack hinzufügen 
      $stack .= substr ( $temp , 0 , self::$numNumberChars );
    }
    // Stack nochmal durchwürfeln 
    $stack = str_shuffle ( $stack );
    // Rückgabe des erzeugten Strings 
    return $stack;
  }
}
?>