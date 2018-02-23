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
  class shipping {
    var $modules; 

    function shipping($module = '') { 
      global $language, $PHP_SELF, $cart, $attendee_id; 

			if (OPTIMIZE_ORDER == 'true'){
					$check_shiptotal_of_shipping_query = go_db_query("select count(*) as count from temp_serialize where user = '" . $attendee_id . "' AND was = 'shiptotal_of_shipping'");
					$check_shiptotal_of_shipping= go_db_fetch_array($check_shiptotal_of_shipping_query);
					if ($check_shiptotal_of_shipping['count'] < 1) {	    
			      $shiptotal = $cart->get_shiptotal();    
			      $shiptotal_hidden = objekt_encode($shiptotal); 
						go_db_query("insert into temp_serialize (user, serialize_code, was) values ('" . $attendee_id . "', '" . $shiptotal_hidden . "', 'shiptotal_of_shipping')"); 
					}
					else{
						$my_shiptotal_query = go_db_query("SELECT serialize_code FROM temp_serialize WHERE user = '" . $attendee_id . "' AND was = 'shiptotal_of_shipping'");
						$my_shiptotal_result=go_db_fetch_array($my_shiptotal_query); 
						$shiptotal = array_in_one_hidden_decode($my_shiptotal_result['serialize_code']);
					}
			}
			else{
				 $shiptotal = $cart->get_shiptotal();    
			}
      if (defined('CONSTITUENT_SHIPPING_INSTALLED') && go_not_null(CONSTITUENT_SHIPPING_INSTALLED)) {
        $this->modules = explode(';', CONSTITUENT_SHIPPING_INSTALLED);
        $include_modules = array();
        if ( (go_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
          $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)));
        } else {
          reset($this->modules);   
          if (go_get_set_it_up_key_value('CONSTITUENT_SHIPPING_INDVSHIP_STATUS') AND $shiptotal AND VERSANMODUL_EINSTELLUNG == 1) {
            	$include_modules[] = array('class'=> 'indvship', 'file' => 'indvship.php');
       
	  }
	  else {
	        if (go_get_set_it_up_key_value('CONSTITUENT_SHIPPING_INDVSHIP_STATUS') and $shiptotal) {
            	$include_modules[] = array('class'=> 'indvship', 'file' => 'indvship.php');
          	} 
            while (list(, $value) = each($this->modules)) {
	    $class = substr($value, 0, strrpos($value, '.')); 
              if ($class != 'indvship')  {
                $include_modules[] = array('class' => $class, 'file' => $value);
              }
            }
	} 
        }
        for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
          include(FOLDER_RELATIV_LANGUAGES . $language . '/' . FOLDER_RELATIV_LANG_MODULES_IN_LANG . 'shipping/' . $include_modules[$i]['file']);
          include(FOLDER_RELATIV_MODULES . 'shipping/' . $include_modules[$i]['file']);
          $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
        }
      }
    }
    function quote($method = '', $module = '') {
      global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_frames;
      $quotes_array = array();
      if (is_array($this->modules)) {
        $shipping_quoted = '';
        $shipping_num_frames = 1;
        $shipping_weight = $total_weight;
        if (SHIPPING_FRAME_WEIGHT >= $shipping_weight*SHIPPING_FRAME_PADDING/100) {
          $shipping_weight = $shipping_weight+SHIPPING_FRAME_WEIGHT;
        } else {
          $shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_FRAME_PADDING/100);
        }
        if ($shipping_weight > SHIPPING_MAX_WEIGHT) { 
          $shipping_num_frames = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
          $shipping_weight = $shipping_weight/$shipping_num_frames;
        }
        $include_quotes = array();
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if (go_not_null($module)) {
            if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
              $include_quotes[] = $class;
            }
          } elseif ($GLOBALS[$class]->enabled) {
            $include_quotes[] = $class;
          }
        }
        $size = sizeof($include_quotes);
        for ($i=0; $i<$size; $i++) {
          $quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
          if (is_array($quotes)) $quotes_array[] = $quotes;
        }
      }
      return $quotes_array;
    }
    function cheapest() {
      if (is_array($this->modules)) {
        $rates = array();
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
          	
            $quotes = $GLOBALS[$class]->quotes;
            for ($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++) {
            	
              if (isset($quotes['methods'][$i]['cost']) && go_not_null($quotes['methods'][$i]['cost'])) {
                $rates[] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                                 'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
                                 'cost' => $quotes['methods'][$i]['cost']);
                                 
              }
            }
          }
        }
        $cheapest = false;
        for ($i=0, $n=sizeof($rates); $i<$n; $i++) {
          if (is_array($cheapest)) {
            if ($rates[$i]['cost'] < $cheapest['cost']) {
              $cheapest = $rates[$i];
            }
          } else {
            $cheapest = $rates[$i];
          }
        }
        return $cheapest;
      }
    }
  }
?>
