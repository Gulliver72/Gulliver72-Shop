<?php
/**
* ###################################################################################
* 
* Bigware Shop 3.0
* Release Datum: 30.05.2016
* 
* Bigware Shop
* http://www.bigware.de
* 
* Copyright (c) 2018 Bigware LTD
* $Id: Shoppingcart.php 0001 2016-07-20 19:47:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
* ###################################################################################
*/

  class ShoppingCart {
  
    var $contents, $total, $weight, $cartID, $content_type;

    var $shiptotal;
    var $total_before_discount;

    function __construct() {
    
      $this->reset();
    }
    
    function restore_contents() {
    
      global $attendee_id, $gv_id, $REMOTE_ADDR;
  
      if (!go_session_is_registered('attendee_id')) return false;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($items_id, ) = each($this->contents)) {
          $qty = $this->contents[$items_id]['qty'];
          $laenge = $this->contents[$items_id]['laenge'];
          $breite = $this->contents[$items_id]['breite'];
          $immeter = $this->contents[$items_id]['immeter'];
          $inmeter_breite = $this->contents[$items_id]['inmeter_breite'];
          $price_option_comment = $this->contents[$items_id]['price_option_comment'];
          $add_cart_variable_serialize = $this->contents[$items_id]['add_cart_variable_serialize'];
          $add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
          if (is_array ($add_cart_variable)){
            foreach ($add_cart_variable as $key => $val){
              ${$key} = $val;
            }
          }
          $items_price_option_query = go_db_query("select items_price_option, items_basis_price from " . DB_TBL_ITEMS . " where items_id = '" . go_db_input($items_id) . "'");
          $items_price_option_now = go_db_fetch_array($items_price_option_query);
          $items_price_option_result = $items_price_option_now['items_price_option'];
          $items_basis_price = $items_price_option_now['items_basis_price'];
          $getProductsPriceOptionQuery=go_db_query("select id from price_option where id='" . $items_price_option_result . "'");
          if (@go_db_num_rows($getProductsPriceOptionQuery)!=0) {
            $getProductsPriceOption = mysqli_result($getProductsPriceOptionQuery,0,'id');
          }
          else {
            $getProductsPriceOption=0;
          }
    
          $item_query = go_db_query("select items_id from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id) . "'");
          if (!go_db_num_rows($item_query)) {
            go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET . " (attendees_id, items_id, attendees_basket_quantity,attendees_basket_laenge,attendees_basket_breite,attendees_basket_price_option, attendees_basket_date_added, price_option_comment, items_basis_price, add_cart_variable_serialize) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id) . "', '" . $qty . "', '" . $laenge . "', '" . $breite . "',  '" . $getProductsPriceOption . "', '" . date('Ymd') . "', '" . $price_option_comment . "', '" . $items_basis_price . "', '" . $add_cart_variable_serialize . "')");
            if (isset($this->contents[$items_id]['characteristics'])) {
              reset($this->contents[$items_id]['characteristics']);
              while (list($option, $value) = each($this->contents[$items_id]['characteristics'])) {
                if (KONFIGURATOR=='true'){
                  if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
                    include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19.php');
                  }
                  else{
                    if (is_array($value)){
                      $characteristics_keys = array_keys($value);
                      for ($ak=0;$ak<sizeof($characteristics_keys);$ak++){
                        go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id) . "', '" . (int)$option . "', '" . (int)$value[$characteristics_keys[$ak]] . "')");
                      }//for ak
                    }//is array
                    else {
                      go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id) . "', '" . (int)$option . "', '" . (int)$value . "')");
                    }//else not array
                  } // not file_exists
    
                }//konfigurator
                else {
          
                  go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " (attendees_id, items_id, items_options_id, items_options_value_id, items_characteristics_id, options_values_price, price_prefix, items_options_name, items_options_values_name, qty) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id) . "', '" . (int)$option . "', '" . (int)$value . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['items_characteristics_id'] . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['options_values_price'] . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['price_prefix'] . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['items_options_name'] . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['items_options_values_name'] . "', '" . $this->contents[$items_id]['characteristics_more'][$option]['qty'] . "')");
                }
              }//while
            }
          } else {
            // Loopthrough9
            go_db_query("update " . DB_TBL_ATTENDEES_BASKET . " set items_basis_price = '".$items_basis_price."',attendees_basket_quantity = '" . $qty . "',attendees_basket_laenge = '" . $laenge . "',attendees_basket_breite = '" . $breite . "',attendees_basket_price_option='" . $getProductsPriceOption . "', price_option_comment='" . $price_option_comment . "', add_cart_variable_serialize='" . $add_cart_variable_serialize . "' where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id) . "'");
          }
        }
        if (go_session_is_registered('gv_id')) {
          $gv_query = go_db_query("insert into  " . DB_TBL_COUPON_REDEEM_TRACK . " (coupon_id, attendee_id, redeem_date, redeem_ip) values ('" . $gv_id . "', '" . (int)$attendee_id . "', now(),'" . $REMOTE_ADDR . "')");
          $gv_update = go_db_query("update " . DB_TBL_COUPONS . " set coupon_active = 'N' where coupon_id = '" . $gv_id . "'");
          go_gv_member_update($attendee_id, $gv_id);
          go_session_unregister('gv_id');
        }
      }
      $this->reset(false);
      $items_query = go_db_query("select items_id, attendees_basket_quantity, attendees_basket_laenge, attendees_basket_breite, attendees_basket_price_option, price_option_comment, items_basis_price, add_cart_variable_serialize  from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "'");
      while ($items = go_db_fetch_array($items_query)) {
        // Loopthrough10
        $add_cart_variable = array_in_one_hidden_decode($items['add_cart_variable_serialize']);
        if (is_array ($add_cart_variable)){
          foreach ($add_cart_variable as $key => $val){
            $this->contents[$items['items_id']][$key] = $val;
          }
        }
        $this->contents[$items['items_id']]['add_cart_variable_serialize'] =  $items['add_cart_variable_serialize'];
        $this->contents[$items['items_id']]['qty'] =  $items['attendees_basket_quantity'];
        $this->contents[$items['items_id']]['items_basis_price'] =  $items['items_basis_price'];
        $this->contents[$items['items_id']]['laenge'] =  $items['attendees_basket_laenge'];
        $this->contents[$items['items_id']]['breite'] =  $items['attendees_basket_breite'];
        $this->contents[$items['items_id']]['attendees_basket_price_option'] =  $items['attendees_basket_price_option'];
        $this->contents[$items['items_id']]['price_option_comment'] =  $items['price_option_comment'];
        if (KONFIGURATOR=='true'){
  
          if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
            include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19a.php');
          }
          else{
            $characteristics_options_query = go_db_query("select distinct items_options_conf_id from ".DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF." where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items['items_id']) . "'");
            $characteristics_options_num = go_db_num_rows($characteristics_options_query);
            for ($ao=0; $ao<$characteristics_options_num;$ao++){
              $items_options_conf_id[$ao] = mysqli_result($characteristics_options_query, $ao, 'items_options_conf_id');
              $characteristics_query[$ao] = go_db_query("select items_options_conf_id, items_options_conf_value_id from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items['items_id']) . "' and items_options_conf_id='".$items_options_conf_id[$ao]."'");
              $characteristics_options_values_num[$ao]=go_db_num_rows($characteristics_query[$ao]);
              if ($characteristics_options_values_num[$ao]>1){
                for ($aov=0;$aov<$characteristics_options_values_num[$ao];$aov++){
                  $this->contents[$items['items_id']]['characteristics'][mysqli_result($characteristics_query[$ao],$aov,'items_options_conf_id')][$aov] = mysqli_result($characteristics_query[$ao],$aov,'items_options_conf_value_id');
                }
              }//array
              else {
                $this->contents[$items['items_id']]['characteristics'][mysqli_result($characteristics_query[$ao],0,'items_options_conf_id')] = mysqli_result($characteristics_query[$ao],0,'items_options_conf_value_id');
              }
            }
          }
        }//if konfigurator
        else {
          $characteristics_query = go_db_query("select items_options_id, items_options_value_id, items_characteristics_id, options_values_price, price_prefix, items_options_name, items_options_values_name, qty from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items['items_id']) . "'");
          while ($characteristics = go_db_fetch_array($characteristics_query)) {
            $this->contents[$items['items_id']]['characteristics'][$characteristics['items_options_id']] = $characteristics['items_options_value_id'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['items_characteristics_id'] = $characteristics['items_characteristics_id'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['options_values_price'] = $characteristics['options_values_price'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['price_prefix'] = $characteristics['price_prefix'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['items_options_name'] = $characteristics['items_options_name'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['items_options_values_name'] = $characteristics['items_options_values_name'];
            $this->contents[$items['items_id']]['characteristics_more'][$characteristics['items_options_id']]['qty'] = $characteristics['qty'];
          }
        }

      }
      $this->cleanup();
    }
  
    function reset( $reset_database = false ) {
    
      global $attendee_id;

      $this->contents = array();
      $this->total = 0;
      $this->total_before_discount = 0;
      $this->weight = 0;
      $this->shiptotal = 0;
      $this->content_type = false;
      if (go_session_is_registered('attendee_id') && ($reset_database == true)) {
        go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "'");
        go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . (int)$attendee_id . "'");
        go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " where attendees_id = '" . (int)$attendee_id . "'");
      }
      unset($this->cartID);
      if (go_session_is_registered('cartID')) go_session_unregister('cartID');
    }
    
    function add_cart($items_id, $qty = '1', $characteristics = '', $notify = true, $laenge=1000, $breite=1000,$immeter=1000,$inmeter_breite=1000, $price_option_comment='', $add_cart_array='') {
    
      global $new_items_id_in_cart, $attendee_id, $languages_id;
  
      if ($immeter=="") {$immeter=1000;}
      if ($inmeter_breite=="") {$inmeter_breite=1000;}
      $items_id_string = go_get_uprid($items_id, $characteristics);
      $items_id = go_get_prid($items_id_string);

      //////////////////
      if (is_numeric($items_id) && is_numeric($qty)) {
        $check_product_query = go_db_query("select items_status from " . DB_TBL_ITEMS . " where items_id = '" . (int)$items_id . "'");
        $check_product = go_db_fetch_array($check_product_query);
        if (($check_product !== false) && ($check_product['items_status'] == '1')) {
          //////////////////
          $add_cart_variable = array_to_variable($add_cart_array);

          if ($notify == true) {
            $new_items_id_in_cart = $items_id;
            go_session_register('new_items_id_in_cart');
          }
          if ($this->in_cart($items_id_string)) {
            $this->update_quantity($items_id_string, $qty, $characteristics,$laenge,$breite,$immeter,$inmeter_breite, $price_option_comment, $add_cart_array);
          } else {
     
            $this->contents[$items_id_string] = array('qty' => $qty);
            $this->contents[$items_id_string]['laenge'] = $laenge;
            $this->contents[$items_id_string]['breite'] = $breite;
            $this->contents[$items_id_string]['immeter'] = $immeter;
            $this->contents[$items_id_string]['inmeter_breite'] = $inmeter_breite;
            $this->contents[$items_id_string]['price_option_comment'] = $price_option_comment;
            $add_cart_variable_serialize = objekt_encode($add_cart_variable);
            $this->contents[$items_id_string]['add_cart_variable_serialize'] = $add_cart_variable_serialize;
    
            if (is_array ($add_cart_variable)){
              foreach ($add_cart_variable as $key => $val){
                $this->contents[$items_id_string][$key] = $val;
                ${$key} = $val;
              }
            }


            $items_price_option_query = go_db_query("select items_price_option, items_basis_price from " . DB_TBL_ITEMS . " where items_id = '" . go_db_input($items_id) . "'");
            $items_price_option_now = go_db_fetch_array($items_price_option_query);
            $items_price_option_result = $items_price_option_now['items_price_option'];
            $items_basis_price = $items_price_option_now['items_basis_price'];
            $getProductsPriceOptionQuery=go_db_query("select id from price_option where id='" . $items_price_option_result . "'");
            if (@go_db_num_rows($getProductsPriceOptionQuery)!=0) {
              $getProductsPriceOption = mysqli_result($getProductsPriceOptionQuery,0,'id');
            }
            else {
              $getProductsPriceOption=0;
            }
            if (go_session_is_registered('attendee_id')){
              $add_cart_variable_serialize = objekt_encode($add_cart_variable);
              go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET . " (attendees_id, items_id, attendees_basket_quantity,attendees_basket_laenge,attendees_basket_breite,attendees_basket_price_option , attendees_basket_date_added, price_option_comment, add_cart_variable_serialize, items_basis_price) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id_string) . "', '" . (int)$qty . "','" . $laenge . "', '" . $breite . "', '" . $getProductsPriceOption. "', '" . date('Ymd') . "', '" . $price_option_comment . "', '" . $add_cart_variable_serialize . "', '". $items_basis_price."')");
            }
            if (KONFIGURATOR=='true'){

              if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
                include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19b.php');
              }
              else{
                if (is_array($characteristics)) {
                  reset($characteristics);
                  while (list($option, $value) = each($characteristics)) {
                    if (is_array($value)){
                      $characteristics_keys = array_keys($value);
                      for ($ak=0;$ak<sizeof($characteristics_keys);$ak++){
                        $this->contents[$items_id_string]['characteristics'][$option][$characteristics_keys[$ak]] = $value[$characteristics_keys[$ak]];
                        if (go_session_is_registered('attendee_id')){
                          go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id_string) . "', '" . (int)$option . "', '" . (int)$value[$characteristics_keys[$ak]] . "')");
                        }
                      }//for ak
                    }//is array
                    else {
                      $this->contents[$items_id_string]['characteristics'][$option] = $value;
                      if (go_session_is_registered('attendee_id')) go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$items_id_string . "', '" . go_db_input($items_id) . "', '" . (int)$option . "', '" . (int)$value . "')");
                    }//else not array
                  }
                }
              } // not file_exists
            }
            else {
      
              if (is_array($characteristics)) {
                reset($characteristics);

                while (list($option, $value) = each($characteristics)) {
                  $this->contents[$items_id_string]['characteristics'][$option] = $value;
                  $characteristics_add_to_card = go_db_query("select popt.items_options_name, poval.items_options_values_name, pa.options_values_price, pa.price_prefix, pa.items_characteristics_id, pa.qty
                      from " . DB_TBL_ITEMS_OPTIONS . " popt, " . DB_TBL_ITEMS_OPTIONS_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS . " pa
                      where pa.items_id = '" . go_db_input($items_id) . "'
                      and pa.options_id = '" . (int)$option . "'
                      and pa.options_id = popt.items_options_id
                      and pa.options_values_id = '" . (int)$value . "'
                      and pa.options_values_id = poval.items_options_values_id
                      and popt.language_id = '" . $languages_id . "'
                      and poval.language_id = '" . $languages_id . "'");
                  $characteristics_add_to_card_values = go_db_fetch_array($characteristics_add_to_card);
                  $this->contents[$items_id_string]['characteristics_more'][$option]['items_characteristics_id'] = $characteristics_add_to_card_values['items_characteristics_id'];
                  $this->contents[$items_id_string]['characteristics_more'][$option]['options_values_price'] = $characteristics_add_to_card_values['options_values_price'];
                  $this->contents[$items_id_string]['characteristics_more'][$option]['price_prefix'] = $characteristics_add_to_card_values['price_prefix'];
                  $this->contents[$items_id_string]['characteristics_more'][$option]['items_options_name'] = $characteristics_add_to_card_values['items_options_name'];
                  $this->contents[$items_id_string]['characteristics_more'][$option]['items_options_values_name'] = $characteristics_add_to_card_values['items_options_values_name'];
                  $this->contents[$items_id_string]['characteristics_more'][$option]['qty'] = $characteristics_add_to_card_values['qty'];

                  if (go_session_is_registered('attendee_id')) go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " (attendees_id, items_id, items_options_id, items_options_value_id, items_characteristics_id, options_values_price, price_prefix, items_options_name, items_options_values_name, qty) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id_string) . "', '" . (int)$option . "', '" . (int)$value . "', '" . $characteristics_add_to_card_values['items_characteristics_id'] . "', '" . $characteristics_add_to_card_values['options_values_price'] . "', '" . $characteristics_add_to_card_values['price_prefix'] . "', '" . $characteristics_add_to_card_values['items_options_name'] . "', '" . $characteristics_add_to_card_values['items_options_values_name'] . "', '" . $characteristics_add_to_card_values['qty'] . "')");
                }
                //exit;
              }
            }//!= konfigurator
          }
          $this->cleanup();
          $this->cartID = $this->generate_cart_id();
          ///////////////////
        }
      }
      ///////////////////
    }
    
  function update_quantity($items_id, $quantity = '', $characteristics = '', $laenge=1000, $breite=1000, $immeter=1000, $inmeter_breite=1000, $price_option_comment='', $add_cart_array = '') {
    global $attendee_id;
    global $languages_id;

    $add_cart_variable = array_to_variable($add_cart_array);

    if ($immeter=="") {$immeter=1000;}
    if ($inmeter_breite=="") {$inmeter_breite=1000;}

    $items_id_string = go_get_uprid($items_id, $characteristics);
    $items_id = go_get_prid($items_id_string);


    if (is_numeric($items_id) && isset($this->contents[$items_id_string]) && is_numeric($quantity)) {

      $this->contents[$items_id_string] = array('qty' => $quantity);

      $this->contents[$items_id_string]['laenge'] = $laenge;
      $this->contents[$items_id_string]['breite'] = $breite;
      $this->contents[$items_id_string]['immeter'] = $immeter;
      $this->contents[$items_id_string]['inmeter_breite'] = $inmeter_breite;
      $this->contents[$items_id_string]['price_option_comment'] = $price_option_comment;
      $add_cart_variable_serialize = objekt_encode($add_cart_variable);
      $this->contents[$items_id_string]['add_cart_variable_serialize'] = $add_cart_variable_serialize;
      if (is_array ($add_cart_variable)){
        foreach ($add_cart_variable as $key => $val){
          $this->contents[$items_id_string][$key] = $val;
          ${$key} = $val;
        }
      }


      $items_price_option_query = go_db_query("select items_price_option, items_basis_price from " . DB_TBL_ITEMS . " where items_id = '" . go_db_input($items_id) . "'");
      $items_price_option_now = go_db_fetch_array($items_price_option_query);
      $items_price_option_result = $items_price_option_now['items_price_option'];
      $items_basis_price = $items_price_option_now['items_basis_price'];
      $getProductsPriceOptionQuery=go_db_query("select id from price_option where id='" . $items_price_option_result . "'");
      if (@go_db_num_rows($getProductsPriceOptionQuery)!=0) {
        $getProductsPriceOption = mysqli_result($getProductsPriceOptionQuery,0,'id');
      }
      else {
        $getProductsPriceOption=0;
      }
      if (go_session_is_registered('attendee_id')){
        $add_cart_variable_serialize = objekt_encode($add_cart_variable);
        go_db_query("update " . DB_TBL_ATTENDEES_BASKET . " set attendees_basket_quantity = '" . $quantity . "',attendees_basket_laenge = '" . $laenge . "',attendees_basket_breite = '" . $breite . "', price_option_comment = '" . $price_option_comment . "', add_cart_variable_serialize = '" . $add_cart_variable_serialize . "', items_basis_price = '" . $items_basis_price . "' where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id_string) . "'");
      }
      if (KONFIGURATOR=='true'){

        if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
          include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19c.php');
        }
        else{
          if (is_array($characteristics)) {
            reset($characteristics);
            while (list($option, $value) = each($characteristics)) {
              go_db_query("delete from ".DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF." where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id_string) . "' and items_options_conf_id = '" . (int)$option . "'");
              if (is_array($value)){
                $characteristics_keys = array_keys($value);
                for ($ak=0;$ak<sizeof($characteristics_keys);$ak++){
                  $this->contents[$items_id_string]['characteristics'][$option][$characteristics_keys[$ak]] = $value[$characteristics_keys[$ak]];
                  if (go_session_is_registered('attendee_id')){
                    go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id_string) . "', '" . (int)$option . "', '" . (int)$value[$characteristics_keys[$ak]] . "')");}
                }//for ak
              }//is array
              else {
                $this->contents[$items_id_string]['characteristics'][$option] = $value;
                if (go_session_is_registered('attendee_id')) go_db_query("insert into " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " (attendees_id, items_id, items_options_conf_id, items_options_conf_value_id) values ('" . (int)$attendee_id . "', '" . go_db_input($items_id_string) . "', '" . (int)$option . "', '" . (int)$value . "')");

              }//else not array
            }
          }
        } // not file_exists


      }
      else {
        if (is_array($characteristics)) {
          reset($characteristics);
          while (list($option, $value) = each($characteristics)) {

            $this->contents[$items_id_string]['characteristics'][$option] = $value;
            $characteristics_add_to_card = go_db_query("select popt.items_options_name, poval.items_options_values_name, pa.options_values_price, pa.price_prefix, pa.items_characteristics_id, pa.qty
                from " . DB_TBL_ITEMS_OPTIONS . " popt, " . DB_TBL_ITEMS_OPTIONS_VALUES . " poval, " . DB_TBL_ITEMS_CHARACTERISTICS . " pa
                where pa.items_id = '" . go_db_input($items_id) . "'
                and pa.options_id = '" . (int)$option . "'
                and pa.options_id = popt.items_options_id
                and pa.options_values_id = '" . (int)$value . "'
                and pa.options_values_id = poval.items_options_values_id
                and popt.language_id = '" . $languages_id . "'
                and poval.language_id = '" . $languages_id . "'");
            $characteristics_add_to_card_values = go_db_fetch_array($characteristics_add_to_card);
            $this->contents[$items_id_string]['characteristics_more'][$option]['items_characteristics_id'] = $characteristics_add_to_card_values['items_characteristics_id'];
            $this->contents[$items_id_string]['characteristics_more'][$option]['options_values_price'] = $characteristics_add_to_card_values['options_values_price'];
            $this->contents[$items_id_string]['characteristics_more'][$option]['price_prefix'] = $characteristics_add_to_card_values['price_prefix'];
            $this->contents[$items_id_string]['characteristics_more'][$option]['items_options_name'] = $characteristics_add_to_card_values['items_options_name'];
            $this->contents[$items_id_string]['characteristics_more'][$option]['items_options_values_name'] = $characteristics_add_to_card_values['items_options_values_name'];
            $this->contents[$items_id_string]['characteristics_more'][$option]['qty'] = $characteristics_add_to_card_values['qty'];

            if (go_session_is_registered('attendee_id')) go_db_query("update " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " set items_options_value_id = '" . (int)$value . "' where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id_string) . "' and items_options_id = '" . (int)$option . "'");
          }//while
        }//if
      }//else not konfigurator
    }
  }
  function cleanup() {
    global $attendee_id;
    reset($this->contents);
    while (list($key,) = each($this->contents)) {
      if ($this->contents[$key]['qty'] < 1) {
        unset($this->contents[$key]);
        if (go_session_is_registered('attendee_id')) {
          go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($key) . "'");
          go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($key) . "'");
          go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($key) . "'");
        }
      }
    }
  }
  function count_contents() {
    $total_items = 0;
    if (is_array($this->contents)) {
      reset($this->contents);
      while (list($items_id, ) = each($this->contents)) {
        $total_items += $this->get_quantity($items_id);
      }
    }
    return $total_items;
  }
  function get_quantity($items_id) {
    if (isset($this->contents[$items_id])) {
      return $this->contents[$items_id]['qty'];
    } else {
      return 0;
    }
  }

  function in_cart($items_id) {
    if (isset($this->contents[$items_id])) {
      return true;
    } else {
      return false;
    }
  }
  function remove($items_id) {
    global $attendee_id;
    unset($this->contents[$items_id]);
    if (go_session_is_registered('attendee_id')) {
      go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id) . "'");
      go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id) . "'");
      go_db_query("delete from " . DB_TBL_ATTENDEES_BASKET_CHARACTERISTICS_CONF . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . go_db_input($items_id) . "'");
    }
    $this->cartID = $this->generate_cart_id();
  }
  function remove_all() {
    $this->reset();
  }
  function get_item_id_list() {
    $item_id_list = '';
    if (is_array($this->contents)) {
      reset($this->contents);
      while (list($items_id, ) = each($this->contents)) {
        $item_id_list .= ', ' . $items_id;
      }
    }
    return substr($item_id_list, 2);
  }




  function calculate() {
    //no_price_modules
    if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/no_price')) {
      $no_price_modules = '1';
    }
    $this->total_virtual = 0;
    $this->total = 0;
    $this->weight = 0;
    $this->shiptotal = 0;
    if (!is_array($this->contents)) return 0;
    global $attendee_id, $my_hidden_items;
    $attendee_group_query = go_db_query("select attendees_group_id from " . DB_TBL_ATTENDEES . " where attendees_id =  '" . $attendee_id . "'");
    $attendee_group_id = go_db_fetch_array($attendee_group_query);

    reset($this->contents);
    $count_shiptotal;
    while (list($items_id, ) = each($this->contents)) {
      $qty = $this->contents[$items_id]['qty'];
            $rebate = 0;
      if (go_session_is_registered('attendee_id')) {
        if (!isset($my_hidden_items)) {
          $item_laenge_breite_query = go_db_query("select attendees_basket_laenge, attendees_basket_breite, attendees_basket_price_option, price_option_comment, add_cart_variable_serialize, items_basis_price from " . DB_TBL_ATTENDEES_BASKET . " where attendees_id = '" . (int)$attendee_id . "' and items_id = '" . $items_id . "'");
          if (@go_db_num_rows($item_laenge_breite_query)!=0) {
            $item_laenge_breite_result = go_db_fetch_array($item_laenge_breite_query);
            $add_cart_variable = array_in_one_hidden_decode($item_laenge_breite_result['add_cart_variable_serialize']);
            if (is_array ($add_cart_variable)){
              foreach ($add_cart_variable as $key => $val){
                ${$key} = $val;
              }
            }
            $laenge = $item_laenge_breite_result['attendees_basket_laenge'];
            $breite = $item_laenge_breite_result['attendees_basket_breite'];
            $price_option_comment = $item_laenge_breite_result['price_option_comment'];
            $items_basis_price = $item_laenge_breite_result['items_basis_price'];
          }
          else{
            $laenge = $my_hidden_items[$items_id]['laenge'];
            $breite = $my_hidden_items[$items_id]['breite'];
            $items_basis_price = $my_hidden_items[$items_id]['items_basis_price'];
            $price_option_comment = $my_hidden_items[$items_id]['price_option_comment'];
            $item_laenge_breite_result['attendees_basket_price_option'] = $my_hidden_items[$items_id]['items_price_option'];
            if (is_array ($my_hidden_items[$items_id])){
              foreach ($my_hidden_items[$items_id] as $key => $val){
                ${$key} = $val;
              }
            }
          }

          if ($item_laenge_breite_result['attendees_basket_price_option'] > 0){
            $item_inmeter_query = go_db_query("select immeter, inmeter_breite from price_option where id = '" . $item_laenge_breite_result['attendees_basket_price_option'] . "'");
            $item_inmeter_result = go_db_fetch_array($item_inmeter_query);
            $immeter = $item_inmeter_result['immeter'];
            $inmeter_breite = $item_inmeter_result['inmeter_breite'];
          } else {
            $immeter = $this->cotents[$items_id]['immeter'];
            if ($immeter=="") {$immeter=1000;}
            $inmeter_breite = $this->contents[$items_id]['inmeter_breite'];
            if ($inmeter_breite=="") {$inmeter_breite=1000;}
          }

        }
        else {
          $laenge = $this->contents[$items_id]['laenge'];
          if ($laenge=="") {$laenge=1000;}
          $breite = $this->contents[$items_id]['breite'];
          if ($breite=="") {$breite=1000;}
          $immeter = $this->cotents[$items_id]['immeter'];
          if ($immeter=="") {$immeter=1000;}
          $inmeter_breite = $this->contents[$items_id]['inmeter_breite'];
          if ($inmeter_breite=="") {$inmeter_breite=1000;}
          $price_option_comment = $this->contents[$items_id]['price_option_comment'];
          if ($price_option_comment=="" OR $price_option_comment == 0) {$price_option_comment="";}

          $add_cart_variable_serialize = $this->contents[$items_id]['add_cart_variable_serialize'];
          $add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
          if (is_array ($add_cart_variable)){
            foreach ($add_cart_variable as $key => $val){
              ${$key} = $val;
            }
          }
        }

      }
      else {
        $laenge = $this->contents[$items_id]['laenge'];
        if ($laenge=="") {$laenge=1000;}
        $breite = $this->contents[$items_id]['breite'];
        if ($breite=="") {$breite=1000;}
        $immeter = $this->contents[$items_id]['immeter'];
        if ($immeter=="") {$immeter=1000;}
        $inmeter_breite = $this->contents[$items_id]['inmeter_breite'];
        if ($inmeter_breite=="") {$inmeter_breite=1000;}
        $price_option_comment = $this->contents[$items_id]['price_option_comment'];
        if ($price_option_comment=="" OR $price_option_comment == 0) {$price_option_comment="";}

        $add_cart_variable_serialize = $this->contents[$items_id]['add_cart_variable_serialize'];
        $add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
        if (is_array ($add_cart_variable)){
          foreach ($add_cart_variable as $key => $val){
            ${$key} = $val;
          }
        }
      }
      if (!isset($my_hidden_items)) {
        $item_query = go_db_query("select items_id, items_model, items_price, items_basis_price, items_ship_price, items_ship_price_two, items_tax_class_id, items_weight from " . DB_TBL_ITEMS . " where items_id = '" . (int)$items_id . "'");
        if ($item = go_db_fetch_array($item_query)) {
          $no_count = 1;
          if (preg_match('/^GIFT/', $item['items_model'])) {
            $no_count = 0;
          }
          $prid = $item['items_id'];
          $items_tax = go_get_tax_rate($item['items_tax_class_id']);
          $items_price = $item['items_price'];
          $items_basis_price = $item['items_basis_price'];
          $items_weight = $item['items_weight'];
          $items_ship_price = $item['items_ship_price'];
          $items_ship_price_two = $item['items_ship_price_two'];
        }
        else{
          $no_count = 1;
          if (preg_match('/^GIFT/', $my_hidden_items[$items_id]['model'])) {
            $no_count = 0;
          }
          $prid = $my_hidden_items[$items_id]['id'];
          $items_tax = go_get_tax_rate($my_hidden_items[$items_id]['tax_class_id']);
          $items_price = $my_hidden_items[$items_id]['price'];
          $items_basis_price = $my_hidden_items[$items_id]['items_basis_price'];
          $items_weight = $my_hidden_items[$items_id]['weight'];
          $items_ship_price = $my_hidden_items[$items_id]['items_ship_price'];
          $items_ship_price_two = $my_hidden_items[$items_id]['items_ship_price_two'];

        }


        if ($attendee_group_id['attendees_group_id'] != 0) {
          $attendee_group_price_query = go_db_query("select attendees_group_price, ROUND((1-(attendees_group_price/items_price))*100) AS rebate from " . DB_TBL_ITEMS_GROUPS . " where items_id = '" . $items_id . "' and attendees_group_id =  '" . $attendee_group_id['attendees_group_id'] . "'");
          if ($attendee_group_price = go_db_fetch_array($attendee_group_price_query)) {
            $items_price = $attendee_group_price['attendees_group_price'];
                        $rebate = $attendee_group_price['rebate'];
          }
        }

        if ($abc = go_get_items_special_price($prid)) {
                    if ($items_price > $abc) {
                        $items_price = $abc;
                    }
                }

        if (!isset($my_hidden_items)) {
          $items_price_option_query = go_db_query("select items_price_option from " . DB_TBL_ITEMS . " where items_id = '" . $items_id . "'");
          $items_price_option_now = go_db_fetch_array($items_price_option_query);
          $items_price_option_result = $items_price_option_now['items_price_option'];
        }
        else{
          $items_price_option_result = $my_hidden_items[$items_id]['items_price_option'];
        }
        if ($items_price_option_result == 4){
          $this->total_virtual += go_add_tax($items_price, $items_tax) * $qty *(($laenge*2/$immeter)+($breite*2/$inmeter_breite))* $no_count + ($qty * go_add_tax($items_basis_price, $items_tax));
          $this->weight_virtual += ($qty *(($laenge*2/$immeter)+($breite*2/$inmeter_breite))*  $items_weight) * $no_count;
          $this->total += go_add_tax($items_price, $items_tax) * $qty*(($laenge*2/$immeter)+($breite*2/$inmeter_breite)) + ($qty * go_add_tax($items_basis_price, $items_tax));
        } else {
          $this->total_virtual += go_add_tax($items_price, $items_tax) * $qty *($laenge/$immeter)*($breite/$inmeter_breite)* $no_count + ($qty * go_add_tax($items_basis_price, $items_tax));
          $this->weight_virtual += ($qty *($laenge/$immeter)*($breite/$inmeter_breite)*  $items_weight) * $no_count;
          $this->total += go_add_tax($items_price, $items_tax) * $qty*($laenge/$immeter)*($breite/$inmeter_breite) + ($qty * go_add_tax($items_basis_price, $items_tax));
        }


        $individueller_versandpreis = array();
        array_push ($individueller_versandpreis, $items_ship_price);
        sort ($individueller_versandpreis, SORT_NUMERIC);
        $individueller_versandpreis = array_shift ($individueller_versandpreis);
        $individueller_versandpreis_zwei = array();
        array_push ($individueller_versandpreis_zwei, $items_ship_price_two);
        sort ($individueller_versandpreis_zwei, SORT_NUMERIC);
        $individueller_versandpreis_zwei = array_shift ($individueller_versandpreis_zwei);
        $this->shiptotal += ($individueller_versandpreis);

        if ($qty > 1) {
          if ($items_ship_price_two > 0) {
            $this->shiptotal += ($individueller_versandpreis_zwei * ($qty - 1));
          }
          else {


          }
        }
        if ($items_price_option_result == 4){
          $this->weight += ($qty *(($laenge*2/$immeter)+($breite*2/$inmeter_breite))*  $items_weight);
        }
        else{
          $this->weight += ($qty *($laenge/$immeter)*($breite/$inmeter_breite)*  $items_weight);
        }
      }
      if (isset($this->contents[$items_id]['characteristics'])) {
        reset($this->contents[$items_id]['characteristics']);
        while (list($option, $value) = each($this->contents[$items_id]['characteristics'])) {
          if (KONFIGURATOR=='true'){

            if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
              include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19d.php');
            }
            else{

              if (is_array($value)){ $value_list = implode(",",$value);}//is array
              else { $value_list = (int)$value;}//is not array
              $characteristic_price_query = go_db_query("select price_prefix,options_conf_values_price from " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " where items_id = '" . (int)$prid . "' and options_conf_id = '" . (int)$option . "' and options_conf_values_id in (" . $value_list . ")");
              while ($characteristic_price = go_db_fetch_array($characteristic_price_query)) {
                if ($characteristic_price['price_prefix'] == '+') {
                  if ($items_price_option_result == 4){
                    $this->total += $qty *(($laenge*2/$immeter)+($breite*2/$inmeter_breite))* go_add_tax($characteristic_price['options_conf_values_price'], $items_tax);
                  }
                  else{
                    $this->total += $qty *($laenge/$immeter)*($breite/$inmeter_breite)* go_add_tax($characteristic_price['options_conf_values_price'], $items_tax);
                  }
                } else {

                  if ($items_price_option_result == 4){
                    $this->total -= $qty * (($laenge*2/$immeter)+($breite*2/$inmeter_breite))* go_add_tax($characteristic_price['options_conf_values_price'], $items_tax);
                  }
                  else{
                    $this->total -= $qty * ($laenge/$immeter)*($breite/$inmeter_breite)* go_add_tax($characteristic_price['options_conf_values_price'], $items_tax);
                  }
                } //else
              }//while
            } // not file_exists

          }// konfigurator
          else {
            if (!isset($my_hidden_items)) {
              $characteristic_price_query = go_db_query("select options_values_price, price_prefix from " . DB_TBL_ITEMS_CHARACTERISTICS . " where items_id = '" . (int)$prid . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
                            $characteristic_price = go_db_fetch_array($characteristic_price_query);
            }else{
              $characteristic_price['price_prefix'] = $my_hidden_items[$items_id]['characteristics_more'][$option]['price_prefix'];
                            $characteristic_price['options_values_price'] = $my_hidden_items[$items_id]['characteristics_more'][$option]['options_values_price'];
            }
                        $characteristic_price['options_values_price'] = empty($rebate) ? $characteristic_price['options_values_price'] : $characteristic_price['options_values_price'] * ((100-$rebate) / 100);
            if ($characteristic_price['price_prefix'] == '+') {
              if ($items_price_option_result == 4){
                $this->total += $qty *(($laenge*2/$immeter)+($breite*2/$inmeter_breite))* go_add_tax($characteristic_price['options_values_price'], $items_tax);
              }
              else{
                $this->total += $qty *($laenge/$immeter)*($breite/$inmeter_breite)* go_add_tax($characteristic_price['options_values_price'], $items_tax);
              }
            } else {

              if ($items_price_option_result == 4){
                $this->total -= $qty * (($laenge*2/$immeter)+($breite*2/$inmeter_breite))* go_add_tax($characteristic_price['options_values_price'], $items_tax);
              }
              else{
                $this->total -= $qty * ($laenge/$immeter)*($breite/$inmeter_breite)* go_add_tax($characteristic_price['options_values_price'], $items_tax);
              }
            }
          }

        }
      }
    }
    if (defined('CONSTITUENT_SHIPPING_INDVSHIP_MAX_PREIS') && $this->shiptotal >= CONSTITUENT_SHIPPING_INDVSHIP_MAX_PREIS){
      $this->shiptotal = CONSTITUENT_SHIPPING_INDVSHIP_MAX_PREIS;
    }
    $this->total = strtr($this->total, ",", ".");
    $this->total_before_discount = $this->total;
    $this->total -= $this->total * $this->discount()/100;
    if (isset($no_price_modules) && $no_price_modules == '1'){
      require(FOLDER_ABSOLUT_CATALOG . 'modules/no_price/class_this/classes_bigware_19_a.php');
    }
  }

  function discount() {
    $discount_value = 0;
    $discount_query = go_db_query("SELECT * FROM discount WHERE `limit` < " . $this->total_before_discount . " AND status = 1 ORDER BY `limit` DESC");
    if (mysqli_num_rows($discount_query)!=0) {
      $discount_value = mysqli_fetch_assoc($discount_query);
                        $discount_value = $discount_value['discount_procent'];
    }
    return $discount_value;
  }

  function characteristics_price($items_id) {
    $characteristics_price = 0;
    if (isset($this->contents[$items_id]['characteristics'])) {
      reset($this->contents[$items_id]['characteristics']);
      while (list($option, $value) = each($this->contents[$items_id]['characteristics'])) {
        if (KONFIGURATOR=='true'){

          if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
            include(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_19e.php');
          }
          else{
            $value_list="";
            if (is_array($value)){ $value_list = implode(",",$value);}//is array
            else { $value_list = (int)$value;}//is not array
            $characteristic_price_query = go_db_query("select price_prefix,options_conf_values_price from " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " where items_id = '" . (int)$items_id . "' and options_conf_id = '" . (int)$option . "' and options_conf_values_id in (" . $value_list . ")");
            while ($characteristic_price = go_db_fetch_array($characteristic_price_query)){
              if ($characteristic_price['price_prefix'] == '+') {
                $characteristics_price += $characteristic_price['options_conf_values_price'];
              } else {
                $characteristics_price -= $characteristic_price['options_conf_values_price'];
              }
            }//while
          } // not file_exists
        }//konfigurator
        else {
          $characteristic_price_query = go_db_query("select options_values_price, price_prefix from " . DB_TBL_ITEMS_CHARACTERISTICS . " where items_id = '" . (int)$items_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
          $characteristic_price = go_db_fetch_array($characteristic_price_query);
          if ($characteristic_price['price_prefix'] == '+') {
            $characteristics_price += $characteristic_price['options_values_price'];
          } else {
            $characteristics_price -= $characteristic_price['options_values_price'];
          }
        }//not konfigurator

      }
    }
    return $characteristics_price;
  }
  function get_items() {
    global $languages_id;
    if (!is_array($this->contents)) return false;
    $items_array = array();
    reset($this->contents);
        $items_count = 0;
    while (list($items_id, ) = each($this->contents)) {
            $rebate = 0;
      $items_query = go_db_query("select pd.*, p.* from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_id = '" . (int)$items_id . "' and pd.items_id = p.items_id and pd.language_id = '" . (int)$languages_id . "' $date_availability");
      if ($items = go_db_fetch_array($items_query)) {
        $prid = $items['items_id'];
        $items_price = $items['items_price'];
        $items_price_option = $items['items_price_option'];
        if ($items_price_option > 0) {
          $getImMeterQuery = go_db_query("select immeter, inmeter_breite from price_option where id=" . $items_price_option);
          $getImMeter = mysqli_result($getImMeterQuery,0,'immeter');
          $getInMeter_breite = mysqli_result($getImMeterQuery,0,'inmeter_breite');
        }
        else {
          $getImMeter = 1000;
          $getInMeter_breite = 1000;
        }
        global $attendee_id;
        $attendee_group_id_query = go_db_query("select attendees_group_id from " . DB_TBL_ATTENDEES . " where attendees_id = '". $attendee_id . "'");
        $attendee_group_id = go_db_fetch_array($attendee_group_id_query);
        $orders_attendees_price = go_db_query("select attendees_group_price, ROUND((1-(attendees_group_price/items_price))*100) AS rebate from " . DB_TBL_ITEMS_GROUPS . " where attendees_group_id = '". $attendee_group_id['attendees_group_id'] . "' and items_id = '" . $items['items_id'] . "'");
        if (($orders_attendees = go_db_fetch_array($orders_attendees_price)) && ($attendee_group_id['attendees_group_id'] != 0)) {
          $items_price = $orders_attendees['attendees_group_price'];
                    $rebate = $orders_attendees['rebate'];
        }

                if ($abc = go_get_items_special_price($items_id)) {
                    if ($items_price > $abc) {
                        $items_price = $abc;
                    }
                }

                $characteristics_price = empty($rebate) ? $this->characteristics_price($items_id) : $this->characteristics_price($items_id) * ((100-$rebate) / 100);
                $final_price = ($items_price + $characteristics_price);

                $final = $final_price;
                $final = (string)$final;
                global $language;
                if (isset($language) && $language == 'de') {
                  $final = preg_replace("','", ".", $final);
                }
                $final_price = $final;
        if ($this->contents[$items_id]['laenge']=="") {$this->contents[$items_id]['laenge']=1000;}
        if ($this->contents[$items_id]['breite']=="") {$this->contents[$items_id]['breite']=1000;}
        $items_array[$items_count] = array('id' => $items_id,
            'items_price_option' => $items_price_option,
            'quantity' => $this->contents[$items_id]['qty'],
            'laenge' => $this->contents[$items_id]['laenge'],
            'breite' => $this->contents[$items_id]['breite'],
            'items_basis_price' => $this->contents[$items_id]['item_basis_price'],
            'breite' => $this->contents[$items_id]['breite'],
            'immeter' => $getImMeter,
            'inmeter_breite' => $getInMeter_breite,
            'price_option_comment' => $this->contents[$items_id]['price_option_comment'],
            'final_price' => $final_price,
            'characteristics' => (isset($this->contents[$items_id]['characteristics']) ? $this->contents[$items_id]['characteristics'] : ''),
            'characteristics_more' => $this->contents[$items_id]['characteristics_more']);
        // all values from table "items" and "items_descriptions"
        if (is_array ($items)){
          foreach ($items as $key_items_and_description => $val_items_and_description){
            if ($key_items_and_description == 'items_name'){$items_array[$items_count]['name'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_model'){$items_array[$items_count]['model'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_picture'){$items_array[$items_count]['picture'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_tax_class_id'){$items_array[$items_count]['tax_class_id'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_weight'){$items_array[$items_count]['weight'] = $val_items_and_description;}
            else{$items_array[$items_count][$key_items_and_description] = $val_items_and_description;}
          }
        }
        $add_cart_variable_serialize = $this->contents[$items_id]['add_cart_variable_serialize'];
        $add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
        if (is_array ($add_cart_variable)){
          foreach ($add_cart_variable as $key => $val){
            $items_array[$items_count][$key] = $val;
          }
        }
        $items_array[$items_count]['add_cart_variable_serialize'] = $add_cart_variable_serialize;



      }
      $items_count++;
    }
    return $items_array;
  }
  function get_items_basket_box($count_last_items) {
    global $languages_id;
    if (!is_array($this->contents)) return false;
    $items_array = array();
    reset($this->contents);
    $result1 = $this->contents;
    $result2 = array_reverse($result1, TRUE);
    reset($result2);
    $basket_count = 0;
    while (list($items_id, ) = each($result2)) {
            $rebate = 0;
      $items_query = go_db_query("select p.*, pd.* from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_id = '" . (int)$items_id . "' and pd.items_id = p.items_id and pd.language_id = '" . (int)$languages_id . "' $date_availability");
      if ($items = go_db_fetch_array($items_query)) {
        if ($basket_count >= $count_last_items) {break;}
        $prid = $items['items_id'];
        $items_price = $items['items_price'];
        $items_basis_price = $items['items_basis_price'];
        $items_price_option = $items['items_price_option'];
        if ($items_price_option > 0) {
          $getImMeterQuery = go_db_query("select immeter, inmeter_breite from price_option where id=" . $items_price_option);
          $getImMeter = mysqli_result($getImMeterQuery,0,'immeter');
          $getInMeter_breite = mysqli_result($getImMeterQuery,0,'inmeter_breite');
        }
        else {
          $getImMeter = 1000;
          $getInMeter_breite = 1000;
        }
        global $attendee_id;
        $attendee_group_id_query = go_db_query("select attendees_group_id from " . DB_TBL_ATTENDEES . " where attendees_id = '". $attendee_id . "'");
        $attendee_group_id = go_db_fetch_array($attendee_group_id_query);
        $orders_attendees_price = go_db_query("select attendees_group_price, ROUND((1-(attendees_group_price/items_price))*100) AS rebate from " . DB_TBL_ITEMS_GROUPS . " where attendees_group_id = '". $attendee_group_id['attendees_group_id'] . "' and items_id = '" . $items['items_id'] . "'");
        if (($orders_attendees = go_db_fetch_array($orders_attendees_price)) && ($attendee_group_id['attendees_group_id'] != 0)) {
          $items_price = $orders_attendees['attendees_group_price'];
                    $rebate = $orders_attendees['rebate'];
        }

        if ($abc = go_get_items_special_price($items_id)) {
                    if ($items_price > $abc) {
                        $items_price = $abc;
                    }
                }

                $characteristics_price = empty($rebate) ? $this->characteristics_price($items_id) : $this->characteristics_price($items_id) * ((100-$rebate) / 100);
                $final_price = ($items_price + $characteristics_price);

                $final = $final_price;
                $final = (string)$final;
                global $language;
                if (isset($language) && $language == 'de') {
                  $final = preg_replace("','", ".", $final);
                }
                $final_price = $final;
        ////////////////////////////////
        if ($this->contents[$items_id]['laenge']=="") {$this->contents[$items_id]['laenge']=1000;}
        if ($this->contents[$items_id]['breite']=="") {$this->contents[$items_id]['breite']=1000;}
        $items_array[$basket_count] = array('id' => $items_id,
            'items_price_option' => $items_price_option,
            'items_basis_price' => $items_basis_price,
            'quantity' => $this->contents[$items_id]['qty'],
            'laenge' => $this->contents[$items_id]['laenge'],
            'breite' => $this->contents[$items_id]['breite'],
            'immeter' => $getImMeter,
            'inmeter_breite' => $getInMeter_breite,
            'price_option_comment' => $this->contents[$items_id]['price_option_comment'],
            'final_price' => $final_price,
            'characteristics' => (isset($this->contents[$items_id]['characteristics']) ? $this->contents[$items_id]['characteristics'] : ''),
            'characteristics_more' => $this->contents[$items_id]['characteristics_more']);
        // all values from table "items" and "items_descriptions"
        if (is_array ($items)){
          foreach ($items as $key_items_and_description => $val_items_and_description){
            if ($key_items_and_description == 'items_name'){$items_array[$basket_count]['name'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_model'){$items_array[$basket_count]['model'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_picture'){$items_array[$basket_count]['picture'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_tax_class_id'){$items_array[$basket_count]['tax_class_id'] = $val_items_and_description;}
            elseif ($key_items_and_description == 'items_weight'){$items_array[$basket_count]['weight'] = $val_items_and_description;}
            else{$items_array[$basket_count][$key_items_and_description] = $val_items_and_description;}
          }
        }

        $add_cart_variable_serialize = $this->contents[$items_id]['add_cart_variable_serialize'];
        $add_cart_variable = array_in_one_hidden_decode($add_cart_variable_serialize);
        if (is_array ($add_cart_variable)){
          foreach ($add_cart_variable as $key => $val){
            $items_array[$basket_count][$key] = $val;
          }
        }
        $items_array[$basket_count]['add_cart_variable_serialize'] = $add_cart_variable_serialize;

        $basket_count++;
      }
    }
    return $items_array;
  }
  function show_total() {
    $this->calculate();
    return $this->total;
  }
  function get_shiptotal() {
    $this->calculate();
    return $this->shiptotal;
  }
  function show_weight() {
    $this->calculate();
    return $this->weight;
  }
  function show_total_virtual() {
    $this->calculate();
    return $this->total_virtual;
  }
  function show_weight_virtual() {
    $this->calculate();
    return $this->weight_virtual;
  }
  function generate_cart_id($length = 5) {
    return go_create_random_value($length, 'digits');
  }
  function get_content_type() {
    $this->content_type = false;

    if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
      reset($this->contents);
      while (list($items_id, ) = each($this->contents)) {
        if (isset($this->contents[$items_id][DOWNLOAD_iss_download])) {
          switch ($this->content_type) {
            case 'physical':
              $this->content_type = 'mixed';
              return $this->content_type;
              break;
            default:
              $this->content_type = 'virtual';
              break;
          }
        } elseif ($this->show_weight() == 0) {
          reset($this->contents);
          while (list($items_id, ) = each($this->contents)) {
            $virtual_check_query = go_db_query("select items_weight, items_model from " . DB_TBL_ITEMS . " where items_id = '" . $items_id . "'");
            $virtual_check = go_db_fetch_array($virtual_check_query);
            if (preg_match('/^GIFT/', $virtual_check['items_model'])) {
              switch ($this->content_type) {
                case 'physical':
                  $this->content_type = 'mixed';
                  return $this->content_type;
                  break;
                default:
                  $this->content_type = 'virtual';
                  break;
              }
            } else {
              switch ($this->content_type) {
                case 'virtual':
                  $this->content_type = 'mixed';
                  return $this->content_type;
                  break;
                default:
                  $this->content_type = 'physical';
                  break;
              }
              $the_coupon = 1;
            }
          }
        } else {
          switch ($this->content_type) {
            case 'virtual':
              $this->content_type = 'mixed';
              return $this->content_type;
              break;
            default:
              $this->content_type = 'physical';
              break;
          }
        }
      }
    } else {
      $this->content_type = 'physical';
    }
    return $this->content_type;
  }
  function unserialize($broken) {
    for(reset($broken);$kv=each($broken);) {
      $key=$kv['key'];
      if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
    }
  }
  function count_contents_virtual() {
    $total_items = 0;
    if (is_array($this->contents)) {
      reset($this->contents);
      while (list($items_id, ) = each($this->contents)) {
        $no_count = false;
        $gv_query = go_db_query("select items_model from " . DB_TBL_ITEMS . " where items_id = '" . $items_id . "'");
        $gv_result = go_db_fetch_array($gv_query);
        if (preg_match('/^GIFT/', $gv_result['items_model'])) {
          $no_count=true;
        }
        if (NO_COUNT_ZERO_WEIGHT == 1) {
          $gv_query = go_db_query("select items_weight from " . DB_TBL_ITEMS . " where items_id = '" . go_get_prid($items_id) . "'");
          $gv_result=go_db_fetch_array($gv_query);
          if ($gv_result['items_weight']<=MIN_WEIGHT) {
            $no_count=true;
          }
        }
        if (!$no_count) $total_items += $this->get_quantity($items_id);
      }
    }
    return $total_items;
  }
}
?>
