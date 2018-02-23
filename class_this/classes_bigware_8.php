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
  class httpClient {
    var $url; 
    var $reply; 
    var $replyString; 
    var $protocolVersion = '1.1';
    var $requestHeaders, $requestBody;
    var $socket = false; 
    var $useProxy = false;
    var $proxyHost, $proxyPort;
 function httpClient($host = '', $port = '') {
      if (go_not_null($host)) {
        $this->connect($host, $port);
      }
    }
 function setProxy($proxyHost, $proxyPort) {
      $this->useProxy = true;
      $this->proxyHost = $proxyHost;
      $this->proxyPort = $proxyPort;
    }
 function setProtocolVersion($version) {
      if ( ($version > 0) && ($version <= 1.1) ) {
        $this->protocolVersion = $version;
        return true;
      } else {
        return false;
      }
    }
 function setCredentials($username, $password) {
      $this->addHeader('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
     }
 function setHeaders($headers) {
      if (is_array($headers)) {
        reset($headers);
        while (list($name, $value) = each($headers)) {
          $this->requestHeaders[$name] = $value;
        }
      }
    }
 function addHeader($headerName, $headerValue) {
      $this->requestHeaders[$headerName] = $headerValue;
    }
 function removeHeader($headerName) {
      unset($this->requestHeaders[$headerName]);
    }
 function Connect($host, $port = '') {
      $this->url['scheme'] = 'http';
      $this->url['host'] = $host;
      if (go_not_null($port)) $this->url['port'] = $port;
      return true;
    }
 function Disconnect() {
      if ($this->socket) fclose($this->socket);
    }
 function Head($uri) {
      $this->responseHeaders = $this->responseBody = '';
      $uri = $this->makeUri($uri);
      if ($this->sendCommand('HEAD ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }
      return $this->reply;
    }
 function Get($url) {
      $this->responseHeaders = $this->responseBody = '';
      $uri = $this->makeUri($url);
      if ($this->sendCommand('GET ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }
      return $this->reply;
    }
 function Post($uri, $query_parameter = '') {
      $uri = $this->makeUri($uri);
      if (is_array($query_parameter)) {
        $postArray = array();
        reset($query_parameter);
        while (list($k, $v) = each($query_parameter)) {
          $postArray[] = urlencode($k) . '=' . urlencode($v);
        }
        $this->requestBody = implode('&', $postArray);
      } 
      $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
      if ($this->sendCommand('POST ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }
      $this->removeHeader('Content-Type');
      $this->removeHeader('Content-Length');
      $this->requestBody = '';
      return $this->reply;
    }
 function Put($uri, $filecontent) {
      $uri = $this->makeUri($uri);
      $this->requestBody = $filecontent;
      if ($this->sendCommand('PUT ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }
      return $this->reply;
    }
 function getHeaders() {
      return $this->responseHeaders;
    }
 function getHeader($headername) {
      return $this->responseHeaders[$headername];
    }
 function getBody() {
      return $this->responseBody;
    }
 function getStatus() {
      return $this->reply;
    }
 function getStatusMessage() {
      return $this->replyString;
    }
  function sendCommand($command) {
      $this->responseHeaders = array();
      $this->responseBody = ''; 
      if ( ($this->socket == false) || (feof($this->socket)) ) {
        if ($this->useProxy) {
          $host = $this->proxyHost;
          $port = $this->proxyPort;
        } else {
          $host = $this->url['host'];
          $port = $this->url['port'];
        }
        if (!go_not_null($port)) $port = 80;
        if (!$this->socket = fsockopen($host, $port, $this->reply, $this->replyString)) {
          return false;
        }
        if (go_not_null($this->requestBody)) {
          $this->addHeader('Content-Length', strlen($this->requestBody));
        }
        $this->request = $command;
        $cmd = $command . "\r\n";
        if (is_array($this->requestHeaders)) {
          reset($this->requestHeaders);
          while (list($k, $v) = each($this->requestHeaders)) {
            $cmd .= $k . ': ' . $v . "\r\n";
          }
        }
        if (go_not_null($this->requestBody)) {
          $cmd .= "\r\n" . $this->requestBody;
        } 
        $this->requestBody = '';
        fputs($this->socket, $cmd . "\r\n");
        return true;
      }
    }
    function processReply() {
      $this->replyString = trim(fgets($this->socket, 1024));
      if (preg_match('|^HTTP/\S+ (\d+) |i', $this->replyString, $a )) {
        $this->reply = $a[1];
      } else {
        $this->reply = 'Bad Response';
      } 
      $this->responseHeaders = $this->processHeader();
      $this->responseBody = $this->processBody();
      return $this->reply;
    }
 function processHeader($lastLine = "\r\n") {
      $headers = array();
      $finished = false;
      while ( (!$finished) && (!feof($this->socket)) ) {
        $str = fgets($this->socket, 1024);
        $finished = ($str == $lastLine);
        if (!$finished) {
          list($hdr, $value) = preg_split('/: /', $str, 2); 
          if (isset($headers[$hdr])) {
            $headers[$hdr] .= '; ' . trim($value);
          } else {
            $headers[$hdr] = trim($value);
          }
        }
      }
      return $headers;
    }
 function processBody() {
      $data = '';
      $counter = 0;
      do {
        $status = socket_get_status($this->socket);
        if ($status['eof'] == 1) {
          break;
        }
        if ($status['unread_bytes'] > 0) {
          $buffer = fread($this->socket, $status['unread_bytes']);
          $counter = 0;
        } else {
          $buffer = fread($this->socket, 128);
          $counter++;
          usleep(2);
        }
        $data .= $buffer;
      } while ( ($status['unread_bytes'] > 0) || ($counter++ < 10) );
      return $data;
    }
 function makeUri($uri) {
      $a = parse_url($uri);
      if ( (isset($a['scheme'])) && (isset($a['host'])) ) {
        $this->url = $a;
      } else {
        unset($this->url['query']);
        unset($this->url['fragment']);
        $this->url = array_merge($this->url, $a);
      }
      if ($this->useProxy) {
        $requesturi = 'http://' . $this->url['host'] . (empty($this->url['port']) ? '' : ':' . $this->url['port']) . $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      } else {
        $requesturi = $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      }
      return $requesturi;
    }
  }
?>
