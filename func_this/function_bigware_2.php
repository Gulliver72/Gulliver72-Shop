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
  function write_cache(&$var, $filename) {
    $filename = FOLDER_ABSOLUT_CACHE . $filename;
    $success = false; 
    if ($fp = @fopen($filename, 'w')) { 
      flock($fp, 2);  
      fputs($fp, serialize($var)); 
      flock($fp, 3); 
      fclose($fp);
      $success = true;
    }
    return $success;
  }      
  function read_cache(&$var, $filename, $auto_expire = false){
    $filename = FOLDER_ABSOLUT_CACHE . $filename;
    $success = false;
    if (($auto_expire == true) && file_exists($filename)) {
      $now = time();
      $filetime = filemtime($filename);
      $difference = $now - $filetime;
      if ($difference >= $auto_expire) {
        return false;
      }
    } 
    if ($fp = @fopen($filename, 'r')) { 
      $szdata = fread($fp, filesize($filename));
      fclose($fp); 
      $var = unserialize($szdata);
      $success = true;
    }
    return $success;
  }        
  function get_db_cache($sql, &$var, $filename, $refresh = false){
    $var = array(); 
    if (($refresh == true)|| !read_cache($var, $filename)) {  
      $res = go_db_query($sql);  
      while ($rec = go_db_fetch_array($res)) {
        $var[] = $rec;
      } 
      write_cache($var, $filename);
    }
  }   
  function go_cache_categories_frame($auto_expire = false, $refresh = false) {
    global $bigPfad, $language, $languages_id, $tree, $bigPfad_array, $categories_string;
    if (($refresh == true) || !read_cache($cache_output, 'categories_frame-' . $language . '.cache' . $bigPfad, $auto_expire)) {
      ob_start();
      include(FOLDER_RELATIV_FRAMES . 'cat.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'categories_frame-' . $language . '.cache' . $bigPfad);
    }
    return $cache_output;
  }   
  function go_cache_producers_frame($auto_expire = false, $refresh = false) {
    global $_GET, $language;
    $producers_id = '';
    if (isset($_GET['manufactuers_id']) && go_not_null($_GET['producers_id'])) {
      $producers_id = $_GET['producers_id'];
    }
    if (($refresh == true) || !read_cache($cache_output, 'producers_frame-' . $language . '.cache' . $producers_id, $auto_expire)) {
      ob_start();
      include(FOLDER_RELATIV_FRAMES . 'producers.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'producers_frame-' . $language . '.cache' . $producers_id);
    }
    return $cache_output;
  }   
  function go_cache_also_purchased($auto_expire = false, $refresh = false) {
    global $_GET, $language, $languages_id;
    if (($refresh == true) || !read_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['items_id'], $auto_expire)) {
      ob_start();
      include(FOLDER_RELATIV_MODULES . $GLOBALS[CONFIG_NAME_FILE][also_purchased_items]);
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['items_id']);
    }
    return $cache_output;
  }
?>
