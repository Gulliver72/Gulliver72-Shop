<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2017  Bigware LTD
  
  $Id: classes_bigware_5.php 4200 2017-09-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

require (str_replace(__FILE__,basename(__FILE__),'DeliveryTime.php'));
class currencies {
  var $currencies; 
  function __construct() {
    $this->currencies();
  }
  function currencies() {
    $this->currencies = array();
    $currencies_query = go_db_query("select code, title, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, roundings, value from " . DB_TBL_CURRENCIES);
    while ($currencies = go_db_fetch_array($currencies_query)) {
      $this->currencies[$currencies['code']] = array('title' => $currencies['title'],
          'symbol_left' => $currencies['symbol_left'],
          'symbol_right' => $currencies['symbol_right'],
          'decimal_point' => $currencies['decimal_point'],
          'thousands_point' => $currencies['thousands_point'],
          'decimal_places' => $currencies['decimal_places'],
          'roundings'=>$currencies['roundings'],
          'value' => $currencies['value']);
    }
  } 
  // if $as_array = 1, the result is array('$symbol_left', '$number_format_price', '$symbol_right', '$small_symbol')
  function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '', $as_array=0) {
    global $currency;
    if (empty($currency_type)) $currency_type = $currency;
    if ($calculate_currency_value == true) {
      $rate = (go_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
      if ($as_array == 1){
        $symbol_left = $this->currencies[$currency_type]['symbol_left'] . ' ';
        $number_format_price = number_format($this->go_round($number * $rate, $this->currencies[$currency_type]['decimal_places'],$this->currencies[$currency_type]['roundings']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
        $symbol_right = ' ' . $this->currencies[$currency_type]['symbol_right'];  
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $small_symbol = ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        } else {
          $small_symbol = '';
        }
        $format_string = array($symbol_left, $number_format_price, $symbol_right, $small_symbol);
      }
      else{
        $format_string = $this->currencies[$currency_type]['symbol_left'] . ' ' . number_format($this->go_round($number * $rate, $this->currencies[$currency_type]['decimal_places'],$this->currencies[$currency_type]['roundings']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . ' ' . $this->currencies[$currency_type]['symbol_right'];  
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      }
    } else {
      if ($as_array == 1){
        $symbol_left = $this->currencies[$currency_type]['symbol_left'] . ' ';
        $number_format_price = number_format($this->go_round($number, $this->currencies[$currency_type]['decimal_places'],$this->currencies[$currency_type]['roundings']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
        $symbol_right = ' ' . $this->currencies[$currency_type]['symbol_right'];
        $format_string = array($symbol_left, $number_format_price, $symbol_right, '');
      }
      else{
        $format_string = $this->currencies[$currency_type]['symbol_left'] . ' ' . number_format($this->go_round($number, $this->currencies[$currency_type]['decimal_places'],$this->currencies[$currency_type]['roundings']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . ' ' . $this->currencies[$currency_type]['symbol_right'];        
      }
    }
    return $format_string;
  }
  function is_set($code) {
    if (isset($this->currencies[$code]) && go_not_null($this->currencies[$code])) {
      return true;
    } else {
      return false;
    }
  }
  function get_value($code) {
    return $this->currencies[$code]['value'];
  }
  function get_decimal_places($code) {
    return $this->currencies[$code]['decimal_places'];
  }

  // if $as_array = 1, the result is array('$format_array', '$html_tag', '$tag')
  // $format_array = array('$symbol_left', '$number_format_price', '$symbol_right', '$small_symbol')
  function display_price($items_price, $items_tax, $html_tag = '', $as_array=0) { 
    global $currencies, $tag;

    //no_price_modules
    if ( defined('NO_PRICE') && NO_PRICE == 'false' ) {
      require(FOLDER_ABSOLUT_CATALOG . 'modules/no_price/class_this/classes_bigware_5.php');
    }
    else{
      if ($as_array == 1){
        $format_array = $this->format( go_add_tax($items_price, $items_tax), true, '', '', '1');
        return array($format_array, $html_tag, $tag);
      }
      else{
        return $this->format(go_add_tax($items_price, $items_tax)). ' ' . $html_tag . $tag;
      }
    }      
  }

  function display_price_conf($items_price) {  
    global $currencies, $tag;    
    //no_price_modules
    if (NO_PRICE == 'false') {
      require(FOLDER_ABSOLUT_CATALOG . 'modules/no_price/class_this/classes_bigware_5_b.php');
    }
    else{
      return $this->format($items_price). ' '.$tag;
    }
  }

  function display_price_shopping_card($items_price, $items_tax, $quantity = 1,$laenge=1000,$breite=1000,$immeter=1000,$inmeter_breite=1000,$itemprice_id, $price_option_comment='', $items_basis_price=0) { 
    global $currencies, $tag;
    //no_price_modules
    if (NO_PRICE == 'false') {
      require(FOLDER_ABSOLUT_CATALOG . 'modules/no_price/class_this/classes_bigware_5_c.php');
    } else {
      if ($itemprice_id == 4){
        return $this->format(go_add_tax($items_price, $items_tax) * $quantity * (($laenge*2/$immeter)+($breite*2/$inmeter_breite)) + (go_add_tax($items_basis_price, $items_tax) * $quantity)). ' '.$tag;  
      } else {
        return $this->format(go_add_tax($items_price, $items_tax) * $quantity * ($laenge/$immeter)*($breite/$inmeter_breite)  + (go_add_tax($items_basis_price, $items_tax) * $quantity)). ' '.$tag;
      }
    }  
  }
  function go_round($number, $precision, $roundings) {
    $number = str_replace(',', '.', $number);
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
  function display_unit_pricing($item,$mytag="",$class="",$s_price = ""){
     if (empty($item['unit_price_option']) || $item['unit_price_option'] == 'none') return '';
     $items_price = empty($s_price)? $item['items_price']:$s_price;
     $output = $this->display_price($items_price/$item['unit_price_factor'], go_get_tax_rate($item['items_tax_class_id'])," / ".$item['unit_price_option'],1);
     $output = implode(" ",$output[0]).' '.$output[1];
     if ($mytag == "br") {
       $output = "<br />".$output;
     }elseif ($mytag){
       $c = empty($class)? "":'class="'.$class.'"';
       $output = sprintf("<%s %s>%s</%s>",$mytag,$c,$output,$mytag);
     }
     return $output;
   }
  function display_special_pricing($item,$attendee_id, $to_pic = true){
    $item = $this->find_item($item);
    if (empty($item)) return;
    $attendees_group_id = go_get_attendee_group_id($attendee_id);
    if ( $attendees_group_id != 0 ){
      $attendee_group_price_query = go_db_query("select attendees_group_price from " . DB_TBL_ITEMS_GROUPS . " where items_id = '" . $item['items_id'] . "' and attendees_group_id =  '" . $attendees_group_id . "'");
       $attendee_group_price = go_db_fetch_array($attendee_group_price_query);
       if ($attendee_group_price ) $item['items_price'] =  $attendee_group_price['attendees_group_price'];
         } 
      $item['specials_new_items_price'] = go_get_items_special_price($item['items_id']);
      $prefix = $subfix = "";
      if ($item['specials_new_items_price']){
        $prefix = '<s>' . $this->display_price($item['items_price'], go_get_tax_rate($item['items_tax_class_id'])) . '</s><br />'. $this->display_unit_pricing($item,'s') ;
       $prefix .= '<br><span class="itemSpecialPrice">';
       $item['items_price'] = $item['specials_new_items_price'];
       $subfix = "</span>";
      }   
     $price = $to_pic?
       get_price_to_pic($this->display_price($item['items_price'], go_get_tax_rate($item['items_tax_class_id']), '<br>', 1)):
       $this->display_price($item['items_price'], go_get_tax_rate($item['items_tax_class_id']));
       return $prefix.$price.$this->get_price_option_text($item).$this->display_unit_pricing( $item, 'br').$subfix;
   }
   function find_item($item){
    if (!is_array($item)){
         $item_query = go_db_query("select * from " . DB_TBL_ITEMS . " where items_id = '" . (int)$item . "'");
        $item = go_db_fetch_array($item_query);
    }
    return $item;
   }
   function get_price_option_text($item_info){
     if ($item_info['items_price_option']!=0) { 
      $getPriceOptionQuery = go_db_query("select text,immeter, inmeter_breite from price_option where id=".(int)$item_info['items_price_option']);
       $price_options = go_db_fetch_array($getPriceOptionQuery);
     }
     return empty($price_options)? "" : " / ".$price_options['text']; 
   }
   function shipping_tag($item){
    global $languages_id;
    $item = $this->find_item($item);
    if (empty($item)) return;
    $delivery_time = new DeliveryTime($languages_id);
    $shipping_tag = $delivery_time->shipping_tag($item);
    return $shipping_tag;
   }
}
?>
