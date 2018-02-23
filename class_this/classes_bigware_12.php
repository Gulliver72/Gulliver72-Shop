<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015
  
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015	Bigware LTD

  Copyrightvermerke duerfen nicht entfernt werden.
  ------------------------------------------------------------------------
  Dieses Programm ist freie Software. Sie koennen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation
  veroeffentlicht, weitergeben und/oder modifizieren, entweder gemaess Version 2 
  der Lizenz oder (nach Ihrer Option) jeder spaeteren Version.
  Die Veroeffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen
  von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die
  implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FUER EINEN
  BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
  
  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
  Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

  Infos:
  ------------------------------------------------------------------------
  Der Bigware Shop wurde vor vielen Jahren bereits aus dem bekannten Shopsystem osCommerce
  weiter- und neuentwickelt.
  Der Bigware Shop legt im hohen Masse Wert auf Bedienerfreundlichkeit, beinhaltet eine leichte
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine grosse 
  Community, die bei Problemen weiterhelfen kann.
  
  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.
 
  Hilfe erhalten Sie im Forum auf www.bigware.de 
  
  -----------------------------------------------------------------------
  
 ##################################################################################




*/
?>
<?php
  class mime {
    var $_encoding;
    var $_subparts;
    var $_encoded;
    var $_headers;
    var $_body;
    function __construct($body, $params = '') {
      $this->mime($body, $params = '');
    }
 function mime($body, $params = '') {
      if ($params == '') $params = array(); 
      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }
      reset($params);
      while (list($key, $value) = each($params)) {
        switch ($key) {
          case 'content_type':
            $headers['Content-Type'] = $value . (isset($charset) ? '; charset="' . $charset . '"' : '');
            break;
          case 'encoding':
            $this->_encoding = $value;
            $headers['Content-Transfer-Encoding'] = $value;
            break;
          case 'cid':
            $headers['Content-ID'] = '<' . $value . '>';
            break;
          case 'disposition':
            $headers['Content-Disposition'] = $value . (isset($dfilename) ? '; filename="' . $dfilename . '"' : '');
            break;
          case 'dfilename':
            if (isset($headers['Content-Disposition'])) {
              $headers['Content-Disposition'] .= '; filename="' . $value . '"';
            } else {
              $dfilename = $value;
            }
            break;
          case 'description':
            $headers['Content-Description'] = $value;
            break;
          case 'charset':
            if (isset($headers['Content-Type'])) {
              $headers['Content-Type'] .= '; charset="' . $value . '"';
            } else {
              $charset = $value;
            }
            break;
        }
      } 
      if (!isset($_headers['Content-Type'])) {
        $_headers['Content-Type'] = 'text/plain';
      } 
      $this->_encoded = array();
  
      $this->_headers = $headers;
      $this->_body = $body;
    }
 function encode() {
  
      $encoded = $this->_encoded;
      if (go_not_null($this->_subparts)) {
        $boundary = '=_' . md5(uniqid(go_rand()) . microtime());
        $this->_headers['Content-Type'] .= ';' . $this->lf . chr(9) . 'boundary="' . $boundary . '"'; 
        for ($i=0; $i<count($this->_subparts); $i++) {
          $headers = array();
  
          $_subparts = $this->_subparts[$i];
          $tmp = $_subparts->encode();
          reset($tmp['headers']);
          while (list($key, $value) = each($tmp['headers'])) {
            $headers[] = $key . ': ' . $value;
          }
          $subparts[] = implode($this->lf, $headers) . $this->lf . $this->lf . $tmp['body'];
        }
        $encoded['body'] = '--' . $boundary . $this->lf . implode('--' . $boundary . $this->lf, $subparts) . '--' . $boundary.'--' . $this->lf;
      } else {
        $encoded['body'] = $this->_getEncodedData($this->_body, $this->_encoding) . $this->lf;
      } 
  
      $encoded['headers'] = $this->_headers;
      return $encoded;
    }
   
    function addSubPart($body, $params) {
      $this->_subparts[] = new mime($body, $params);
      return $this->_subparts[count($this->_subparts) - 1];
    }
 function _getEncodedData($data, $encoding) {
      switch ($encoding) {
       case '7bit':
         return $data;
         break;
       case 'quoted-printable':
         return $this->_quotedPrintableEncode(str_replace('\"','"',$data));
         break;
       case 'base64':
         return rtrim(chunk_split(base64_encode($data), 76, $this->lf));
         break;
      }
    }
 function _quotedPrintableEncode($input , $line_max = 76) {
      $lines = preg_split("/\r\n|\r|\n/", $input);
      $eol = $this->lf;
      $escape = '=';
      $output = '';
      while (list(, $line) = each($lines)) {
        $linlen = strlen($line);
        $newline = '';
        for ($i = 0; $i < $linlen; $i++) {
          $char = substr($line, $i, 1);
          $dec = ord($char); 
          if ( ($dec == 32) && ($i == ($linlen - 1)) ) {
            $char = '=20';
          } elseif ($dec == 9) { 
          } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
            $char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
          } 
          if ((strlen($newline) + strlen($char)) >= $line_max) { 
            $output .= $newline . $escape . $eol;
            $newline = '';
          }
          $newline .= $char;
        }
        $output .= $newline . $eol;
      } 
      $output = substr($output, 0, -1 * strlen($eol));
      return $output;
    }
  }
?>
