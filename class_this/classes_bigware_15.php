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
class order_total {
	var $modules; 
	function order_total() {
		global $language;
		if (defined('CONSTITUENT_ORDER_TOTAL_INSTALLED') && go_not_null(CONSTITUENT_ORDER_TOTAL_INSTALLED)) {
			$this->modules = explode(';', CONSTITUENT_ORDER_TOTAL_INSTALLED);
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				include(FOLDER_RELATIV_LANGUAGES . $language . '/' . FOLDER_RELATIV_LANG_MODULES_IN_LANG . 'order_total/' . $value);
				include(FOLDER_RELATIV_MODULES . 'order_total/' . $value);
				$class = substr($value, 0, strrpos($value, '.'));
				$GLOBALS[$class] = new $class;
			}
		}
	}
	function process() {
		$order_total_array = array();
		if (is_array($this->modules)) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled) {
					$GLOBALS[$class]->process();
					for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
						if (go_not_null($GLOBALS[$class]->output[$i]['title']) && go_not_null($GLOBALS[$class]->output[$i]['text'])) {
							$order_total_array[] = array('code' => $GLOBALS[$class]->code,
									'title' => $GLOBALS[$class]->output[$i]['title'],
									'text' => $GLOBALS[$class]->output[$i]['text'],
									'value' => $GLOBALS[$class]->output[$i]['value'],
									'sort_order' => $GLOBALS[$class]->sort_order);
						}
					}
				}
			}
		}
		return $order_total_array;
	}
	function output() {
		$output_string = '';
		if (is_array($this->modules)) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled) {
					$size = sizeof($GLOBALS[$class]->output);
					for ($i=0; $i<$size; $i++) {
						$output_string .= '              <tr>' . "\n" .
							'                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
							'                <td align="right" class="main">' . go_fetch_dividing_up('tranparentes.gif', '10', '1') . '</td>' . "\n" .
							'                <td align="right" class="main" nowrap>' . $GLOBALS[$class]->output[$i]['text'] . '</td>' . "\n" .
							'              </tr>';
					}
				}
			}
		}
		return $output_string;
	}          
	function crchange_selection() {
		$selection_string = '';
		$close_string = '';
		$crchange_class_string = '';
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			$header_string = '<tr>' . "\n";
			$header_string .= '   <td><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
			$output1_string .= '      <tr>' . "\n";
			$header_string .= '        <td class="main"><b>' . DB_TBL_ABOVE_CRCHANGE . '</b></td>' . "\n";
			$header_string .= '      </tr>' . "\n";
			$header_string .= '    </table></td>' . "\n";
			$header_string .= '  </tr>' . "\n";
			$header_string .= '<tr>' . "\n";
			$header_string .= '   <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">' . "\n";
			$header_string .= '     <tr class="infoFrameInsides"><td><table border="0" width="100%" cellspacing="0" cellpadding="2">' ."\n";
			$header_string .= '       <tr><td width="10">' .  go_fetch_dividing_up('tranparentes.gif', '10', '1') .'</td>' . "\n";
			$header_string .= '           <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
			$close_string   = '                           </table></td>';
			$close_string  .= '<td width="10">' .  go_fetch_dividing_up('tranparentes.gif', '10', '1') . '</td>';
			$close_string  .= '</tr></table></td></tr></table></td>';
			$close_string  .= '<tr><td width="100%">' .  go_fetch_dividing_up('tranparentes.gif', '100%', '10') . '</td></tr>';
			reset($this->modules);
			$output_string = '';
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) {
					$use_crchange_string = $GLOBALS[$class]->use_crchange_amount();
					if ($selection_string =='') $selection_string = $GLOBALS[$class]->crchange_selection();
					if ( ($use_crchange_string !='' ) || ($selection_string != '') ) {
						$output_string .=  '<tr colspan="4"><td colspan="4" width="100%">' .  go_fetch_dividing_up('tranparentes.gif', '100%', '10') . '</td></tr>';
						$output_string = ' <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" >' . "\n" .
							'   <td width="10">' .  go_fetch_dividing_up('tranparentes.gif', '10', '1') .'</td>' .
							'     <td class="main"><b>' . $GLOBALS[$class]->header . '</b></td>' . $use_crchange_string;
						$output_string .= '<td width="10">' . go_fetch_dividing_up('tranparentes.gif', '10', '1') . '</td>';
						$output_string .= '  </tr>' . "\n";
						$output_string .= $selection_string;
					}
				}
			}
			if ($output_string != '') {
				$output_string = $header_string . $output_string;
				$output_string .= $close_string;
			}
		}
		return $output_string;
	}          
	function update_crchange_member($i) {
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ( ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) ) {
					$GLOBALS[$class]->update_crchange_member($i);
				}
			}
		}
	}      
	function collect_posts() {
		global $_POST,$_SESSION;
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ( ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) ) {
					$post_var = 'c' . $GLOBALS[$class]->code;
					if ($_POST[$post_var]) {
						if (!go_session_is_registered($post_var)) go_session_register($post_var);
						$post_var = $_POST[$post_var];
					}
					$GLOBALS[$class]->collect_posts();
				}
			}
		}
	}     
	function pre_confirmation_check() {
		global $payment, $order, $crchange_covers;
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			$total_deductions  = 0;
			reset($this->modules);
			$order_total = $order->info['total'];
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				$order_total = $this->get_order_total_main($class,$order_total);
				if ( ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) ) {
					$total_deductions = $total_deductions + $GLOBALS[$class]->pre_confirmation_check($order_total);
					$order_total = $order_total - $GLOBALS[$class]->pre_confirmation_check($order_total);
				}
			}
			if ($order->info['total'] - $total_deductions <= 0 ) {
				if(!go_session_is_registered('crchange_covers')) go_session_register('crchange_covers');
				$crchange_covers = true;
			}
			else{ 
				if(go_session_is_registered('crchange_covers')) go_session_unregister('crchange_covers');	
			}
		}
	}    
	function apply_crchange() {
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ( ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) ) {
					$GLOBALS[$class]->apply_crchange();
				}
			}
		}
	}  
	function clear_posts() {
		global $_POST,$_SESSION;
		if (CONSTITUENT_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list(, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ( ($GLOBALS[$class]->enabled && $GLOBALS[$class]->crchange_class) ) {
					$post_var = 'c' . $GLOBALS[$class]->code;
					if (go_session_is_registered($post_var)) go_session_unregister($post_var);
				}
			}
		}
	}    
	function get_order_total_main($class, $order_total) {
		global $crchange, $order;  
		return $order_total;
	}
}
?>
