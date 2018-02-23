<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015

  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015  Bigware LTD

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
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine gro�e
  Community, die bei Problemen weiterhelfen kann.

  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.

  Hilfe erhalten Sie im Forum auf www.bigware.de

  -----------------------------------------------------------------------

 ##################################################################################




*/
?>
<?php
$date_availability = 'AND (to_days(items_date_available_end) IS NULL OR (to_days(items_date_available_end) IS NOT NULL AND to_days(now()) <= to_days(items_date_available_end)))';

function go_exit() {
  go_session_close();
  exit();
}
function go_iso_check($iso,$piva) {
  $fp1 = fsockopen ("europa.eu.int", 80, $errno1, $errstr1, 30);
  if (!$fp1) {
    $iso='2';
  } else {
    $lang="EN";
    $find="No, invalid VAT number";
    fputs ($fp1, "GET /comm/taxation_customs/vies/cgi-bin/viesquer?Lang=".$lang."&MS=".$iso."&ISO=".$iso."&VAT=".$piva." HTTP/1.1\r\nHost: europa.eu.int\r\n\r\n");
    $iso='0';
    while (!feof($fp1)) {
      if (substr_count(fgets ($fp1,128),$find)==1) {
        $iso="1";
      }
    }
    fclose ($fp1);
  }
  return $iso;
}
function go_forward($url) {
  if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on') ) {
    if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) {
      $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER));
    }
  }
  header('Location: ' . $url);
  if (SHOP_PAGE_PARSE_TIME == 'true') {
    if (!is_object($logger)) $logger = new logger;
    $logger->timer_stop();
  }
  go_exit();
}
function go_analysis_inputfeld_data($data, $parse) {
  return strtr(trim($data), $parse);
}
function go_output_string($string, $translate = false, $protected = false) {
  if ($protected == true) {
    return isohtmlspecialchars($string);
  } else {
    if ($translate == false) {
      return go_analysis_inputfeld_data($string, array('"' => '&quot;'));
    } else {
      return go_analysis_inputfeld_data($string, $translate);
    }
  }
}
function go_output_string_protected($string) {
  return go_output_string($string, false, true);
}
function go_sanitize_string($string) {
  $string = preg_replace('/\/ +\//', ' ', trim($string));
  return preg_replace("/[<>]/", '_', $string);
}
function go_random_select($query) {
  $random_item = '';
  $random_query = go_db_query($query);
  $num_rows = go_db_num_rows($random_query);
  if ($num_rows > 0) {
    $random_row = go_rand(0, ($num_rows - 1));
    go_db_data_seek($random_query, $random_row);
    $random_item = go_db_fetch_array($random_query);
  }
  return $random_item;
}
function go_get_items_name($item_id, $language = '') {
  global $languages_id;
  if (empty($language)) $language = $languages_id;
  $item_query = go_db_query("select items_name from " . DB_TBL_ITEMS_DESCRIPTION . " where items_id = '" . (int)$item_id . "' and language_id = '" . (int)$language . "'");
  $item = go_db_fetch_array($item_query);
  return $item['items_name'];
}
function go_get_items_description_in_cat_list($item_id, $language = '') {
  global $languages_id;
  if (empty($language)) $language = $languages_id;
  $item_query = go_db_query("select items_description_in_cat_list from " . DB_TBL_ITEMS_DESCRIPTION . " where items_id = '" . (int)$item_id . "' and language_id = '" . (int)$language . "'");
  $item = go_db_fetch_array($item_query);
  return $item['items_description_in_cat_list'];
}
function go_get_items_name2($item_id, $language_id = 0) {
  global $languages_id;
  if ($language_id == 0) $language_id = $languages_id;
  $item_query = go_db_query("select items_name2 from " . DB_TBL_ITEMS_DESCRIPTION . " where items_id = '" . (int)$item_id . "' and language_id = '" . (int)$language_id . "'");
  $item = go_db_fetch_array($item_query);
  return $item['items_name2'];
}
function go_get_items_special_price($item_id) {
    global $attendee_id;

    $attendee_group = go_get_attendee_group_id($attendee_id);
    if (!isset($attendee_group['attendees_group_id'])) $attendee_group = array('attendees_group_id' => 0);// undefinierte Kundengruppe gefixt Gulliver72
    $item_query = go_db_query("select DISTINCT attendees_group_id, specials_new_items_price from " . DB_TBL_SPECIALS . " where items_id = '" . (int)$item_id . "' and status = 1 and attendees_group_id = '". (int)$attendee_group['attendees_group_id'] ."'");
    $item = go_db_fetch_array($item_query);
    if (is_array($item) & isset($item['specials_new_items_price'])) { // undefinierter Index gefixt Gulliver72
        return $item['specials_new_items_price'];
    } else {
        return false; // Rückgabe, damit Funktion einen Wert liefert Gulliver72
    }
}
function go_get_items_stock($items_id) {
  $items_id = go_get_prid($items_id);
  $stock_query = go_db_query("select items_quantity from " . DB_TBL_ITEMS . " where items_id = '" . (int)$items_id . "'");
  $stock_values = go_db_fetch_array($stock_query);
  return $stock_values['items_quantity'];
}
function go_check_stock($items_id, $items_quantity) {
  $stock_left = go_get_items_stock($items_id) - $items_quantity;
  $out_of_stock = '';
  if ($stock_left < 0) {
    $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_ITEM_OUT_OF_STOCK . '</span>';
  }
  return $out_of_stock;
}
function go_break_string($string, $len, $break_char = '-') {
  $l = 0;
  $output = '';
  for ($i=0, $n=strlen($string); $i<$n; $i++) {
    $char = substr($string, $i, 1);
    if ($char != ' ') {
      $l++;
    } else {
      $l = 0;
    }
    if ($l > $len) {
      $l = 1;
      $output .= $break_char;
    }
    $output .= $char;
  }
  return $output;
}
function go_get_all_get_parameter($exclude_array = '') {
  global $_GET;
  if (!is_array($exclude_array)) $exclude_array = array();
  $get_url = '';
  if (is_array($_GET) && (sizeof($_GET) > 0)) {
    reset($_GET);

    if (isset($_GET['selectsearch']) && $_GET['selectsearch'] == 1) { // undefinierter GET-Index gefixt Gulliver72 23.10.2015
      $get_url .= 'search_in_description=1&selectsearch=1&';
      $test = $_GET['keywords'];
      foreach ($test as $key => $value) {
        if ( (strlen($value) > 0) ) {
          $get_url .= 'keywords[' . $key . ']=' . rawurlencode(stripslashes($value)) . '&';
        }
      }
    }
    else {
      while (list($key, $value) = each($_GET)) {
        if ( (is_string($value) && strlen($value) > 0) && ($key != go_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
          $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
        }
      }
    }
  }
  return $get_url;
}


function go_get_lands($lands_id = '', $with_iso_codes = false) {
  $lands_array = array();
  if (go_not_null($lands_id)) {
    if ($with_iso_codes == true) {
      $lands = go_db_query("select lands_name, lands_iso_code_2, lands_iso_code_3 from " . DB_TBL_LANDS . " where lands_id = '" . (int)$lands_id . "' order by lands_name");
      $lands_values = go_db_fetch_array($lands);
      $lands_array = array('lands_name' => $lands_values['lands_name'],
          'lands_iso_code_2' => $lands_values['lands_iso_code_2'],
          'lands_iso_code_3' => $lands_values['lands_iso_code_3']);
    } else {
      $lands = go_db_query("select lands_name from " . DB_TBL_LANDS . " where lands_id = '" . (int)$lands_id . "'");
      $lands_values = go_db_fetch_array($lands);
      $lands_array = array('lands_name' => $lands_values['lands_name']);
    }
  } else {
    $lands = go_db_query("select lands_id, lands_name, lands_iso_code_2 from " . DB_TBL_LANDS . " order by lands_name");
    while ($lands_values = go_db_fetch_array($lands)) {
      $lands_array[] = array('lands_id' => $lands_values['lands_id'],
          'lands_name' => $lands_values['lands_name'],
          'lands_iso_code_2' => $lands_values['lands_iso_code_2']);
    }
  }
  return $lands_array;
}
function go_get_lands_with_iso_codes($lands_id) {
  return go_get_lands($lands_id, true);
}
function go_get_path($current_category_id = '') {
  global $bigPfad_array;
  if (go_not_null($current_category_id)) {
    $cp_size = sizeof($bigPfad_array);
    if ($cp_size == 0) {
      $bigPfad_new = $current_category_id;
    } else {
      $bigPfad_new = '';
      $last_category_query = go_db_query("select parent_id from " . DB_TBL_CATEGORIES . " where categories_id = '" . (int)$bigPfad_array[($cp_size-1)] . "'");
      $last_category = go_db_fetch_array($last_category_query);
      $current_category_query = go_db_query("select parent_id from " . DB_TBL_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
      $current_category = go_db_fetch_array($current_category_query);
      if ($last_category['parent_id'] == $current_category['parent_id']) {
        for ($i=0; $i<($cp_size-1); $i++) {
          $bigPfad_new .= '_' . $bigPfad_array[$i];
        }
      } else {
        for ($i=0; $i<$cp_size; $i++) {
          $bigPfad_new .= '_' . $bigPfad_array[$i];
        }
      }
      $bigPfad_new .= '_' . $current_category_id;
      if (substr($bigPfad_new, 0, 1) == '_') {
        $bigPfad_new = substr($bigPfad_new, 1);
      }
    }
  } else {
    $bigPfad_new = implode('_', $bigPfad_array);
  }
  return 'bigPfad=' . $bigPfad_new;
}
function go_browser_detect($component) {
  global $HTTP_USER_AGENT;
  return stristr($HTTP_USER_AGENT, $component);
}
function go_get_land_name($land_id) {
  $land_array = go_get_lands($land_id);
  return $land_array['lands_name'];
}
function go_get_zone_name($land_id, $zone_id, $default_zone) {
  $zone_query = go_db_query("select zone_name from " . DB_TBL_ZONES . " where zone_land_id = '" . (int)$land_id . "' and zone_id = '" . (int)$zone_id . "'");
  if (go_db_num_rows($zone_query)) {
    $zone = go_db_fetch_array($zone_query);
    return $zone['zone_name'];
  } else {
    return $default_zone;
  }
}
function go_get_zone_code($land_id, $zone_id, $default_zone) {
  $zone_query = go_db_query("select zone_code from " . DB_TBL_ZONES . " where zone_land_id = '" . (int)$land_id . "' and zone_id = '" . (int)$zone_id . "'");
  if (go_db_num_rows($zone_query)) {
    $zone = go_db_fetch_array($zone_query);
    return $zone['zone_code'];
  } else {
    return $default_zone;
  }
}
function go_round($number, $precision) {
  if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
    $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);
    if (substr($number, -1) >= 5) {
      if ($precision > 1) {
        $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
      } elseif ($precision == 1) {
        $number = substr($number, 0, -1) + 0.1;
      } else {
        $number = substr($number, 0, -1) + 1;
      }
    } else {
      $number = substr($number, 0, -1);
    }
  }
  return $number;
}
function go_get_tax_rate($class_id, $land_id = -1, $zone_id = -1) {
  global $attendee_zone_id, $attendee_land_id;
  if ( ($land_id == -1) && ($zone_id == -1) ) {
    if (!go_session_is_registered('attendee_id')) {
      $land_id = SHOP_LAND;
      $zone_id = SHOP_ZONE;
    } else {
      $land_id = $attendee_land_id;
      $zone_id = $attendee_zone_id;
    }
  }
  $tax_query = go_db_query("select sum(tax_rate) as tax_rate from (((" . DB_TBL_TAX_RATES . " tr) left join " . DB_TBL_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id)) left join " . DB_TBL_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id)) where (za.zone_land_id is null or za.zone_land_id = '0' or za.zone_land_id = '" . (int)$land_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority");
  if (go_db_num_rows($tax_query)) {
    $tax_multiplier = 1.0;
    while ($tax = go_db_fetch_array($tax_query)) {
      $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
    }
    return ($tax_multiplier - 1.0) * 100;
  } else {
    return 0;
  }
}
function go_get_tax_description($class_id, $land_id, $zone_id) {
  $tax_query = go_db_query("select tax_description from (((" . DB_TBL_TAX_RATES . " tr) left join " . DB_TBL_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id)) left join " . DB_TBL_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id)) where (za.zone_land_id is null or za.zone_land_id = '0' or za.zone_land_id = '" . (int)$land_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
  if (go_db_num_rows($tax_query)) {
    $tax_description = '';
    while ($tax = go_db_fetch_array($tax_query)) {
      $tax_description .= $tax['tax_description'] . ' + ';
    }
    $tax_description = substr($tax_description, 0, -3);
    return $tax_description;
  } else {
    return TEXT_UNKNOWN_TAX_RATE;
  }
}
function go_add_tax($price, $tax) {
    global $currencies, $attendee_id, $tag;
    $attendee_group = go_get_attendee_group_id($attendee_id);
    if (!isset($attendee_group['attendees_group_id'])) $attendee_group = array('attendees_group_id' => 0);// undefinierte Kundengruppe gefixt Gulliver72
    $group_tax_qry = go_db_query("select group_tax from ". DB_TBL_ATTENDEES_GROUPS ." where attendees_group_id = '". $attendee_group['attendees_group_id'] ."'");
    $group_tax = go_db_fetch_array($group_tax_qry);
    //$group_taxed = ( (go_session_is_registered('attendee_id')) && ($attendee_group['attendees_group_id'] != '0') && ($group_tax['group_tax'] == 'true') && ($tax > 0)) ? 'true' : ( ( (go_session_is_registered('attendee_id')) && (SHOW_PRICE_WITH_TAX == 'true') && ($tax > 0) ) ? 'true' : ( ( (!go_session_is_registered('attendee_id')) && (SHOW_PRICE_WITH_TAX == 'true') && ($tax > 0) ) ? 'true' : 'false'));
    if(!empty($group_tax)) {
        $group_taxed = go_session_is_registered('attendee_id') && $group_tax['group_tax'] == 'true' && $tax > 0 ? 'true' : 'false';
    } else {
        $group_taxed = (SHOW_PRICE_WITH_TAX == 'true' && $tax > 0) ? 'true' : 'false';
    }

    switch ($group_taxed) {
    case 'true':
        $tag = TAX_INCLUDED ;
        return go_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + go_calculate_tax($price, $tax) + $tag;
        break;
    case 'false':
        $tag = TAX_EXCLUDED ;
        return go_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places'])  + $tag;
        break;
    default:
        $tag = TAX_EXCLUDED ;
        return go_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places'])  + $tag;
        break;
    }
}
function go_calculate_tax($price, $tax) {
  global $currencies;
  return go_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
}
function go_count_items_in_category($category_id, $include_inactive = false) {
  $items_count = 0;
  if ($include_inactive == true) {
    $items_query = go_db_query("select count(*) as total from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_TO_CATEGORIES . " p2c where p.items_id = p2c.items_id and p2c.categories_id = '" . (int)$category_id . "'");
  } else {
    $items_query = go_db_query("select count(*) as total from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_TO_CATEGORIES . " p2c where p.items_id = p2c.items_id and p.items_status = '1' and p2c.categories_id = '" . (int)$category_id . "' $date_availability");
  }
  $items = go_db_fetch_array($items_query);
  $items_count += $items['total'];
  $child_categories_query = go_db_query("select categories_id from " . DB_TBL_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
  if (go_db_num_rows($child_categories_query)) {
    while ($child_categories = go_db_fetch_array($child_categories_query)) {
      $items_count += go_count_items_in_category($child_categories['categories_id'], $include_inactive);
    }
  }
  return $items_count;
}
function go_has_category_subcategories($category_id) {
  $child_category_query = go_db_query("select count(*) as count from " . DB_TBL_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
  $child_category = go_db_fetch_array($child_category_query);
  if ($child_category['count'] > 0) {
    return true;
  } else {
    return false;
  }
}
function go_get_form_of_address_id($land_id) {
  $form_of_address_query = go_db_query("select form_of_address_id as format_id from " . DB_TBL_LANDS . " where lands_id = '" . (int)$land_id . "'");
  if (go_db_num_rows($form_of_address_query)) {
    $form_of_address = go_db_fetch_array($form_of_address_query);
    return $form_of_address['format_id'];
  } else {
    return '1';
  }
}
function go_form_of_address($form_of_address_id, $address, $html, $boln, $eoln) {
  $form_of_address_query = go_db_query("select form_of_address as format from " . DB_TBL_FORM_OF_ADDRESS . " where form_of_address_id = '" . (int)$form_of_address_id . "'");
  $form_of_address = go_db_fetch_array($form_of_address_query);
  $company = go_output_string_protected($address['company']);
  if (isset($address['firstname']) && go_not_null($address['firstname'])) {
    $firstname = go_output_string_protected($address['firstname']);
    $lastname = go_output_string_protected($address['lastname']);
  } elseif (isset($address['name']) && go_not_null($address['name'])) {
    $firstname = go_output_string_protected($address['name']);
    $lastname = '';
  } else {
    $firstname = '';
    $lastname = '';
  }
  $street = go_output_string_protected($address['street_address']);
  $street2 = go_output_string_protected($address['street_address2']);
  $suburb = go_output_string_protected($address['suburb']);
  $city = go_output_string_protected($address['city']);
  $state = go_output_string_protected($address['state']);
  if (isset($address['land_id']) && go_not_null($address['land_id'])) {
    $land = go_get_land_name($address['land_id']);
    if (isset($address['zone_id']) && go_not_null($address['zone_id'])) {
      $state = go_get_zone_code($address['land_id'], $address['zone_id'], $state);
    }
  } elseif (isset($address['land']) && go_not_null($address['land'])) {
    $land = go_output_string_protected($address['land']);
  } else {
    $land = '';
  }
  $postcode = go_output_string_protected($address['postcode']);
  $zip = $postcode;
  if ($html) {
    $HR = '<hr>';
    $hr = '<hr>';
    if ( ($boln == '') && ($eoln == "\n") ) {
      $CR = '<br>';
      $cr = '<br>';
      $eoln = $cr;
    } else {
      $CR = $eoln . $boln;
      $cr = $CR;
    }
  } else {
    $CR = $eoln;
    $cr = $CR;
    $HR = '----------------------------------------';
    $hr = '----------------------------------------';
  }
  $statecomma = '';
  if ($street2!="") {  $streets = $street.$cr.$street2;}
  else {$streets = $street;}
  if ($suburb != '') $streets = $street . $cr . $suburb;
  if ($state != '') $statecomma = $state . ', ';
  $fmt = $form_of_address['format'];
  eval("\$address = \"".addslashes($fmt)."\";"); // addslashes gefixt Gulliver72 04.11.2015
  if ( (MEMBER_COMPANY == 'true') && (go_not_null($company)) ) {
    $address = $company . $cr . $address;
  }
  return $address;
}
function go_address_label($attendees_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
  $address_query = go_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_street_address2 as street_address2, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_land_id as land_id from " . DB_TBL_DIRECTORY_TO_ADDRESS . " where attendees_id = '" . (int)$attendees_id . "' and directory_to_address_id = '" . (int)$address_id . "'");
  $address = go_db_fetch_array($address_query);
  $format_id = go_get_form_of_address_id($address['land_id']);
  return go_form_of_address($format_id, $address, $html, $boln, $eoln);
}
function go_row_number_format($number) {
  if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;
  return $number;
}
function go_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
  global $languages_id;
  if (!is_array($categories_array)) $categories_array = array();
  $categories_query = go_db_query("select c.categories_id, cd.categories_name from " . DB_TBL_CATEGORIES . " c, " . DB_TBL_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and c.categories_status = '1' and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
  while ($categories = go_db_fetch_array($categories_query)) {
    $categories_array[] = array('id' => $categories['categories_id'],
        'text' => $indent . $categories['categories_name']);
    if ($categories['categories_id'] != $parent_id) {
      $categories_array = go_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
    }
  }
  return $categories_array;
}
function go_get_producers($producers_array = '') {
  if (!is_array($producers_array)) $producers_array = array();
  $producers_query = go_db_query("select producers_id, producers_name from " . DB_TBL_PRODUCERS . " order by producers_name");
  while ($producers = go_db_fetch_array($producers_query)) {
    $producers_array[] = array('id' => $producers['producers_id'], 'text' => $producers['producers_name']);
  }
  return $producers_array;
}
function go_get_subcategories(&$subcategories_array, $parent_id = 0) {
  $subcategories_query = go_db_query("select categories_id from " . DB_TBL_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
  while ($subcategories = go_db_fetch_array($subcategories_query)) {
    $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
    if ($subcategories['categories_id'] != $parent_id) {
      go_get_subcategories($subcategories_array, $subcategories['categories_id']);
    }
  }
}
function go_date_long($raw_date) {
  if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;
  $year = (int)substr($raw_date, 0, 4);
  $month = (int)substr($raw_date, 5, 2);
  $day = (int)substr($raw_date, 8, 2);
  $hour = (int)substr($raw_date, 11, 2);
  $minute = (int)substr($raw_date, 14, 2);
  $second = (int)substr($raw_date, 17, 2);
  return pagadors_strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
}
function go_date_short($raw_date) {
  if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;
  $year = substr($raw_date, 0, 4);
  $month = (int)substr($raw_date, 5, 2);
  $day = (int)substr($raw_date, 8, 2);
  $hour = (int)substr($raw_date, 11, 2);
  $minute = (int)substr($raw_date, 14, 2);
  $second = (int)substr($raw_date, 17, 2);
  if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
    return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  } else {
    return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
  }
}
function go_analysis_search_string($search_str = '', &$objects) {

  if (is_array($search_str)) {
    for ($i=0;$i<sizeof($search_str);$i++) {
      if ($search_str[$i]!="") {
        $objects_temp[] = $search_str[$i];
      }
    }//for i
    for ($j=0;$j<sizeof($objects_temp);$j++) {
      $objects[] = $objects_temp[$j];
      if ($j!=(sizeof($objects_temp)-1)) $objects[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
    }//fot j

    return true;}
  else {

    $search_str = trim(strtolower($search_str));
    $pieces = preg_split('/[[:space:]]+/', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';
    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }
      $post_objects = array();
      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }
      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);
        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {

        $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));
          if (substr($pieces[$k], -1 ) == '"') {
            $flag = 'off';
            $objects[] = trim($pieces[$k]);
            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }
            unset($tmpstring);
            continue;
          }
        $flag = 'on';
        $k++;
        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }
          if (substr($pieces[$k], -1) != '"') {
            $tmpstring .= ' ' . $pieces[$k];
            $k++;
            continue;
          } else {
            $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));
              $objects[] = trim($tmpstring);
            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }
            unset($tmpstring);
            $flag = 'off';
          }
        }
      }
    }
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
          ($objects[$i] != 'or') &&
          ($objects[$i] != '(') &&
          ($objects[$i+1] != 'and') &&
          ($objects[$i+1] != 'or') &&
          ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;
    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }
    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }
}
function go_checkdate($date_to_check, $format_string, &$date_array) {
  $separator_idx = -1;
  $separators = array('-', ' ', '/', '.');
  $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
  $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  $format_string = strtolower($format_string);
  if (strlen($date_to_check) != strlen($format_string)) {
    return false;
  }
  $size = sizeof($separators);
  for ($i=0; $i<$size; $i++) {
    $pos_dividing_up = strpos($date_to_check, $separators[$i]);
    if ($pos_dividing_up != false) {
      $date_dividing_up_idx = $i;
      break;
    }
  }
  for ($i=0; $i<$size; $i++) {
    $pos_dividing_up = strpos($format_string, $separators[$i]);
    if ($pos_dividing_up != false) {
      $format_dividing_up_idx = $i;
      break;
    }
  }
  if ($date_dividing_up_idx != $format_dividing_up_idx) {
    return false;
  }
  if ($date_dividing_up_idx != -1) {
    $format_string_array = explode( $separators[$date_dividing_up_idx], $format_string );
    if (sizeof($format_string_array) != 3) {
      return false;
    }
    $date_to_check_array = explode( $separators[$date_dividing_up_idx], $date_to_check );
    if (sizeof($date_to_check_array) != 3) {
      return false;
    }
    $size = sizeof($format_string_array);
    for ($i=0; $i<$size; $i++) {
      if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
      if (($format_string_array[$i] == 'dd') || ($format_string_array[$i] == 'tt') ) $day = $date_to_check_array[$i];
      if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'jjjj') ||($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
    }
  } else {
    if (strlen($format_string) == 8 || strlen($format_string) == 9) {
      $pos_month = strpos($format_string, 'mmm');
      if ($pos_month != false) {
        $month = substr( $date_to_check, $pos_month, 3 );
        $size = sizeof($month_abbr);
        for ($i=0; $i<$size; $i++) {
          if ($month == $month_abbr[$i]) {
            $month = $i;
            break;
          }
        }
      } else {
        $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
      }
    } else {
      return false;
    }
    $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
    $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
  }
  if (strlen($year) != 4) {
    return false;
  }
  if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
    return false;
  }
  if ($month > 12 || $month < 1) {
    return false;
  }
  if ($day < 1) {
    return false;
  }
  if (go_is_leap_year($year)) {
    $no_of_days[1] = 29;
  }
  if ($day > $no_of_days[$month - 1]) {
    return false;
  }
  $date_array = array($year, $month, $day);
  return true;
}
function go_is_leap_year($year) {
  if ($year % 100 == 0) {
    if ($year % 400 == 0) return true;
  } else {
    if (($year % 4) == 0) return true;
  }
  return false;
}
function go_create_sort_heading($sortby, $colnum, $heading) {
  global $PHP_SELF;
  $sort_prefix = '';
  $sort_suffix = '';
  if ($sortby) {
    $sort_prefix = '<a href="' . go_href_link(basename($PHP_SELF), go_get_all_get_parameter(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . go_output_string(TEXT_SORT_ITEMS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? TEXT_ASCENDINGLY : TEXT_DESCENDINGLY) . TEXT_BY . $heading) . '" class="itemListing-heading">' ;
    $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
  }
  return $sort_prefix . $heading . $sort_suffix;
}
function go_get_parent_categories(&$categories, $categories_id) {
  $parent_categories_query = go_db_query("select parent_id from " . DB_TBL_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
  while ($parent_categories = go_db_fetch_array($parent_categories_query)) {
    if ($parent_categories['parent_id'] == 0) return true;
    $categories[sizeof($categories)] = $parent_categories['parent_id'];
    if ($parent_categories['parent_id'] != $categories_id) {
      go_get_parent_categories($categories, $parent_categories['parent_id']);
    }
  }
}
function go_get_item_path($items_id) {
  global $date_availability; // undefinierte Variable gefixt Gulliver72 28.10.2015
  $bigPfad = '';
  $category_query = go_db_query("select p2c.categories_id from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_TO_CATEGORIES . " p2c where p.items_id = '" . (int)$items_id . "' and p.items_status = '1' and p.items_id = p2c.items_id $date_availability limit 1");
  if (go_db_num_rows($category_query)) {
    $category = go_db_fetch_array($category_query);
    $categories = array();
    go_get_parent_categories($categories, $category['categories_id']);
    $categories = array_reverse($categories);
    $bigPfad = implode('_', $categories);
    if (go_not_null($bigPfad)) $bigPfad .= '_';
    $bigPfad .= $category['categories_id'];
  }
  return $bigPfad;
}
//  function go_get_uprid($prid, $params) {
//    $uprid = $prid;
//    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
//      while (list($option, $value) = each($params)) {
//        $uprid = $uprid . '{' . $option . '}' . $value;
//      }
//    }
//    return $uprid;
//  }
function go_get_uprid($prid, $params) {
  if (is_numeric($prid)) {
    $uprid = $prid;

    if (is_array($params) && (sizeof($params) > 0)) {
      $attributes_check = true;
      $attributes_ids = '';

      reset($params);
      while (list($option, $value) = each($params)) {
        if (is_numeric($option) && is_numeric($value)) {
          $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
        } else {
          $attributes_check = false;
          break;
        }
      }

      if ($attributes_check == true) {
        $uprid .= $attributes_ids;
      }
    }
  } else {
    $uprid = go_get_prid($prid);

    if (is_numeric($uprid)) {
      if (strpos($prid, '{') !== false) {
        $attributes_check = true;
        $attributes_ids = '';

        // strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
        $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

        for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {
          $pair = explode('}', $attributes[$i]);

          if (is_numeric($pair[0]) && is_numeric($pair[1])) {
            $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ($attributes_check == true) {
          $uprid .= $attributes_ids;
        }
      }
      } else {
        return false;
      }
    }

    return $uprid;
  }



  //  function go_get_prid($uprid) {
  //    $pieces = explode('{', $uprid);
  //    return $pieces[0];
  //  }

  function go_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    if (is_numeric($pieces[0])) {
      return $pieces[0];
    } else {
      return false;
    }
  }

  function go_attendee_greeting() {
    global $attendee_id, $attendee_first_name;
    if (go_session_is_registered('attendee_first_name') && go_session_is_registered('attendee_id')) {
      $greeting_string = sprintf(TEXT_GREETING_PERSONAL, go_output_string_protected($attendee_first_name), go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_38]));
    } else {
      $greeting_string = sprintf(TEXT_GREETING_GUEST, go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_40], '', 'SSL'), go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_10], '', 'SSL'), go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_11], '', 'SSL'));
    }
    return $greeting_string;
  }
  function go_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') return false;
    $message = new email(array('X-Mailer: bigware Mailer'));
    // vorbereiten für "nur text"
    $text = preg_replace('/<br>|<br \/>/', "\n", $email_text);
    $text = strip_tags($text);
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }
    $message->build_message();
    if ($message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject) === true) {// Prüfung, ob die Email verschickt wurde Gulliver72
      return true; // Email wurde verschickt
    } else {
      return false; // Email wurde nicht verschickt
    }
  }
  function go_has_item_characteristics($items_id) {
    $characteristics_query = go_db_query("select count(*) as count from " . DB_TBL_ITEMS_CHARACTERISTICS . " where items_id = '" . (int)$items_id . "'");
    $characteristics = go_db_fetch_array($characteristics_query);
    if ($characteristics['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }
  function go_word_count($string, $needle) {
    $temp_array = preg_split("/$needle/", $string);
    return sizeof($temp_array);
  }
  function go_count_modules($modules = '') {
    $count = 0;
    if (empty($modules)) return $count;
    $modules_array = preg_split('/;/', $modules);
    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));
      if (isset($GLOBALS[$class]) && is_object($GLOBALS[$class])) { // undefinierte Variable gefixt Gulliver72
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }
    return $count;
  }
  function go_count_payment_modules() {
    return go_count_modules(CONSTITUENT_PAYMENT_INSTALLED);
  }
  function go_count_shipping_modules() {
    return go_count_modules(CONSTITUENT_SHIPPING_INSTALLED);
  }
  function go_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;
    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = go_rand(0,9);
      } else {
        $char = chr(go_rand(0,255));
      }
      if ($type == 'mixed') {
        if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
      }
    }
    return $rand_value;
  }
  function go_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();
    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }
    return $get_string;
  }
  function go_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }
  function go_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }
    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }
    return $value;
  }
  function go_currency_exists($code) {
    $code = go_db_producing_input($code);
    $currency_code = go_db_query("select currencies_id from " . DB_TBL_CURRENCIES . " where code = '" . go_db_input($code) . "'");
    if (go_db_num_rows($currency_code)) {
      return $code;
    } else {
      return false;
    }
  }
  function go_string_to_int($string) {
    return (int)$string;
  }
  function go_analysis_category_path($bigPfad) {
    $bigPfad_array = array_map('go_string_to_int', explode('_', $bigPfad));
    $tmp_array = array();
    $n = sizeof($bigPfad_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($bigPfad_array[$i], $tmp_array)) {
        $tmp_array[] = $bigPfad_array[$i];
      }
    }
    return $tmp_array;
  }
  function go_rand($min = null, $max = null) {
    static $seeded;
    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }
    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }
  function go_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
    $cookie = setcookie($name, $value, $expire, $path, (go_not_null($domain) ? $domain : ''), $secure);
    return $cookie;
  }
  function go_get_ip_address() {
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    } else {
      if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
      } elseif (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
      } else {
        $ip = getenv('REMOTE_ADDR');
      }
    }
    return $ip;
  }
  function go_count_attendee_orders($id = '', $check_session = true) {
    global $attendee_id;
    if (is_numeric($id) == false) {
      if (go_session_is_registered('attendee_id')) {
        $id = $attendee_id;
      } else {
        return 0;
      }
    }
    if ($check_session == true) {
      if ( (go_session_is_registered('attendee_id') == false) || ($id != $attendee_id) ) {
        return 0;
      }
    }
    $orders_check_query = go_db_query("select count(*) as total from " . DB_TBL_ORDERS . " where attendees_id = '" . (int)$id . "'");
    $orders_check = go_db_fetch_array($orders_check_query);
    return $orders_check['total'];
  }
  function go_count_attendee_directory_to_address_entries($id = '', $check_session = true) {
    global $attendee_id;
    if (is_numeric($id) == false) {
      if (go_session_is_registered('attendee_id')) {
        $id = $attendee_id;
      } else {
        return 0;
      }
    }
    if ($check_session == true) {
      if ( (go_session_is_registered('attendee_id') == false) || ($id != $attendee_id) ) {
        return 0;
      }
    }
    $addresses_query = go_db_query("select count(*) as total from " . DB_TBL_DIRECTORY_TO_ADDRESS . " where attendees_id = '" . (int)$id . "'");
    $addresses = go_db_fetch_array($addresses_query);
    return $addresses['total'];
  }
  function go_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }
  require(FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_FUNCTIONS . $GLOBALS['CONFIG_NAME_FILE_FUNCTIONS']['function_bigware_7']);
  function go_get_sources($sources_id = '') {
    $sources_array = array();
    if (go_not_null($sources_id)) {
      $sources = go_db_query("select sources_name from " . DB_TBL_SOURCES . " where sources_id = '" . (int)$sources_id . "'");
      $sources_values = go_db_fetch_array($sources);
      $sources_array = array('sources_name' => $sources_values['sources_name']);
    } else {
      $sources = go_db_query("select sources_id, sources_name from " . DB_TBL_SOURCES . " order by sources_name");
      while ($sources_values = go_db_fetch_array($sources)) {
        $sources_array[] = array('sources_id' => $sources_values['sources_id'],
            'sources_name' => $sources_values['sources_name']);
      }
    }
    return $sources_array;
  }
  function go_get_attendees_group($default = '') {
    $attendees_group_array = array();
    if ($default) {
      $attendees_group_array[] = array('id' => '',
          'text' => $default);
    }
    $attendees_group_query = go_db_query("select g.attendees_group_id, g.attendees_group_name from " . DB_TBL_ATTENDEES_GROUPS . " as g, ". DB_TBL_ATTENDEES ." as c WHERE g.attendees_group_id = c.attendees_group_id order by attendees_group_name");
    $attendees_group['attendees_group_id'] = $attendees_groups_id;
    $attendees_group_array[] = array('id' => $attendees_groups_id,
        'text' => $attendees_group['attendees_group_name']);
    return $attendees_group_array;
  }
  function go_get_attendee_group_id($attendee_id) {
    $g = go_db_query("select attendees_group_id from ".DB_TBL_ATTENDEES." where attendees_id = '".$attendee_id."'");
    $g1 = go_db_fetch_array($g);
    if (is_array($g1) & isset($g1['attendees_group_id'])) {// undefinierter Index gefixt Gulliver72
      return $g1['attendees_group_id'];
    } else {
      return 0;
    }
  }
  function bigware_group_price ($item_id) {
    global $item_id, $attendee_group_id, $price;

    $item_info_query = go_db_query("select p.items_id, pd.items_name, pd.items_description, p.items_model, p.items_quantity, p.items_picture, pd.items_url, p.items_price, p.items_tax_class_id, p.items_date_added, p.items_date_available, p.items_date_available_end, p.producers_id from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_status = '1' and p.items_id = '" . (int)$item_id . "' and pd.items_id = p.items_id and pd.language_id = '" . (int)$languages_id . "' $date_availability ");
    $item_info = go_db_fetch_array($item_info_query);

    $attendee_group_price_query = go_db_query("select attendees_group_price from " . DB_TBL_ITEMS_GROUPS . " where items_id = '" . (int)$_GET['items_id'] . "' and attendees_group_id =  '" . bigware_group_attendee($attendee_group_id) . "'");
    if ( bigware_group_attendee($attendee_group_id) != 0) {
      if ($attendee_group_price = go_db_fetch_array($attendee_group_price_query)) {
        $items_price = "";
        $items_price = $currencies->display_price_b2b($attendee_group_price['attendees_group_price'], go_get_tax_rate($item_info['items_tax_class_id']));
      } else {
        $items_price = $currencies->display_price($price, go_get_tax_rate($item_info['items_tax_class_id']));
      }
    } else {
      $items_price = $currencies->display_price($item_info['items_price'], go_get_tax_rate($item_info['items_tax_class_id']));
    }
    return $items_price;
  }
  function go_get_payment_unallowed ($pay_check) {
    $bigwaregroupid = go_get_attendee_group_id($attendee_id);
    $payments = go_db_query("select group_payment_unallowed from " . DB_TBL_ATTENDEES_GROUPS . " where attendees_group_id = '" . $bigwaregroupid . "'");
    $payments_not_allowed = go_db_fetch_array($payments);
    $payments_unallowed = explode (",",$payments_not_allowed['group_payment_unallowed']);
    $clearance = (!in_array ($pay_check, $payments_unallowed)) ? true : false;
    return $clearance;
  }
  function go_count_galeries_in_category($category_id, $include_inactive = false) {
    $galeries_count = 0;
    if ($include_inactive == true) {
      $galeries_query = go_db_query("select count(*) as total from " . DB_TBL_GALERIE . " p, " . DB_TBL_GALERIE_TO_CATEGORIES . " p2c where p.galeries_id = p2c.galeries_id and p2c.categories_id = '" . (int)$category_id . "'");
    } else {
      $galeries_query = go_db_query("select count(*) as total from " . DB_TBL_GALERIE . " p, " . DB_TBL_GALERIE_TO_CATEGORIES . " p2c where p.galeries_id = p2c.galeries_id and p.galeries_status = '1' and p2c.categories_id = '" . (int)$category_id . "'");
    }
    $galeries = go_db_fetch_array($galeries_query);
    $galeries_count += $galeries['total'];
    $child_categories_query = go_db_query("select categories_id from " . DB_TBL_GALERIE_CAT . " where parent_id = '" . (int)$category_id . "'");
    if (go_db_num_rows($child_categories_query)) {
      while ($child_categories = go_db_fetch_array($child_categories_query)) {
        $galeries_count += go_count_galeries_in_category($child_categories['categories_id'], $include_inactive);
      }
    }
    return $galeries_count;
  }
  function go_has_galerie_cat_subcategories($category_id) {
    $child_category_query = go_db_query("select count(*) as count from " . DB_TBL_GALERIE_CAT . " where parent_id = '" . (int)$category_id . "'");
    $child_category = go_db_fetch_array($child_category_query);
    if ($child_category['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }
  function go_get_galeries_stock($galeries_id) {
    $count_gal_to_pic_id_query = go_db_query("select count(*) as total from " . DB_TBL_GALERIE_TO_PICTURES . " where galeries_id = '" . $galeries_id . "' AND vorschau != '1'");
    $count_gal_to_pic_id = go_db_fetch_array($count_gal_to_pic_id_query);
    return $count_gal_to_pic_id['total'];
  }

  function array_to_hiddenfields($my_items) {

    $input_fields = '';
    $zahler = '0';
    if (is_array($my_items)) {
      foreach ($my_items as $item_key => $value1) {
        if (is_array($value1)) {
          foreach ($value1 as $key1 => $value2) {
            if (is_array($value2)) {
              foreach ($value2 as $key2 => $value3) {
                if (is_array($value3)) {
                  foreach ($value3 as $key3 => $value4) {
                    if (is_array($value4)) {
                      foreach ($value4 as $key4 => $value5) {
                        if (is_array($value5)) {
                          foreach ($value5 as $key5 => $value6) {
                            if (is_array($value7)) {
                            }
                            else {
                              $input_fields .= '<input type=hidden name="' . $item_key . '[' . $key1 . '][' . $key2 . '][' . $key3 . '][' . $key4 . '][' . $key5 . ']" value="' . $value6 .'">' . "\n";
                            }
                          }
                        }
                        else {
                          $input_fields .= '<input type=hidden name="' . $item_key . '[' . $key1 . '][' . $key2 . '][' . $key3 . '][' . $key4 . ']" value="' . $value5 .'">' . "\n";
                        }
                      }
                    }
                    else {
                      $input_fields .= '<input type=hidden name="' . $item_key . '[' . $key1 . '][' . $key2 . '][' . $key3 . ']" value="' . $value4 .'">' . "\n";
                    }
                  }
                }
                else {
                  $input_fields .= '<input type=hidden name="' . $item_key . '[' . $key1 . '][' . $key2 . ']" value="' . $value3 .'">' . "\n";
                }
              }
            }
            else {
              $input_fields .= '<input type=hidden name="' . $item_key . '[' . $key1 . ']" value="' . $value2 .'">' . "\n";
            }
          }
        }
        else {
          $input_fields .= '<input type=hidden name="' . $item_key . '" value="' . $value1 .'">' . "\n";
        }
        $zahler++;
      }
    }

    return $input_fields;
  }

  function array_to_variable ($array) {
    if (!is_array ($array)) {
      $new_array = '';
      return $new_array;
    }
    foreach ($array as $key => $val) {
      if (is_array ($array[$key])) {
        array_to_variable ($array[$key]);
        // make Variable with name of key in Globals
        //$GLOBALS[$key] = $val;
        $new_array[$key] = $val;
      } else {
        // make Variable with name of key in Globals
        //$GLOBALS[$key] = $val;
        $new_array[$key] = $val;
      }
    }
    return $new_array;
  }

  function array_in_one_hidden_encode($my_items) {

    $my_array_hidden = serialize($my_items);
    $my_array_hidden = base64_encode($my_array_hidden);
    $hidden_field = '<input name="my_items" type="hidden" value="' . $my_array_hidden . '">';

    return $hidden_field;
  }

  function objekt_encode($my_items) {

    $my_array_hidden = serialize($my_items);
    $my_array_hidden = base64_encode($my_array_hidden);

    return $my_array_hidden;
  }

  function array_in_one_hidden_decode($my_hidden) {

    $my_hidden_array = base64_decode ($my_hidden);
    $my_array = unserialize($my_hidden_array);

    return $my_array;
  }
  ////////////////////////////////////////////////////////////////

  //      $options_name = go_configurator_options_name($items_options_conf_id, $is_items_id);
  //      $values_name = go_configurator_values_name($items_options_conf_values_id, $is_items_id);
  //      $values_description = go_configurator_description($options_conf_values_id, $is_items_id);
  //      $characteristics_price = go_configurator_price($items_characteristics_conf_id, $is_items_id);
  //      $characteristics_price_prefix = go_configurator_price_prefix($items_characteristics_conf_id, $is_items_id);

  function go_configurator_options_name($options_conf_id, $is_items_id) {
    global $languages_id;
    if ($is_items_id == '0') {
      $options_conf = go_db_query("select items_options_conf_name from " . DB_TBL_CONFIGURATOR_OPTION . " where items_options_conf_id = '" . (int)$options_conf_id . "' and language_id = '" . (int)$languages_id . "'");
      $options_conf_values = go_db_fetch_array($options_conf);
      return $options_conf_values['items_options_conf_name'];
    } else {
      $options_conf = go_db_query("select pd.items_name from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where pd.items_id = p.items_id and pd.items_id = " . $is_items_id . " and pd.language_id = '" . (int)$languages_id . "' order by pd.items_name");
      $options_conf_values = go_db_fetch_array($options_conf);
      return $options_conf_values['items_name'];
    }
  }
  function go_configurator_values_name($values_conf_id, $is_items_id) {
    global $languages_id;
    if ($is_items_id == '0') {
      $values_conf = go_db_query("select items_options_conf_values_name from " . DB_TBL_CONFIGURATOR_OPTIONS_VALUES . " where items_options_conf_values_id = '" . (int)$values_conf_id . "' and language_id = '" . (int)$languages_id . "'");
      $values_conf_values = go_db_fetch_array($values_conf);
      return $values_conf_values['items_options_conf_values_name'];
    } else {
      $values_conf = go_db_query("select pd.items_name2 from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where pd.items_id = p.items_id and pd.items_id = " . $is_items_id . " and pd.language_id = '" . (int)$languages_id . "' order by pd.items_name");
      $values_conf_values = go_db_fetch_array($values_conf);
      return $values_conf_values['items_name2'];
    }
  }
  function go_configurator_description($values_conf_id, $is_items_id) {
    global $languages_id;
    if ($is_items_id == '0') {
      $values_conf = go_db_query("select items_options_conf_values_desc from " . DB_TBL_CONFIGURATOR_OPTIONS_VALUES . " where items_options_conf_values_id = '" . (int)$values_conf_id . "' and language_id = '" . (int)$languages_id . "'");
      $values_conf_values = go_db_fetch_array($values_conf);
      return $values_conf_values['items_options_conf_values_desc'];
    } else {
      $values_conf = go_db_query("select pd.items_description from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where pd.items_id = p.items_id and pd.items_id = " . $is_items_id . " and pd.language_id = '" . (int)$languages_id . "' order by pd.items_name");
      $values_conf_values = go_db_fetch_array($values_conf);
      return $values_conf_values['items_description'];
    }
  }
  function go_configurator_price($items_characteristics_conf_id, $is_items_id) {
    global $languages_id;
    if ($is_items_id == '0') {
      $price_conf = go_db_query("select options_conf_values_price from " . DB_TBL_CONFIGURATOR_CHARACTERISTICS . " where items_characteristics_conf_id = '" . (int)$items_characteristics_conf_id . "'");
      $price_conf_values = go_db_fetch_array($price_conf);
      return $price_conf_values['options_conf_values_price'];
    } else {
      $price_conf = go_db_query("select p.items_price from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where pd.items_id = p.items_id and pd.items_id = " . $is_items_id . " and pd.language_id = '" . $languages_id . "' order by pd.items_name");
      $price_conf_values = go_db_fetch_array($price_conf);
      return $price_conf_values['items_price'];
    }
  }
  function go_configurator_price_prefix($items_characteristics_conf_id, $is_items_id) {
    global $languages_id;
    if ($is_items_id == '0') {
      $price_prefix_conf = go_db_query("select price_prefix from " . DB_TBL_CONFIGURATOR_CHARACTERISTICS . " where items_characteristics_conf_id = '" . (int)$items_characteristics_conf_id . "'");
      $price_prefix = go_db_fetch_array($price_prefix_conf);
      return $price_prefix['price_prefix'];
    } else {
      $price_prefix = '+';
      return $price_prefix;
    }
  }
  /////////////////////////////////////////////////////////////////////

  function picture_frame() {
//            global $db_link;

    $query = "SELECT * FROM rahmen WHERE id = '1'";
    $result = go_db_query($query);
    $number = go_db_numrows($result);
    $rahmennummer = go_db_result($result,0,"rahmennummer");

    if ($rahmennummer != 3) {
      $begin_frame = '
        <table cellspacing="0" cellpadding="0" border="0" class="paspardu">
        <tr style="height:5px; font-size:1pt;">
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/oben_links' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0; height:5px;"></th>
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/oben_linie' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0;"></th>
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/oben_rechts' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0;"></th>
        </tr>
        <tr style="width:5px;">
        <td style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/links_linie' . $rahmennummer . '.gif); background-repeat:repeat-y; border:0; width:5px;">
          <img ALT src="' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/links_linie' . $rahmennummer . '.gif"></td>
        <td bgcolor="#FFFFFF" align="center" class="paspardu">
        ';
      $end_frame = '
        </td>
        <td style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/rechts_linie' . $rahmennummer . '.gif); background-repeat:repeat-y; border:0;">
          <img ALT src="' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/rechts_linie' . $rahmennummer . '.gif"></td>
        </tr>
        <tr>
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/unten_links' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0; height:6px;"></th>
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/unten_linie' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0;"></th>
        <th style="background-image:url(' . FOLDER_RELATIV_TEMPLATES . 'picture_frame/unten_rechts' . $rahmennummer . '.gif); background-repeat:repeat-x; border:0;"></th>
        </tr>
        </TABLE>
        ';
    } else {
      $begin_frame = ''; // undefinierte Variable gefixt Gulliver72
      $end_frame = ''; // undefinierte Variable gefixt Gulliver72
    }


    return array("begin_frame" => $begin_frame, "end_frame" => $end_frame);
  }

  function make_picture_popup($items_id, $items_picture, $items_name, $popup_text, $selected_pic) {
    if (POPBOX == 'True') {
      global $binary_gateway, $languages_id, $date_availability, $request_type; // undefinierte Variable gefixt Gulliver72 03.11.2015
      $width = SMALL_PICTURE_WIDTH;
      $height = SMALL_PICTURE_HEIGHT;
      $style = '';
      if ($binary_gateway == '') {
        if ( (CONFIG_CALCULATE_PICTURE_SIZE == 'true') && (empty($width) || empty($height)) ) {
          if ($picture_size = @getimagesize(FOLDER_RELATIV_PICTURES . $items_picture)) {
            if (empty($width) && go_not_null($height)) {
              $ratio = $height / $picture_size[1];
              $width = $picture_size[0] * $ratio;
            } elseif (go_not_null($width) && empty($height)) {
              $ratio = $width / $picture_size[0];
              $height = $picture_size[1] * $ratio;
            } elseif (empty($width) && empty($height)) {
              $width = $picture_size[0];
              $height = $picture_size[1];
            }
          }
        }
      }
      if (go_not_null($width)) {
        $style .= 'width: ' . go_output_string($width) . 'px; ';
      }
      if (go_not_null($height)) {
        $style .= 'height: ' . go_output_string($height) . 'px;';
      }
      $items_query = go_db_query("select pd.items_name, p.items_picture, p.items_bpicture, p.items_3picture, p.items_4picture, p.items_5picture, p.items_id from ((" . DB_TBL_ITEMS . " p) left join " . DB_TBL_ITEMS_DESCRIPTION . " pd on p.items_id = pd.items_id) where p.items_status = '1' and p.items_id = '" . $items_id . "' and pd.language_id = '" . $languages_id . "' $date_availability ");
      $items_values = go_db_fetch_array($items_query);
      $komma = '0';
      $htmlcode = '<script>
        var image_set = [';
      $immage_count = '0';
      $first_pic = '0'; // Variable initialisiert Gulliver72
      if ($items_values['items_bpicture'] != '') {
        $immage_count++;
        if ($komma == '1') {$htmlcode .= ', ';}
        $pic_url = preg_match('#^http#i', $items_values['items_bpicture']) ? $items_values['items_bpicture'] : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_values['items_bpicture'];
        $htmlcode .= '{\'caption\': \'' . $items_values['items_name'] . '\', \'url\': \'' . $pic_url . '\'}';
        if ($selected_pic == '1') {
          $first_pic = $immage_count;
          $first_pic_url = $pic_url;
        }
        $komma = '1';
      } else {
        $komma = '0';
      }
      if ($items_values['items_picture'] != '') {
        $immage_count++;
        if ($komma == '1') {$htmlcode .= ', ';}
        $pic_url = preg_match('#^http#i', $items_values['items_picture']) ? $items_values['items_picture'] : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_values['items_picture'];
        $htmlcode .= '{\'caption\': \'' . $items_values['items_name'] . '\', \'url\': \'' . $pic_url . '\'}';
        if ($selected_pic == '2') {
          $first_pic = $immage_count;
          $first_pic_url = $pic_url;
        }
        $komma = '1';
      } else {
        $komma = '0';
      }
      if ($items_values['items_3picture'] != '') {
        $immage_count++;
        if ($komma == '1') {$htmlcode .= ', ';}
        $pic_url = preg_match('#^http#i', $items_values['items_3picture']) ? $items_values['items_3picture'] : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_values['items_3picture'];
        $htmlcode .= '{\'caption\': \'' . $items_values['items_name'] . '\', \'url\': \'' . $pic_url . '\'}';
        if ($selected_pic == '3') {
          $first_pic = $immage_count;
          $first_pic_url = $pic_url;
        }
        $komma = '1';
      } else {
        $komma = '0';
      }
      if ($items_values['items_4picture'] != '') {
        $immage_count++;
        if ($komma == '1') {$htmlcode .= ', ';}
        $pic_url = preg_match('#^http#i', $items_values['items_4picture']) ? $items_values['items_4picture'] : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_values['items_4picture'];
        $htmlcode .= '{\'caption\': \'' . $items_values['items_name'] . '\', \'url\': \'' . $pic_url . '\'}';
        if ($selected_pic == '4') {
          $first_pic = $immage_count;
          $first_pic_url = $pic_url;
        }
        $komma = '1';
      } else {
        $komma = '0';
      }
      if ($items_values['items_5picture'] != '') {
        $immage_count++;
        if ($komma == '1') {$htmlcode .= ', ';}
        $pic_url = preg_match('#^http#i', $items_values['items_5picture']) ? $items_values['items_5picture'] : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_values['items_5picture'];
        $htmlcode .= '{\'caption\': \'' . $items_values['items_name'] . '\', \'url\': \'' . $pic_url . '\'}';
        if ($selected_pic == '5') {
          $first_pic = $immage_count;
          $first_pic_url = $pic_url;
        }
        $komma = '1';
      } else {
        $komma = '0';
      }
      $htmlcode .= '];';
//      if ($komma == '0') $first_pic_url = (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . 'alle/no_picture.gif'; // wenn kein Artikelbild gespeichert, dann no_picture.gif Gulliver72

      $popup_text = preg_replace("/\<br\>/", " ", $popup_text);
      $htmlcode .= '  </script>
          <a href="' . $first_pic_url . '" onmouseover="Tip(\'' . $popup_text . '\')" onmouseout="UnTip()" onclick="return GB_showImageSet(image_set, ' . $first_pic . ')" title="' . addslashes($items_name) . '">
          <img src="' . $first_pic_url . '" style="' . $style . '" alt="" title="' . $popup_text . '">
          </a>
          ';
    } else {
      $picture = preg_match('#^http#i', $items_picture) ? $items_picture : (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . FOLDER_RELATIV_CATALOG . FOLDER_RELATIV_PICTURES . $items_picture;
      $popup_text = preg_replace("/\<br\>/", " ", $popup_text);
      $htmlcode = '
<script language="javascript"><!--
  document.write(\'<a onmouseover="Tip(\\\'' . $popup_text . '\\\')" onmouseout="UnTip()"  href="javascript:popupWindow(\\\'' . go_href_link($GLOBALS[CONFIG_NAME_FILE][popup_picture], 'bigID=' . $items_id . '&pictures=' . $selected_pic) . '\\\')">' . go_picture($picture, addslashes($items_name), SMALL_PICTURE_WIDTH, SMALL_PICTURE_HEIGHT, 'hspace="5" vspace="5"') . '</a>\');
//--></script>
<noscript>
  <a href="' . go_href_link($picture) . '" target="_blank">' . go_picture($picture, $items_name, SMALL_PICTURE_WIDTH, SMALL_PICTURE_HEIGHT, 'hspace="5" vspace="5"') . '</a>
</noscript> ';
    }
    return $htmlcode;
  }

  function get_price_to_pic($string) {
    if (PRICE_PICTURE == 'True') {
    if (defined('CLASS_FONT_COMPATIBLE_AND_EXIST') && CLASS_FONT_COMPATIBLE_AND_EXIST == 1 AND PRICE_FONT_CLASS_USE == 1) {
      // Zusammenbauen, wenn String
      if (is_array($string)) {
        $string_price = $string[0][0];
        $string_price .= $string[0][1];
        $string_price .= $string[0][2];
        $string_price .= $string[0][3];
        $string_rest = $string[1];
        $string_rest .= $string[2];
      } else {
        $string_price = $string;
      }

      $output = '<img ALT src="' . FOLDER_RELATIV_TEMPLATES . 'font_class_output_price.php?txt=' . $string_price . '">';
        $output .= $string_rest;

        return $output;

    } else {
      // Zusammenbauen, wenn String
      if (is_array($string)) {
        $string_new = $string[0][0];
        $string_new .= $string[0][1];
        $string_new .= $string[0][2];
        $string_new .= $string[0][3];
        $string_new .= $string[1];
        $string_new .= $string[2];
        $string = $string_new;
      }
      // Bild => preg_replace_Suchenstring
      $arr2 = array("1" => "1.gif",
          "2" => "2.gif",
          "3" => "3.gif",
          "4" => "4.gif",
          "5" => "5.gif",
          "6" => "6.gif",
          "7" => "7.gif",
          "8" => "8.gif",
          "9" => "9.gif",
          "0" => "0.gif",
          "||" => "punkt.gif",
          "::" => "komma.gif",
          "&euro;" => "euro.gif",
          "€" => "euro.gif",
          "EURO" => "euro.gif",
          "EUR" => "euro.gif",
          "Eur" => "euro.gif",
          "eur" => "euro.gif",
          "*" => "stern.gif");
      $arr = $arr2;

      // Prüfe ob die Bilder von $arr vorhanden sind. Wenn eines nicht vorhanden ist, setzte $stopp
      foreach ($arr as $key => $value) {
        if (!file_exists(FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_PIC_PRICE_TEMPLATES . $value)) { $stopp = '1'; }
      }

      // füge vor und hinter jedem Wert von $arr2 etwas hinzu (siehe function get_price_to_pic_array())
      array_walk($arr2, 'get_price_to_pic_array');

      // Nur wenn alle Bilder vorhanden, dann umwandeln (siehe $stopp)
      if (isset($stopp) && $stopp != '1') {

        // Nur wenn Zahlen vor und hinter dem PUNKT oder dem KOMMA, dann tausche PUNKT oder KOMMA mit "::"
        $suche = '/([0-9])\.([0-9])/';
        $ersetzen = '\1||\2';
        $string = preg_replace($suche, $ersetzen, $string);
        $suche2 = '/([0-9]),([0-9])/';
        $ersetzen2 = '\1::\2';
        $string = preg_replace($suche2, $ersetzen2, $string);

        $string = strtr($string, $arr2);
        $string = '<span style="white-space: nowrap">' . $string . '</span>';
            }
          }
        }
        if (is_array($string) AND PRICE_PICTURE != 'True') {
          $string_new = $string[0][0];
          $string_new .= $string[0][1];
          $string_new .= $string[0][2];
          $string_new .= $string[0][3];
          $string_new .= $string[1];
          $string_new .= $string[2];
          $string = $string_new;
        }
        return $string;
      }

  function get_price_to_pic_array(&$value, $key) {
    $vorne = "<img ALT src='" . FOLDER_RELATIV_PIC_PRICE_TEMPLATES;
    $hinten = "'>";
    $value = $vorne . $value . $hinten;
  }



  function getagent()
  {
    if (strstr($_SERVER['HTTP_USER_AGENT'],'Opera'))    {

      $brows=preg_replace("/.+\(.+\) (Opera |v) {0,1}([0-9,\.]+)[^0-9]*/","Opera \\2",$_SERVER['HTTP_USER_AGENT']);
      if (preg_match('^Opera/.*',$_SERVER['HTTP_USER_AGENT'])) {
        $brows=preg_replace("/Opera/([0-9,\.]+).*/","Opera \\1",$_SERVER['HTTP_USER_AGENT']);    }}
      elseif (strstr($_SERVER['HTTP_USER_AGENT'],'MSIE'))
        $brows=preg_replace("/.+\(.+MSIE ([0-9,\.]+).+/","Internet Explorer \\1",$_SERVER['HTTP_USER_AGENT']);
      elseif (strstr($_SERVER['HTTP_USER_AGENT'],'Firefox'))
        $brows=preg_replace("/.+\(.+rv:.+\).+Firefox/(.*)/","Firefox \\1",$_SERVER['HTTP_USER_AGENT']);
      elseif (strstr($_SERVER['HTTP_USER_AGENT'],'Mozilla'))
        $brows=preg_replace("/.+\(.+rv:([0-9,\.]+).+/","Mozilla \\1",$_SERVER['HTTP_USER_AGENT']);
    else
      $brows=$_SERVER['HTTP_USER_AGENT'];
    return $brows;
  }
  ////////////////////////////////////////////////////////////
  // Checkt ob eine URL existiert
  // Rückgabewert:
  // FALSE = Nein URL existiert nicht oder ist falsch
  // TRUE = URL existiert

  function url_exists($url) {
    if ($url != "") {
      if (! preg_match("/^https?:\/\//", $url) ) {
        return 0;
        // This function requires a fully qualified http:// URL
      } else {
        if (@fopen($url,"r")) {
          return 1;
          // This URL s readable
        } else {
          return 0;
          // This URL is not readable
        }
      }
    } else {
      return 0;
      // no URL entered (yet)
    }
  }
  //
  ////////////////////////////////////////////////////////////
  if ( !function_exists( "search_from_phrase_to_phrase" ) ) {
    ////////////////////////////////////////////////////////////////////////////////
    ////// suche auf einer Webseite einen Bereich "von phrase - bis phrase" //////////////
    //////                und gebe Ihn aus                            //////////////
    //
    // Von "phrase"-Anfang bis "phrase"-Ende, wenn gewünscht mit einem weiteren
    // "phrase"-Anfang und "phrase"-Ende innen drin.
    // Beispiel (mit innere PHRASE): PHRASE Text PHRASE SUCHWORT PHRASE Text PHRASE
    // Beispiel2 (ohne innere PHRASE): PHRASE SUCHWORT PHRASE
    // Rückgabewert: $ausgabe[0] enthält ein Array von Zeichenketten, die auf das komplette Suchmuster passen
    // und $ausgabe[1] ein Array von Zeichenketten, die sich zwischen den Tags befinden.
    // Ausgabe $ausgabe[0][0] enthält also das ganze Suchmuster
    // Ausgabe $ausgabe[1][0] enthält also das Suchergebnis
    //
    // $url = URL der Internetseite
    // wenn $url leer ist, dann wir angenommen das es sich um ein String handelt (siehe $string)
    // $begin_first_phrase = PHRASE der ersten PHRASE (z.B. "irgendwas")
    //     leer lassen, wenn nicht benötigt
    // $in_first_phrase_begin = ist eine PHRASE innerhalb der ersten PHRASE vor dem Suchwort enthalten (z.B. bei "irgendwas jetztdas" = "jetztdas")?
    //     leer lassen, wenn nicht benötigt
    // $in_first_phrase_end = ist eine PHRASE innerhalb der ersten PHRASE nach dem Suchwort enthalten (z.B. bei "irgendwas jetztdas SUCHWORT nochwas" = "nochwas")?
    //    leer lassen, wenn kein innerer tag gesucht werden soll
    // $end_first_phrase = PHRASE der letzten PHRASE (z.B. "irgendwas2")
    //     leer lassen, wenn nicht benötigt
    // $string = Wenn $url leer ist wird der String als Inhalt, der durchsucht werden soll, herangenommen
    //    wenn $string dann auch leer ist, wird gar nichts gemacht

    function search_from_phrase_to_phrase($url = '', $begin_first_phrase, $in_first_phrase_begin, $in_first_phrase_end, $end_first_phrase, $string = '') {


      $search = array("^", "?", "[", "]", ".", "*", "+", "°", "&ordm;");
      $replace = array("\^", "\?", "\[", "\]", "\.", "\*", "\+", "\°", "\&ordm;");
      $stopp = ''; // undefinierte Variable gefixt Gulliver72

      $begin_first_phrase = str_replace($search, $replace, $begin_first_phrase);
      $in_first_phrase_begin = str_replace($search, $replace, $in_first_phrase_begin);
      $in_first_phrase_end = str_replace($search, $replace, $in_first_phrase_end);
      $end_first_phrase = str_replace($search, $replace, $end_first_phrase);

      if ($url != '') {
        $host = $url;
        if (url_exists($host)) {
          $filestring = file_get_contents($host);
        }
      }
      elseif ($string != '') {
        $filestring = $string;
      } else {
        $stopp = '1';
      }
      if ($stopp != '1') {
        if ($begin_first_phrase != '' AND $end_first_phrase != '') {
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°" . $begin_first_phrase . "(.?:" . $in_first_phrase_begin . ")(.*?)(.?:" . $in_first_phrase_end . ")" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°" . $begin_first_phrase . "(.*?)" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°" . $begin_first_phrase . "(.?:" . $in_first_phrase_begin . ")(.*?)" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°" . $begin_first_phrase . "(.*?)(.?:" . $in_first_phrase_end . ")" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
        }
        if ($begin_first_phrase == '' AND $end_first_phrase != '') {
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°(.?:" . $in_first_phrase_begin . ")(.*?)(.?:" . $in_first_phrase_end . ")" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°(.*?)" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°(.?:" . $in_first_phrase_begin . ")(.*?)" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°(.*?)(.?:" . $in_first_phrase_end . ")" . $end_first_phrase . "°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
        }
        if ($begin_first_phrase != '' AND $end_first_phrase == '') {
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°" . $begin_first_phrase . "(.?:" . $in_first_phrase_begin . ")(.*?)(.?:" . $in_first_phrase_end . ")°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°" . $begin_first_phrase . "(.*?)°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°" . $begin_first_phrase . "(.?:" . $in_first_phrase_begin . ")(.*?)°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°" . $begin_first_phrase . "(.*?)(.?:" . $in_first_phrase_end . ")°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
        }
        if ($begin_first_phrase == '' AND $end_first_phrase == '') {
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°(.?:" . $in_first_phrase_begin . ")(.*?)(.?:" . $in_first_phrase_end . ")°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°(.*?)°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin != '') AND ($in_first_phrase_end == '')) {
            preg_match_all("°(.?:" . $in_first_phrase_begin . ")(.*?)°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
          if (($in_first_phrase_begin == '') AND ($in_first_phrase_end != '')) {
            preg_match_all("°(.*?)(.?:" . $in_first_phrase_end . ")°si", $filestring, $ausgabe, PREG_PATTERN_ORDER);
          }
        }
      } else {
        $ausgabe = 0;
      }
      return $ausgabe;
    }
    //
    ////////////////////////// ENDE "von phrase - bis phrase" /////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

  }
  function pic_string_new() {
    return base64_decode ('PHN0eWxlPgo8IS0tCi5jcmlnaHQgICAgICB7IGNvbG9yOiAjODA4MDgwOyBmb250LXNpemU6IDlweDsgZm9udC1mYW1pbHk6IHZlcmRhbmEsIGhlbHZldGljYSwgYXJpYWwgfQouY3JpZ2h0X2xpbmsgeyBjb2xvcjogIzgwODA4MDsgdGV4dC1kZWNvcmF0aW9uOiB1bmRlcmxpbmU7IGZvbnQtc2l6ZTogOXB4OyBmb250LWZhbWlseTogdmVyZGFuYSwgaGVsdmV0aWNhLCBhcmlhbCB9CmE6dmlzaXRlZC5jcmlnaHRfbGluayB7IGNvbG9yOiAjODA4MDgwOyB0ZXh0LWRlY29yYXRpb246IHVuZGVybGluZTsgZm9udC1zaXplOiA5cHg7IGZvbnQtZmFtaWx5OiB2ZXJkYW5hLCBoZWx2ZXRpY2EsIGFyaWFsIH0KYTpob3Zlci5jcmlnaHRfbGluayB7IGNvbG9yOiAjODA4MDgwOyB0ZXh0LWRlY29yYXRpb246IHVuZGVybGluZTsgZm9udC1zaXplOiA5cHg7IGZvbnQtZmFtaWx5OiB2ZXJkYW5hLCBoZWx2ZXRpY2EsIGFyaWFsIH0KLS0+Cjwvc3R5bGU+Cgo8ZGl2IGNsYXNzPSJjcmlnaHQiPgo8Y2VudGVyPgogIDxhIGNsYXNzPSJjcmlnaHRfbGluayIgaHJlZj0iaHR0cDovL3d3dy5iaWd3YXJlLmRlLyI+S29zdGVubG9zZSBTaG9wc29mdHdhcmU8L2E+IHZvbiAKICA8YSBjbGFzcz0iY3JpZ2h0X2xpbmsiIGhyZWY9Imh0dHA6Ly93d3cuYmlnd2FyZS5kZS8iPkJpZ3dhcmUuZGU8L2E+IHwgCiAgPGEgY2xhc3M9ImNyaWdodF9saW5rIiBocmVmPSJodHRwOi8vd3d3LmJpZ3dhcmUuZGUiPlNob3BzeXN0ZW08L2E+IG1pdAoJPGEgY2xhc3M9ImNyaWdodF9saW5rIiBocmVmPSJodHRwOi8vd3d3LmJpZ3dhcmUuZXUiPkJpZ3dhcmUgVGVtcGxhdGU8L2E+ICAKCTxhIGNsYXNzPSJjcmlnaHRfbGluayIgaHJlZj0iaHR0cDovL3d3dy5zaG9wdG9uZXQuZGUiPmJ5PC9hPiAKCTxhIGNsYXNzPSJjcmlnaHRfbGluayIgaHJlZj0iaHR0cDovL3d3dy5wYWdhZG9yLmRlIj5EaXJrIFBhZ2Fkb3I8L2E+IAoJPGEgY2xhc3M9ImNyaWdodF9saW5rIiBocmVmPSJodHRwOi8vd3d3LmhhcmR3YXJlZ2VpZXIuZGUiPnVuZDwvYT4gCgk8YSBjbGFzcz0iY3JpZ2h0X2xpbmsiIGhyZWY9Imh0dHA6Ly93d3cuc3RldWVybWl4LmRlIj5GcmVkIEtvb3A8L2E+Lgo8L2NlbnRlcj4KPC9kaXY+');
  }
  function pic_string_new_css() {
    return base64_decode ('PGRpdiBjbGFzcz0iY2xlYXJlZCI+PC9kaXY+DQogIDxwIGNsYXNzPSJiaWd3YXJlLWZvb3RlciI+PGEgaHJlZj0iaHR0cDovL3d3dy5iaWd3YXJlLmRlLyI+S29zdGVubG9zZSBTaG9wc29mdHdhcmU8L2E+IHZvbiANCiAgPGEgaHJlZj0iaHR0cDovL3d3dy5iaWd3YXJlLmRlLyI+Qmlnd2FyZS5kZTwvYT4NCjwvZGl2Pg==');
  }

  if ( !function_exists( "easy_preg_replace" ) ) {
    ////////////////////////////////////////////////////////////////////////////////
    //////         suchen und ersetzen in einem string                 //////////////
    //////         ohne auf Sonderzeichen zu achten                   //////////////
    //
    // $search = was gesucht wird (auch längere strings möglich)
    // $replace = das gesuchte wird damit ersetzt
    // $body = der string in dem gesucht wird
    //
    // Achtung: Reguläre Ausdrücke sind nicht möglich
    //
    function easy_preg_replace($search, $replace, $body) {


      $search_comment = array("^", "?", "[", "]", ".", "*", "+", "°", "(", ")");
      $replace_comment = array("\^", "\?", "\[", "\]", "\.", "\*", "\+", "\°", "\(", "\)");

      $search = str_replace($search_comment, $replace_comment, $search);
      if ($search != '') {
        $body = preg_replace("°" . $search . "°si", $replace, $body);
      }

      return $body;
    }
    //
    ////////////////////////// ENDE suchen und ersetzen in einem string ////////////
    ////////////////////////////////////////////////////////////////////////////////

  }
  function exact_wordwrap($txt, $maxlines, $maxlen, $break = "<br />\n") {
  $retTxt = array();

  while($maxlines) {
    // keine zeilen mehr
    if (!strlen($txt))
      break;

    // Möglichkeit zum kürzen rückwärts suchen
    $posCut = -1;
    $j = min($maxlen-1, strlen($txt)-1);

    // letzte zeile.. platz für '...' reservieren
    if ($j > $maxlen-3) {
      $j = $maxlen-3;
    }

    if (strlen($txt) <= $maxlen) { // kürzen nicht weiter nötig.. kurz genug
      $retTxt[] = $txt;
      break;
    }

    while ($j > 0) {
      if ($txt[$j] == ' ' || $txt[$j] == ',' || $txt[$j] == '+') { // Möglichkeit zum kürzen
        $posCut = $j;
        break;
      }

      $j--;
    }

    if ($posCut != -1) { // kürzen sauber möglich, sonst abhacken
      $newline = substr($txt, 0, $posCut);

      if ($txt[$posCut] == ' ') // leerzeichen überspringen
        $posCut++;

      $txt = substr($txt, $posCut);

      if ($maxlines == 1 && strlen($txt) > 0)
        $newline .= '...';
    } else {
      if ($maxlines == 1) { // letzte zeile
        $newline = substr($txt, 0, $maxlen-3) . '...';
      } else {
        $newline = substr($txt, 0, $maxlen-1) . '-';
        $txt = substr($txt, $maxlen-1);
      }
    }

    $retTxt[] = $newline;
    $maxlines--;
  }
  return implode($break, $retTxt);
  }
?>
