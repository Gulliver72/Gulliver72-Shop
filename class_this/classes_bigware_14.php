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
class order {
	var $info, $totals, $items, $attendee, $delivery, $content_type;
	var $total_before_discount;

	function order($order_id = '') {
		$this->info = array();
		$this->totals = array();
		$this->items = array();
		$this->attendee = array();
		$this->delivery = array();
		if (go_not_null($order_id)) {
			$this->query($order_id);
		} else {
			$this->cart();
		}
	}
	function query($order_id) {

		global $languages_id;
		$order_id = go_db_producing_input($order_id); 
		$order_query = go_db_query("select attendees_id, attendees_group_id, attendees_name, attendees_company, attendees_street_address, attendees_street_address2, attendees_suburb, attendees_city, attendees_postcode, attendees_state, attendees_land, attendees_telephone, attendees_email_address, attendees_form_of_address_id, delivery_name, delivery_company, delivery_street_address, delivery_street_address2, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_land, delivery_form_of_address_id, billing_name, billing_company, billing_piva, billing_cf, billing_street_address, billing_street_address2, billing_suburb, billing_city, billing_postcode, billing_state, billing_land, billing_form_of_address_id, payment_method, cc_type, cc_owner, cc_number, cc_code, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified from " . DB_TBL_ORDERS . " where orders_id = '" . (int)$order_id . "'"); 
		$order = go_db_fetch_array($order_query);
		$totals_query = go_db_query("select title, text from " . DB_TBL_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");
		while ($totals = go_db_fetch_array($totals_query)) {
			$this->totals[] = array('title' => $totals['title'],
					'text' => $totals['text']);
		}
		$order_total_query = go_db_query("select text from " . DB_TBL_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' and class = 'ot_total'");
		$order_total = go_db_fetch_array($order_total_query);
		$shipping_method_query = go_db_query("select title from " . DB_TBL_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' and class = 'ot_shipping'");
		$shipping_method = go_db_fetch_array($shipping_method_query);
		$order_status_query = go_db_query("select orders_status_name from " . DB_TBL_ORDERS_STATUS . " where orders_status_id = '" . $order['orders_status'] . "' and language_id = '" . (int)$languages_id . "'");
		$order_status = go_db_fetch_array($order_status_query);
		$this->info = array('currency' => $order['currency'],
				'currency_value' => $order['currency_value'],
				'payment_method' => $order['payment_method'],
				'cc_type' => $order['cc_type'],
				'cc_owner' => $order['cc_owner'],
				'cc_number' => $order['cc_number'],
				'cc_code' => $order['cc_code'],
				'cc_expires' => $order['cc_expires'],
				'date_purchased' => $order['date_purchased'],
				'orders_status' => $order_status['orders_status_name'],
				'last_modified' => $order['last_modified'],
				'total' => strip_tags($order_total['text']),
				'shipping_method' => ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title'])));
		$this->attendee = array('id' => $order['attendees_id'], 
				'group_id' => $order['attendees_group_id'], 
				'name' => $order['attendees_name'],
				'company' => $order['attendees_company'],
				'street_address2' => $order['attendees_street_address2'],
				'street_address' => $order['attendees_street_address'],
				'suburb' => $order['attendees_suburb'],
				'city' => $order['attendees_city'],
				'postcode' => $order['attendees_postcode'],
				'state' => $order['attendees_state'],
				'land' => $order['attendees_land'],
				'format_id' => $order['attendees_form_of_address_id'],
				'telephone' => $order['attendees_telephone'],
				'email_address' => $order['attendees_email_address']);
		$this->delivery = array('name' => $order['delivery_name'],
				'company' => $order['delivery_company'],
				'street_address' => $order['delivery_street_address'],
				'street_address2' => $order['delivery_street_address2'],
				'suburb' => $order['delivery_suburb'],
				'city' => $order['delivery_city'],
				'postcode' => $order['delivery_postcode'],
				'state' => $order['delivery_state'],
				'land' => $order['delivery_land'],
				'format_id' => $order['delivery_form_of_address_id']);
		if (empty($this->delivery['name']) && empty($this->delivery['street_address'])) {
			$this->delivery = false;
		}
		$this->billing = array('name' => $order['billing_name'],
				'company' => $order['billing_company'],
				'street_address' => $order['billing_street_address'],
				'street_address2' => $order['billing_street_address2'],
				'suburb' => $order['billing_suburb'],
				'city' => $order['billing_city'],
				'postcode' => $order['billing_postcode'],
				'state' => $order['billing_state'],
				'land' => $order['billing_land'],
				'piva' => $order['billing_piva'], 
				'cf' => $order['billing_cf'],  
				'format_id' => $order['billing_form_of_address_id']);
		$index = 0;
		$orders_items_query = go_db_query("select orders_items_id, items_id, items_name, items_model, items_price, items_basis_price, items_price_option , items_tax, items_quantity,items_laenge,items_breite, final_price from " . DB_TBL_ORDERS_ITEMS . " where orders_id = '" . (int)$order_id . "'");
		while ($orders_items = go_db_fetch_array($orders_items_query)) {
			$this->items[$index] = array('qty' => $orders_items['items_quantity'],
					'laenge' => $orders_items['items_laenge'],
					'breite' => $orders_items['items_breite'],
					'items_basis_price' => $orders_items['items_basis_price'],
					'id' => $orders_items['items_id'],
					'name' => $orders_items['items_name'],
					'model' => $orders_items['items_model'],
					'tax' => $orders_items['items_tax'],
					'price' => $orders_items['items_price'],
					'items_price_option' => $orders_items['items_price_option'],
					'final_price' => $orders_items['final_price']); 
			global $attendee_id, $attendee_group_id;
			if (!isset($attendee_group_id)) $attendee_group_id = go_get_attendee_group_id($attendee_id);
//      $attendee_group_id_query = go_db_query("select attendees_group_id from " . DB_TBL_ATTENDEES . " where attendees_id = '". $attendee_id . "'");
//			$attendee_group_id = go_db_fetch_array($attendee_group_id_query);
			if (isset($attendee_group_id['attendees_group_id']) && $attendee_group_id['attendees_group_id'] != 0) {
				$orders_attendees_price = go_db_query("select attendees_group_price from " . DB_TBL_ITEMS_GROUPS . " where attendees_group_id = '". $attendee_group_id['attendees_group_id'] . "' and items_id = '" . $items[$i]['id'] . "'");
				if ($orders_attendees = go_db_fetch_array($orders_attendees_price)) {
					$this->items[$index] = array('price' => $orders_attendees['attendees_group_price'], 'final_price' => $orders_attendees['attendees_group_price']);
				}
			} 

			$subindex = 0;
			if (KONFIGURATOR=='true'){
				if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
					include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_14a.php');
				} else {	
					$characteristics_options_query = go_db_query("select distinct items_options_conf from " . DB_TBL_ORDERS_ITEMS_CHARACTERISTICS_CONF . " where orders_id = '" . (int)$order_id . "' and orders_items_id = '" . (int)$orders_items['orders_items_id'] . "'");
					$characteristics_options_num = go_db_num_rows($characteristics_options_query);
					for ($ao=0;$ao<$characteristics_options_num;$ao++){
						$characteristics_query = go_db_query("select items_options_conf, items_options_conf_values, options_conf_values_price, price_prefix from " . DB_TBL_ORDERS_ITEMS_CHARACTERISTICS_CONF . " where orders_id = '" . (int)$order_id . "' and orders_items_id = '" . (int)$orders_items['orders_items_id'] . "' and items_options_conf='".mysqli_result($characteristics_options_query,$ao,'items_options_conf')."'");

						if (go_db_num_rows($characteristics_query)>1) {
							$a=0;
							while ($characteristics = go_db_fetch_array($characteristics_query)) {
								$this->items[$index]['characteristics'][$subindex][$a] = array('option' => $characteristics['items_options_conf'],
										'value' => $characteristics['items_options_conf_values'],
										'prefix' => $characteristics['price_prefix'],
										'price' => $characteristics['options_conf_values_price']);
								$a++;
							}//while
						}//if
						elseif (go_db_num_rows($characteristics_query)){
							while ($characteristics = go_db_fetch_array($characteristics_query)) {
								$this->items[$index]['characteristics'][$subindex] = array('option' => $characteristics['items_options_conf'],
										'value' => $characteristics['items_options_conf_values'],
										'prefix' => $characteristics['price_prefix'],
										'price' => $characteristics['options_conf_values_price']);

							}//while
						}$subindex++;
					}//for ao						
				} // not file_exists
			}//konfigurator
			else {	





				$characteristics_query = go_db_query("select items_options, items_options_values, options_values_price, price_prefix from " . DB_TBL_ORDERS_ITEMS_CHARACTERISTICS . " where orders_id = '" . (int)$order_id . "' and orders_items_id = '" . (int)$orders_items['orders_items_id'] . "'");
				if (go_db_num_rows($characteristics_query)) {
					while ($characteristics = go_db_fetch_array($characteristics_query)) {
						$this->items[$index]['characteristics'][$subindex] = array('option' => $characteristics['items_options'],
								'value' => $characteristics['items_options_values'],
								'prefix' => $characteristics['price_prefix'],
								'price' => $characteristics['options_values_price']);
						$subindex++;
					}//while
				}//if
			}//not konfigurator

			$this->info['tax_groups']["{$this->items[$index]['tax']}"] = '1';
			$index++;
		}
	}
	function cart() {
		global $attendee_id, $sendto, $billto, $cart, $languages_id, $currency, $currencies, $shipping, $payment, $my_hidden_items;

		$this->content_type = $cart->get_content_type(); 
		$attendee_address_query = go_db_query("select c.attendees_firstname, c.attendees_lastname, c.attendees_group_id, c.attendees_telephone, c.attendees_email_address, ab.entry_company, ab.entry_street_address, ab.entry_street_address2, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.lands_id, cp.lands_name, co.lands_iso_code_2, co.lands_iso_code_3, co.form_of_address_id, ab.entry_state from ((((" . DB_TBL_ATTENDEES . " c, " . DB_TBL_DIRECTORY_TO_ADDRESS . " ab) left join " . DB_TBL_ZONES . " z on (ab.entry_zone_id = z.zone_id)) left join " . DB_TBL_LANDS . " co on (ab.entry_land_id = co.lands_id)) left join " . DB_TBL_LANDS_NAME . " cp on (co.lands_id = cp.lands_id)) where c.attendees_id = '" . (int)$attendee_id . "' and ab.attendees_id = '" . (int)$attendee_id . "' and c.attendees_default_address_id = ab.directory_to_address_id"); 
		$attendee_address = go_db_fetch_array($attendee_address_query);
		$shipping_address_query = go_db_query("select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_street_address2, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_land_id, c.lands_id, cp.lands_name, c.lands_iso_code_2, c.lands_iso_code_3, c.form_of_address_id, ab.entry_state from ((((" . DB_TBL_DIRECTORY_TO_ADDRESS . " ab) left join " . DB_TBL_ZONES . " z on (ab.entry_zone_id = z.zone_id)) left join " . DB_TBL_LANDS . " c on (ab.entry_land_id = c.lands_id)) left join " . DB_TBL_LANDS_NAME . " cp on (c.lands_id = cp.lands_id)) where ab.attendees_id = '" . (int)$attendee_id . "' and ab.directory_to_address_id = '" . (int)$sendto . "'");
		$shipping_address = go_db_fetch_array($shipping_address_query);

		$billing_address_query = go_db_query("select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_piva, ab.entry_cf, ab.entry_street_address, ab.entry_street_address2, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_land_id, c.lands_id, cp.lands_name, c.lands_iso_code_2, c.lands_iso_code_3, c.form_of_address_id, ab.entry_state from ((((" . DB_TBL_DIRECTORY_TO_ADDRESS . " ab) left join " . DB_TBL_ZONES . " z on (ab.entry_zone_id = z.zone_id)) left join " . DB_TBL_LANDS . " c on (ab.entry_land_id = c.lands_id)) left join " . DB_TBL_LANDS_NAME . " cp on (c.lands_id = cp.lands_id)) where ab.attendees_id = '" . (int)$attendee_id . "' and ab.directory_to_address_id = '" . (int)$billto . "'");
		$billing_address = go_db_fetch_array($billing_address_query);
		$tax_address_query = go_db_query("select ab.entry_land_id, ab.entry_zone_id from ((" . DB_TBL_DIRECTORY_TO_ADDRESS . " ab) left join " . DB_TBL_ZONES . " z on (ab.entry_zone_id = z.zone_id)) where ab.attendees_id = '" . (int)$attendee_id . "' and ab.directory_to_address_id = '" . (int)($this->content_type == 'virtual' ? $billto : $sendto) . "'");
		$tax_address = go_db_fetch_array($tax_address_query);
		$this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
				'currency' => $currency,
				'currency_value' => $currencies->currencies[$currency]['value'],
				'payment_method' => $payment,
				'payment_class' => $payment,
				'cc_type' => (isset($GLOBALS['cc_type']) ? $GLOBALS['cc_type'] : ''),
				'cc_owner' => (isset($GLOBALS['cc_owner']) ? $GLOBALS['cc_owner'] : ''),
				'cc_number' => (isset($GLOBALS['cc_number']) ? $GLOBALS['cc_number'] : ''),
				'cc_code' => (isset($GLOBALS['cc_code']) ? $GLOBALS['cc_code'] : ''),
				'cc_expires' => (isset($GLOBALS['cc_expires']) ? $GLOBALS['cc_expires'] : ''),
				'shipping_method' => $shipping['title'],
				'shipping_class' =>  ( (strpos($shipping['id'],'_') > 0) ?  substr( strrev( strchr(strrev($shipping['id']),'_') ),0,-1) : $shipping['id'] ), 
				'shipping_cost' => $shipping['cost'],
				'subtotal' => 0,
				'tax' => 0,
				'tax_groups' => array(),
				'comments' => (isset($GLOBALS['comments']) ? $GLOBALS['comments'] : ''));
		if (isset($GLOBALS[$payment]) && is_object($GLOBALS[$payment])) {
			$this->info['payment_method'] = $GLOBALS[$payment]->title;
			$this->info['payment_class'] = $GLOBALS[$payment]->code;
			if ( isset($GLOBALS[$payment]->order_status) && is_numeric($GLOBALS[$payment]->order_status) && ($GLOBALS[$payment]->order_status > 0) ) {
				$this->info['order_status'] = $GLOBALS[$payment]->order_status;
			}
		}
		$this->attendee = array( 
				'group_id' => $attendee_address['group_id'], 
				'firstname' => $attendee_address['attendees_firstname'],
				'lastname' => $attendee_address['attendees_lastname'],
				'company' => $attendee_address['entry_company'],
				'street_address' => $attendee_address['entry_street_address'],
				'street_address2' => $attendee_address['entry_street_address2'],
				'suburb' => $attendee_address['entry_suburb'],
				'city' => $attendee_address['entry_city'],
				'postcode' => $attendee_address['entry_postcode'],
				'state' => ((go_not_null($attendee_address['entry_state'])) ? $attendee_address['entry_state'] : $attendee_address['zone_name']),
				'zone_id' => $attendee_address['entry_zone_id'],
				'land' => array('id' => $attendee_address['lands_id'], 'title' => $attendee_address['lands_name'], 'iso_code_2' => $attendee_address['lands_iso_code_2'], 'iso_code_3' => $attendee_address['lands_iso_code_3']),
				'format_id' => $attendee_address['form_of_address_id'],
				'telephone' => $attendee_address['attendees_telephone'],
				'email_address' => $attendee_address['attendees_email_address']);
		$this->delivery = array('firstname' => $shipping_address['entry_firstname'],
				'lastname' => $shipping_address['entry_lastname'],
				'company' => $shipping_address['entry_company'],
				'street_address' => $shipping_address['entry_street_address'],
				'street_address2' => $shipping_address['entry_street_address2'],
				'suburb' => $shipping_address['entry_suburb'],
				'city' => $shipping_address['entry_city'],
				'postcode' => $shipping_address['entry_postcode'],
				'state' => ((go_not_null($shipping_address['entry_state'])) ? $shipping_address['entry_state'] : $shipping_address['zone_name']),
				'zone_id' => $shipping_address['entry_zone_id'],
				'land' => array('id' => $shipping_address['lands_id'], 'title' => $shipping_address['lands_name'], 'iso_code_2' => $shipping_address['lands_iso_code_2'], 'iso_code_3' => $shipping_address['lands_iso_code_3']),
				'land_id' => $shipping_address['entry_land_id'],
				'format_id' => $shipping_address['form_of_address_id']);
		$this->billing = array('firstname' => $billing_address['entry_firstname'],
				'lastname' => $billing_address['entry_lastname'],
				'company' => $billing_address['entry_company'],
				'piva' => $billing_address['entry_piva'],
				'cf' => $billing_address['entry_cf'],
				'street_address' => $billing_address['entry_street_address'],
				'street_address2' => $billing_address['entry_street_address2'],
				'suburb' => $billing_address['entry_suburb'],
				'city' => $billing_address['entry_city'],
				'postcode' => $billing_address['entry_postcode'],
				'state' => ((go_not_null($billing_address['entry_state'])) ? $billing_address['entry_state'] : $billing_address['zone_name']),
				'zone_id' => $billing_address['entry_zone_id'],
				'land' => array('id' => $billing_address['lands_id'], 'title' => $billing_address['lands_name'], 'iso_code_2' => $billing_address['lands_iso_code_2'], 'iso_code_3' => $billing_address['lands_iso_code_3']),
				'land_id' => $billing_address['entry_land_id'],
				'format_id' => $billing_address['form_of_address_id']);
		$index = 0;
		if (OPTIMIZE_ORDER == 'true'){
			if (!isset($my_hidden_items)) {
				$items = $cart->get_items();
				//echo drin1;
			}
			else{
				$items = $my_hidden_items;
				//echo drin2;
			}
		}
		else{
			$items = $cart->get_items();
		}


		// für spaeter die Kundengruppen_id rausholen
		global $attendee_id;
		$attendee_group_id_query = go_db_query("select attendees_group_id from " . DB_TBL_ATTENDEES . " where attendees_id = '". $attendee_id . "'");
		$attendee_group_id = go_db_fetch_array($attendee_group_id_query);			
		if ($attendee_group_id['attendees_group_id'] != '0') {
			$group_tax_qry = go_db_query("select group_tax from ". DB_TBL_ATTENDEES_GROUPS ." where attendees_group_id = '". $attendee_group_id['attendees_group_id'] . "'");
			$group_tax = ''; 
			$group_tax = go_db_fetch_array($group_tax_qry);
		}		

		for ($i=0, $n=sizeof($items); $i<$n; $i++) {

			if ($items[$i]['laenge']=="") {$items[$i]['laenge']=1000;}
			if ($items[$i]['breite']=="") {$items[$i]['breite']=1000;}

      // BOF Fix Gulliver72 
        $final = $items[$i]['price'] + $items[$i]['items_basis_price'] + $cart->characteristics_price($items[$i]['id']);
        $final = (string)$final;
        global $language;
        if (isset($language) && $language == 'de') {
//        if ($languages_id == '3') {
          $final = preg_replace("','", ".", $final);
//          $final = str_replace(',', '.', preg_replace('/[^0-9,.%]/','',$final));
        }
			// Loopthrough12
			$this->items[$index] = array('tax' => go_get_tax_rate($items[$i]['tax_class_id'], $tax_address['entry_land_id'], $tax_address['entry_zone_id']),
					'tax_description' => go_get_tax_description($items[$i]['tax_class_id'], $tax_address['entry_land_id'], $tax_address['entry_zone_id']),
					'final_price' => $final); 
      // BOF Fix Gulliver72 
			// all values from table "items" and "items_descriptions" and define befor

			if (is_array ($items[$i])){
				foreach ($items[$i] as $key_items_and_description_and_more => $val_items_and_description_and_more){
					if ($key_items_and_description_and_more == 'quantity'){$this->items[$index]['qty'] = $val_items_and_description_and_more;}
					elseif ($key_items_and_description_and_more == 'characteristics'){}
					else{$this->items[$index][$key_items_and_description_and_more] = $val_items_and_description_and_more;}
				}
			} 

			// 						$add_cart_variable_serialize = $items[$i]['add_cart_variable_serialize'];
			//						$add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
			//						if (is_array ($add_cart_variable)){
			//							foreach ($add_cart_variable as $key => $val){
			//								$this->items[$index][$key] = $val;
			//				    	}
			//						}
			//						$this->items[$index]['add_cart_variable_serialize'] = $add_cart_variable_serialize; 
			//						print_r($this->items[$index]);

			if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/no_price')) {
				require(FOLDER_ABSOLUT_CATALOG . 'modules/no_price/class_this/classes_bigware_14.php');
			}

			// siehe weiter oben die Kundengruppen_id rausgeholt
			if ($attendee_group_id['attendees_group_id'] != '0') {
				$orders_attendees_price = go_db_query("select attendees_group_price from " . DB_TBL_ITEMS_GROUPS . " where attendees_group_id = '". $attendee_group_id['attendees_group_id'] . "' and items_id = '" . $items[$i]['id'] . "'");
				$orders_attendees = go_db_fetch_array($orders_attendees_price);
				if ($orders_attendees = go_db_fetch_array($orders_attendees_price)) {
          // BOF Fix Gulliver72 
          $final = $orders_attendees['attendees_group_price'] + $cart->characteristics_price($items[$i]['id']);
          $final = (string)$final;
          global $language;
          if (isset($language) && $language == 'de') {
//          if ($languages_id == '3') {
            $final = preg_replace("','", ".", $final);
//          $final = str_replace(',', '.', preg_replace('/[^0-9,.%]/','',$final));
          }
					$this->items[$index] = array('price' => $orders_attendees['attendees_group_price'], 'final_price' => $final);
          // BOF Fix Gulliver72 
				}
			} 
			if ($items[$i]['characteristics']) {
				$subindex = 0;
				reset($items[$i]['characteristics']);

				while (list($option, $value) = each($items[$i]['characteristics'])) {
					if (KONFIGURATOR=='true'){
						if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
							include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_14b.php');
						}
						else{	
							if (is_array($value)) {
								$value_keys = array_keys($value);
								for ($v=0;$v<sizeof($value_keys);$v++){
									$characteristics_query = go_db_query("select popt.items_options_conf_name, poval.items_options_conf_values_name, pa.options_conf_values_price, pa.price_prefix from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_OPTIONS_CONF_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " pa where pa.items_id = '" . (int)$items[$i]['id'] . "' and pa.options_conf_id = '" . (int)$option . "' and pa.options_conf_id = popt.items_options_conf_id and pa.options_conf_values_id = '" . (int)$value[$value_keys[$v]] . "' and pa.options_conf_values_id = poval.items_options_conf_values_id and popt.language_id = '" . (int)$languages_id . "' and poval.language_id = '" . (int)$languages_id . "'");
									$characteristics = go_db_fetch_array($characteristics_query);
									$this->items[$index]['characteristics'][$subindex][$value_keys[$v]] = array('option' => $characteristics['items_options_conf_name'],
											'value' => $characteristics['items_options_conf_values_name'],
											'option_id' => $option,
											'value_id' => $value[$value_keys[$v]],
											'prefix' => $characteristics['price_prefix'],
											'price' => $characteristics['options_conf_values_price']);

								}//for
								$subindex++;
							}//array
							else {
								$characteristics_query = go_db_query("select popt.items_options_conf_name, poval.items_options_conf_values_name, pa.options_conf_values_price, pa.price_prefix from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_OPTIONS_CONF_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " pa where pa.items_id = '" . (int)$items[$i]['id'] . "' and pa.options_conf_id = '" . (int)$option . "' and pa.options_conf_id = popt.items_options_conf_id and pa.options_conf_values_id = '" . (int)$value . "' and pa.options_conf_values_id = poval.items_options_conf_values_id and popt.language_id = '" . (int)$languages_id . "' and poval.language_id = '" . (int)$languages_id . "'");
								$characteristics = go_db_fetch_array($characteristics_query);
								$this->items[$index]['characteristics'][$subindex] = array('option' => $characteristics['items_options_conf_name'],
										'value' => $characteristics['items_options_conf_values_name'],
										'option_id' => $option,
										'value_id' => $value,
										'prefix' => $characteristics['price_prefix'],
										'price' => $characteristics['options_conf_values_price']);
								$subindex++;
							}//not array							
						}// not file_exists
					}//if konfigurator
					else {

						if (OPTIMIZE_ORDER == 'true'){

							if (!isset($my_hidden_items)) {
								$characteristics_query = go_db_query("select items_options_id, items_options_value_id, items_characteristics_id, options_values_price, price_prefix, items_options_name, items_options_values_name, qty from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . $attendee_id . "' and items_id = '" . go_db_input($items[$i]['id']) . "' and  items_options_id = '" . (int)$option . "' and  items_options_value_id = '" . (int)$value . "'");
								if (go_db_num_rows($characteristics_query)==0) {
									$characteristics_query = go_db_query("select popt.items_options_name, poval.items_options_values_name, pa.options_values_price, pa.price_prefix from " . DB_TBL_ITEMS_OPTIONS . " popt, " . DB_TBL_ITEMS_OPTIONS_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS . " pa where pa.items_id = '" . (int)$items[$i]['id'] . "' and pa.options_id = '" . (int)$option . "' and pa.options_id = popt.items_options_id and pa.options_values_id = '" . (int)$value . "' and pa.options_values_id = poval.items_options_values_id and popt.language_id = '" . (int)$languages_id . "' and poval.language_id = '" . (int)$languages_id . "'");
								}
								$characteristics = go_db_fetch_array($characteristics_query);
							}
							else{
								$characteristics['items_options_name'] = $my_hidden_items[$i]['characteristics_more'][$option]['items_options_name'];
								$characteristics['items_options_values_name'] = $my_hidden_items[$i]['characteristics_more'][$option]['items_options_values_name'];
								$characteristics['price_prefix'] = $my_hidden_items[$i]['characteristics_more'][$option]['price_prefix'];
								$characteristics['options_values_price'] = $my_hidden_items[$i]['characteristics_more'][$option]['options_values_price'];

							}
						}
						else{
							$characteristics_query = go_db_query("select items_options_id, items_options_value_id, items_characteristics_id, options_values_price, price_prefix, items_options_name, items_options_values_name, qty from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . $attendee_id . "' and items_id = '" . go_db_input($items[$i]['id']) . "' and  items_options_id = '" . (int)$option . "' and  items_options_value_id = '" . (int)$value . "'");
							if (go_db_num_rows($characteristics_query)==0) {
								$characteristics_query = go_db_query("select popt.items_options_name, poval.items_options_values_name, pa.options_values_price, pa.price_prefix from " . DB_TBL_ITEMS_OPTIONS . " popt, " . DB_TBL_ITEMS_OPTIONS_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS . " pa where pa.items_id = '" . (int)$items[$i]['id'] . "' and pa.options_id = '" . (int)$option . "' and pa.options_id = popt.items_options_id and pa.options_values_id = '" . (int)$value . "' and pa.options_values_id = poval.items_options_values_id and popt.language_id = '" . (int)$languages_id . "' and poval.language_id = '" . (int)$languages_id . "'");
							}
							$characteristics = go_db_fetch_array($characteristics_query);					
						}		

						$this->items[$index]['characteristics'][$subindex] = array('option' => $characteristics['items_options_name'],
								'value' => $characteristics['items_options_values_name'],
								'option_id' => $option,
								'value_id' => $value,
								'prefix' => $characteristics['price_prefix'],
								'price' => $characteristics['options_values_price']);
						$subindex++;

					}//not konfigurtor
				}//while  

			} 
			if ($this->items[$index]['items_price_option'] > 0) {
				$getImMeterQuery[$index] = go_db_query("select immeter, inmeter_breite from price_option where id=".$this->items[$index]['items_price_option']);
				$immeter[$index] = mysqli_result($getImMeterQuery[$index],0,'immeter');
				$inmeter_breite[$index] = mysqli_result($getImMeterQuery[$index],0,'inmeter_breite');
			}
			else {
				$immeter[$index]=1000;
				$inmeter_breite[$index]=1000;
			}

			$item_laenge_breite_query = go_db_query("select attendees_basket_laenge, attendees_basket_breite, attendees_basket_price_option, price_option_comment, items_basis_price from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . $this->items[$index]['id'] . "'");

			if (@go_db_num_rows($item_laenge_breite_query)!=0) {	

				$item_laenge_breite_result = go_db_fetch_array($item_laenge_breite_query);
				$this->items[$index]['laenge'] = $item_laenge_breite_result['attendees_basket_laenge'];
				$this->items[$index]['breite'] = $item_laenge_breite_result['attendees_basket_breite'];


			}
			else {
				if ($this->items[$index]['laenge']=="") {$this->items[$index]['laenge']=1000;}
				if ($this->items[$index]['breite']=="") {$this->items[$index]['breite']=1000;}
			}				

			$items_price_option_query = go_db_query("select shipping_zone_1, shipping_zone_2, items_price_option, shipping_greatship from " . DB_TBL_ITEMS . " where items_id = '" . $this->items[$index]['id'] . "'");
			$items_price_option_now = go_db_fetch_array($items_price_option_query);
			$items_price_option_result = $items_price_option_now['items_price_option'];
			if ($items_price_option_result == 4){
				$shown_price = go_add_tax($this->items[$index]['final_price'], $this->items[$index]['tax']) * $this->items[$index]['qty']*(($this->items[$index]['laenge']*2/$immeter[$index])+($this->items[$index]['breite']*2/$inmeter_breite[$index])) + $this->items[$index]['qty'] * go_add_tax($this->items[$index]['items_basis_price'],$this->items[$index]['tax']); 
			} else {
				$shown_price = go_add_tax($this->items[$index]['final_price'], $this->items[$index]['tax']) * $this->items[$index]['qty']*($this->items[$index]['laenge']/$immeter[$index])*($this->items[$index]['breite']/$inmeter_breite[$index]) + $this->items[$index]['qty'] * go_add_tax($this->items[$index]['items_basis_price'],$this->items[$index]['tax']); 
			}

			$this->items[$i]['shipping_greatship'] = $items_price_option_now['shipping_greatship']; 					
			$this->items[$i]['shipping_zone_1'] = $items_price_option_now['shipping_zone_1']; 								
			$this->items[$i]['shipping_zone_2'] = $items_price_option_now['shipping_zone_2']; 					


			$this->info['subtotal'] += $shown_price;			
			$items_tax = $this->items[$index]['tax'];
			$items_tax_description = $this->items[$index]['tax_description']; 
			$gotax = ''; 
			$gotax = ((($attendee_group_id['attendees_group_id'] == '0')  AND (SHOW_PRICE_WITH_TAX == 'true') AND ($items_tax > 0) ) ? 'inc' : ( ( ($attendee_group_id['attendees_group_id'] != '0') AND ($group_tax['group_tax'] == 'true') AND ($items_tax > 0)  ) ? 'inc' : 'exc')  );
			if ($gotax == 'inc') { 
				$this->items[$index]['tax_value'] = $shown_price - ($shown_price / (($items_tax < 10) ? "1.0" . str_replace('.', '', $items_tax) : "1." . str_replace('.', '', $items_tax)));
				$this->info['tax'] += $this->items[$index]['tax_value'];
				if (isset($this->info['tax_groups']["$items_tax_description"])) {
					$this->info['tax_groups']["$items_tax_description"] += $shown_price - ($shown_price / (($items_tax < 10) ? "1.0" . str_replace('.', '', $items_tax) : "1." . str_replace('.', '', $items_tax)));
				} else {
					$this->info['tax_groups']["$items_tax_description"] = $shown_price - ($shown_price / (($items_tax < 10) ? "1.0" . str_replace('.', '', $items_tax) : "1." . str_replace('.', '', $items_tax)));
				}
			} else {
				$this->items[$index]['tax_value'] = $items_tax / 100 * $shown_price;
				$this->info['tax'] += $this->items[$index]['tax_value'];
				if (isset($this->info['tax_groups']["$items_tax_description"])) {
					$this->info['tax_groups']["$items_tax_description"] += ($items_tax / 100) * $shown_price;
				} else {
					$this->info['tax_groups']["$items_tax_description"] = ($items_tax / 100) * $shown_price;
				}
			}
			$index++;
		}


		$this->info['subtotal'] = strtr($this->info['subtotal'], ",", "."); 
		$this->total_before_discount = $this->info['subtotal'];
		$discount = $this->discount();
		if ($discount > 0) {
			$this->info['discount_value'] = $discount;
			$this->info['discount'] = $this->info['subtotal'] * $discount / 100;
			$this->info['subtotal'] -= $this->info['discount'];
			for ($i=0, $n=sizeof($items); $i<$n; $i++) {
				$items_tax_value       = $this->items[$i]['tax_value'];
				$items_tax_description = $this->items[$i]['tax_description']; 
				$this->info['tax']    -= $items_tax_value * $discount / 100;
				if (isset($this->info['tax_groups']["$items_tax_description"])) {
					$this->info['tax_groups']["$items_tax_description"] -= $items_tax_value * $discount / 100;
				}
			}
		}

		if ($gotax == 'inc') {
			$this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
		} else {
			$this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
		}
	}

	function discount() {
		$discount_value = 0;
		$discount_query = go_db_query("SELECT * FROM discount WHERE `limit` < " . $this->total_before_discount . " AND status = 1 ORDER BY `limit` DESC");
		if (go_db_num_rows($discount_query)!=0) {
			$discount_value = mysqli_result($discount_query, 0, 'discount_procent');
		} 
		return $discount_value;
	}

}
?>