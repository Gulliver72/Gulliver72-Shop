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
  class payment {
    var $modules, $selected_module; 
    function payment($module = '') {
      global $payment, $language, $PHP_SELF;
      if (defined('CONSTITUENT_PAYMENT_INSTALLED') && go_not_null(CONSTITUENT_PAYMENT_INSTALLED)) {
        $this->modules = explode(';', CONSTITUENT_PAYMENT_INSTALLED);
        $include_modules = array();
        if ( (go_not_null($module)) && (in_array($module . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
          $this->selected_module = $module;
          $include_modules[] = array('class' => $module, 'file' => $module . '.php');
        } else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }
        for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
          include(FOLDER_RELATIV_LANGUAGES . $language . '/' . FOLDER_RELATIV_LANG_MODULES_IN_LANG . 'payment/' . $include_modules[$i]['file']);
          include(FOLDER_RELATIV_MODULES . 'payment/' . $include_modules[$i]['file']);
          $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
        }   
        if ( (go_count_payment_modules() == 1) && (!isset($GLOBALS[$payment]) || (isset($GLOBALS[$payment]) && !is_object($GLOBALS[$payment]))) ) {
          $payment = $include_modules[0]['class'];
        }
        if ( (go_not_null($module)) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
          $this->form_action_url = $GLOBALS[$module]->form_action_url;
        }
      }
    } 
 function update_status() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module])) {
          if (function_exists('method_exists')) {
            if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
              $GLOBALS[$this->selected_module]->update_status();
            }
          } else { 
            @call_user_func('update_status', $GLOBALS[$this->selected_module]);
          }
        }
      }
    }
    function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script language="javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . JS_ERROR . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.payment_order_way.payment.length) {' . "\n" .
              '    for (var i=0; i<document.payment_order_way.payment.length; i++) {' . "\n" .
              '      if (document.payment_order_way.payment[i].checked) {' . "\n" .
              '        payment_value = document.payment_order_way.payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.payment_order_way.payment.checked) {' . "\n" .
              '    payment_value = document.payment_order_way.payment.value;' . "\n" .
              '  } else if (document.payment_order_way.payment.value) {' . "\n" .
              '    payment_value = document.payment_order_way.payment.value;' . "\n" .
              '  }' . "\n\n";
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $js .= $GLOBALS[$class]->javascript_validation();
          }
        }
        $js .= "\n" . '  if (payment_value == null && submitter != 1) {' . "\n" . 
               '    error_message = error_message + "' . JS_ERROR_NO_PAYMENT_CONSTITUENT_SELECTED . '";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1 && submitter != 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }
      return $js;
    }
    function selection() {
      $selection_array = array();
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $selection = $GLOBALS[$class]->selection();
            if (is_array($selection)) $selection_array[] = $selection;
          }
        }
      }
      return $selection_array;
    } 
function check_crchange_covers() {
	global $crchange_covers;
	return $crchange_covers;
}
    function pre_confirmation_check() {
      global $crchange_covers, $payment_modules;
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        	
          if ($crchange_covers) { 
            $GLOBALS[$this->selected_module]->enabled = false; 
            $GLOBALS[$this->selected_module] = NULL; 
            $payment_modules = ''; 
          } else { 
            $GLOBALS[$this->selected_module]->pre_confirmation_check();
          }
        }
      }
    }
    function confirmation() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->confirmation();
        }
      }
    }
    function process_button() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->process_button();
        }
      }
    }
    function before_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->before_process();
        }
      }
    }
    function after_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->after_process();
        }
      }
    }
    function get_error() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->get_error();
        }
      }
    }
  }
?>
