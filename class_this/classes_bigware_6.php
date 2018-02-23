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
  class email {
    var $html;
    var $text;
    var $output;
    var $html_text;
    var $html_pictures;
    var $picture_types;
    var $build_parameter;
    var $attachments;
    var $headers;
    function __construct($headers = '') {
      $this->email($headers);
    }
    function email($headers = '') {
      if ($headers == '') $headers = array();
      $this->html_pictures = array();
      $this->headers = array();
      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }
 $this->picture_types = array('gif' => 'picture/gif',
                                 'jpg' => 'picture/jpeg',
                                 'jpeg' => 'picture/jpeg',
                                 'jpe' => 'picture/jpeg',
                                 'bmp' => 'picture/bmp',
                                 'png' => 'picture/png',
                                 'tif' => 'picture/tiff',
                                 'tiff' => 'picture/tiff',
                                 'swf' => 'application/x-shockwave-flash');
      $this->build_parameter['html_encoding'] = 'quoted-printable';
      $this->build_parameter['text_encoding'] = '7bit';
      $this->build_parameter['html_charset'] = constant('CHARSET');
      $this->build_parameter['text_charset'] = constant('CHARSET');
      $this->build_parameter['text_wrap'] = 998;
 $this->headers[] = 'MIME-Version: 1.0';
      reset($headers);
      while (list(,$value) = each($headers)) {
        if (go_not_null($value)) {
          $this->headers[] = $value;
        }
      }
    }
 function get_file($filename) {
      $return = '';
      if ($fp = fopen($filename, 'rb')) {
        while (!feof($fp)) {
          $return .= fread($fp, 1024);
        }
        fclose($fp);
        return $return;
      } else {
        return false;
      }
    }
 function find_html_pictures($pictures_dir) { 
      while (list($key, ) = each($this->picture_types)) {
        $extensions[] = $key;
      }
      preg_match_all('/"([^"]+\.(' . implode('|', $extensions).'))"/Ui', $this->html, $pictures);
      for ($i=0; $i<count($pictures[1]); $i++) {
        if (file_exists($pictures_dir . $pictures[1][$i])) {
          $html_pictures[] = $pictures[1][$i];
          $this->html = str_replace($pictures[1][$i], basename($pictures[1][$i]), $this->html);
        }
      }
      if (go_not_null($html_pictures)) { 
        $html_pictures = array_unique($html_pictures);
        sort($html_pictures);
        for ($i=0; $i<count($html_pictures); $i++) {
          if ($picture = $this->get_file($pictures_dir . $html_pictures[$i])) {
            $content_type = $this->picture_types[substr($html_pictures[$i], strrpos($html_pictures[$i], '.') + 1)];
            $this->add_html_picture($picture, basename($html_pictures[$i]), $content_type);
          }
        }
      }
    }
 function add_text($text = '') {
      $this->text = go_convert_linefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);
    }
 function add_html($html, $text = NULL, $pictures_dir = NULL) {
      $this->html = go_convert_linefeeds(array("\r\n", "\n", "\r"), '<br>', $html);
      $this->html_text = go_convert_linefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);
      if (isset($pictures_dir)) $this->find_html_pictures($pictures_dir);
    }
 function add_html_picture($file, $name = '', $c_type='application/octet-stream') {
      $this->html_pictures[] = array('body' => $file,
                                   'name' => $name,
                                   'c_type' => $c_type,
                                   'cid' => md5(uniqid(time())));
    }
 function add_attachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64') {
      $this->attachments[] = array('body' => $file,
                                   'name' => $name,
                                   'c_type' => $c_type,
                                   'encoding' => $encoding);
    }
   
    function add_text_part(&$obj, $text) {
      $params['content_type'] = 'text/plain';
      $params['encoding'] = $this->build_parameter['text_encoding'];
      $params['charset'] = $this->build_parameter['text_charset'];
      if (is_object($obj)) {
        return $obj->addSubpart($text, $params);
      } else {
        return new mime($text, $params);
      }
    }
   
    function add_html_part(&$obj) {
      $params['content_type'] = 'text/html';
      $params['encoding'] = $this->build_parameter['html_encoding'];
      $params['charset'] = $this->build_parameter['html_charset'];
      if (is_object($obj)) {
        return $obj->addSubpart($this->html, $params);
      } else {
        return new mime($this->html, $params);
      }
    }
   
    function add_mixed_part() {
      $params['content_type'] = 'multipart/mixed';
      return new mime('', $params);
    }
   
    function add_alternative_part(&$obj) {
      $params['content_type'] = 'multipart/alternative';
      if (is_object($obj)) {
        return $obj->addSubpart('', $params);
      } else {
        return new mime('', $params);
      }
    }
   
    function add_related_part(&$obj) {
      $params['content_type'] = 'multipart/related';
      if (is_object($obj)) {
        return $obj->addSubpart('', $params);
      } else {
        return new mime('', $params);
      }
    }
   
    function add_html_picture_part(&$obj, $value) {
      $params['content_type'] = $value['c_type'];
      $params['encoding'] = 'base64';
      $params['disposition'] = 'inline';
      $params['dfilename'] = $value['name'];
      $params['cid'] = $value['cid'];
      $obj->addSubpart($value['body'], $params);
    }
   
    function add_attachment_part(&$obj, $value) {
      $params['content_type'] = $value['c_type'];
      $params['encoding'] = $value['encoding'];
      $params['disposition'] = 'attachment';
      $params['dfilename'] = $value['name'];
      $obj->addSubpart($value['body'], $params);
    }
   
    function build_message($params = '') {
      if ($params == '') $params = array();
      if (count($params) > 0) {
        reset($params);
        while(list($key, $value) = each($params)) {
          $this->build_parameter[$key] = $value;
        }
      }
      if (go_not_null($this->html_pictures)) {
        reset($this->html_pictures);
        while (list(,$value) = each($this->html_pictures)) {
          $this->html = str_replace($value['name'], 'cid:' . $value['cid'], $this->html);
        }
      }
      $null = NULL;
      $attachments = ((go_not_null($this->attachments)) ? true : false);
      $html_pictures = ((go_not_null($this->html_pictures)) ? true : false);
      $html = ((go_not_null($this->html)) ? true : false);
      $text = ((go_not_null($this->text)) ? true : false);
      switch (true) {
        case (($text == true) && ($attachments == false)):
  
          $message = $this->add_text_part($null, $this->text);
          break;
        case (($text == false) && ($attachments == true) && ($html == false)):
  
          $message = $this->add_mixed_part();
          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($text == true) && ($attachments == true)):
  
          $message = $this->add_mixed_part();
          $this->add_text_part($message, $this->text);
          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($html == true) && ($attachments == false) && ($html_pictures == false)):
          if (go_not_null($this->html_text)) {
  
            $message = $this->add_alternative_part($null);
            $this->add_text_part($message, $this->html_text);
            $this->add_html_part($message);
          } else {
  
            $message = $this->add_html_part($null);
          }
          break;
        case (($html == true) && ($attachments == false) && ($html_pictures == true)):
          if (go_not_null($this->html_text)) {
  
            $message = $this->add_alternative_part($null);
            $this->add_text_part($message, $this->html_text);
  
            $related = $this->add_related_part($message);
          } else {
   
            $message = $this->add_related_part($null);
            $related = $message;
          }
          $this->add_html_part($related);
          for ($i=0; $i<count($this->html_pictures); $i++) {
            $this->add_html_picture_part($related, $this->html_pictures[$i]);
          }
          break;
        case (($html == true) && ($attachments == true) && ($html_pictures == false)):
  
          $message = $this->add_mixed_part();
          if (go_not_null($this->html_text)) {
  
            $alt = $this->add_alternative_part($message);
            $this->add_text_part($alt, $this->html_text);
            $this->add_html_part($alt);
          } else {
            $this->add_html_part($message);
          }
          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($html == true) && ($attachments == true) && ($html_pictures == true)):
  
          $message = $this->add_mixed_part();
          if (go_not_null($this->html_text)) {
  
            $alt = $this->add_alternative_part($message);
            $this->add_text_part($alt, $this->html_text);
  
            $rel = $this->add_related_part($alt);
          } else {
  
            $rel = $this->add_related_part($message);
          }
          $this->add_html_part($rel);
          for ($i=0; $i<count($this->html_pictures); $i++) {
            $this->add_html_picture_part($rel, $this->html_pictures[$i]);
          }
          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
      }
      if ( (isset($message)) && (is_object($message)) ) {
        $output = $message->encode();
        $this->output = $output['body'];
        reset($output['headers']);
        while (list($key, $value) = each($output['headers'])) {
          $headers[] = $key . ': ' . $value;
        }
        $this->headers = array_merge($this->headers, $headers);
        return true;
      } else {
        return false;
      }
    }
 function send($to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = '') {
      $to = (($to_name != '') ? '"' . $to_name . '" <' . $to_addr . '>' : $to_addr);
      $from = (($from_name != '') ? '"' . $from_name . '" <' . $from_addr . '>' : $from_addr);
      if (is_string($headers)) {
        $headers = explode($this->lf, trim($headers));
      }
      for ($i=0; $i<count($headers); $i++) {
        if (is_array($headers[$i])) {
          for ($j=0; $j<count($headers[$i]); $j++) {
            if ($headers[$i][$j] != '') {
              $xtra_headers[] = $headers[$i][$j];
            }
          }
        }
        if ($headers[$i] != '') {
          $xtra_headers[] = $headers[$i];
        }
      }
      if (!isset($xtra_headers)) {
        $xtra_headers = array();
      }
      if (EMAIL_TRANSPORT == 'smtp') {
        return mail($to_addr, $subject, $this->output, 'From: ' . $from . $this->lf . 'To: ' . $to . $this->lf . implode($this->lf, $this->headers) . $this->lf . implode($this->lf, $xtra_headers));
      } else {
        return mail($to, $subject, $this->output, 'From: '.$from.$this->lf.implode($this->lf, $this->headers).$this->lf.implode($this->lf, $xtra_headers));
      }
    }
 function get_rfc822($to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = '') { 
      $date = 'Date: ' . date('D, d M y H:i:s');
      $to = (($to_name != '') ? 'To: "' . $to_name . '" <' . $to_addr . '>' : 'To: ' . $to_addr);
      $from = (($from_name != '') ? 'From: "' . $from_name . '" <' . $from_addr . '>' : 'From: ' . $from_addr);
      if (is_string($subject)) {
        $subject = 'Subject: ' . $subject;
      }
      if (is_string($headers)) {
        $headers = explode($this->lf, trim($headers));
      }
      for ($i=0; $i<count($headers); $i++) {
        if (is_array($headers[$i])) {
          for ($j=0; $j<count($headers[$i]); $j++) {
            if ($headers[$i][$j] != '') {
              $xtra_headers[] = $headers[$i][$j];
            }
          }
        }
        if ($headers[$i] != '') {
          $xtra_headers[] = $headers[$i];
        }
      }
      if (!isset($xtra_headers)) {
        $xtra_headers = array();
      }
      $headers = array_merge($this->headers, $xtra_headers);
      return $date . $this->lf . $from . $this->lf . $to . $this->lf . $subject . $this->lf . implode($this->lf, $headers) . $this->lf . $this->lf . $this->output;
    }
  }
?>
