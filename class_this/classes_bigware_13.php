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
  class navigationHistory {
    var $path, $snapshot;
    function __construct() {
      $this->navigationHistory();
    }
    function navigationHistory() {
      $this->reset();
    }
    function reset() {
      $this->path = array();
      $this->snapshot = array();
    }
    function add_current_page() {
      global $PHP_SELF, $_GET, $_POST, $request_type, $bigPfad;
      $set = 'true';
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        if ( ($this->path[$i]['page'] == basename($PHP_SELF)) ) {
          if (isset($bigPfad)) {
            if (!isset($this->path[$i]['get']['bigPfad'])) {
              continue;
            } else {
              if ($this->path[$i]['get']['bigPfad'] == $bigPfad) {
                array_splice($this->path, ($i+1));
                $set = 'false';
                break;
              } else {
                $old_bigPfad = explode('_', $this->path[$i]['get']['bigPfad']);
                $new_bigPfad = explode('_', $bigPfad);
                for ($j=0, $n2=sizeof($old_bigPfad); $j<$n2; $j++) {
                  if (isset($old_bigPfad[$j]) && isset($new_bigPfad[$j]) && $old_bigPfad[$j] != $new_bigPfad[$j]) {
                    array_splice($this->path, ($i));
                    $set = 'true';
                    break 2;
                  }
                }
              }
            }
          } else {
            array_splice($this->path, ($i));
            $set = 'true';
            break;
          }
        }
      }
      if ($set == 'true') {
        $this->path[] = array('page' => basename($PHP_SELF),
                              'mode' => $request_type,
                              'get' => $_GET,
                              'post' => $_POST);
      }
    }
    function remove_current_page() {
      global $PHP_SELF;
      $last_entry_position = sizeof($this->path) - 1;
      if ($this->path[$last_entry_position]['page'] == basename($PHP_SELF)) {
        unset($this->path[$last_entry_position]);
      }
    }
    function set_snapshot($page = '') {
      global $PHP_SELF, $_GET, $_POST, $request_type;
      if (is_array($page)) {
        $this->snapshot = array('page' => $page['page'],
                                'mode' => $page['mode'],
                                'get' => $page['get'],
                                'post' => $page['post']);
      } else {
        $this->snapshot = array('page' => basename($PHP_SELF),
                                'mode' => $request_type,
                                'get' => $_GET,
                                'post' => $_POST);
      }
    }
    function clear_snapshot() {
      $this->snapshot = array();
    }
    function set_path_as_snapshot($history = 0) {
      $pos = (sizeof($this->path)-1-$history);
      $this->snapshot = array('page' => $this->path[$pos]['page'],
                              'mode' => $this->path[$pos]['mode'],
                              'get' => $this->path[$pos]['get'],
                              'post' => $this->path[$pos]['post']);
    }
    function debug() {
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        echo $this->path[$i]['page'] . '?';
        while (list($key, $value) = each($this->path[$i]['get'])) {
          echo $key . '=' . $value . '&';
        }
        if (sizeof($this->path[$i]['post']) > 0) {
          echo '<br>';
          while (list($key, $value) = each($this->path[$i]['post'])) {
            echo '&nbsp;&nbsp;<b>' . $key . '=' . $value . '</b><br>';
          }
        }
        echo '<br>';
      }
      if (sizeof($this->snapshot) > 0) {
        echo '<br><br>';
        echo $this->snapshot['mode'] . ' ' . $this->snapshot['page'] . '?' . go_array_to_string($this->snapshot['get'], array(go_session_name())) . '<br>';
      }
    }
    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }
  }
?>
