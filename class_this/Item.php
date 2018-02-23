<?php
/**
* ###################################################################################
* Bigware Shop 3.0
* Release Datum: 30.05.2016
* 
* Bigware Shop
* http://www.bigware.de
* 
* Copyright (c) 2018 Bigware LTD
* $Id: Item.php 0001 2016-07-20 19:47:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
*/

  class Item {

    public $isItem = false; // bool 
    protected $newItem = false; // bool
    
    protected $items_id; // int
    protected $items_quantity; // string
    protected $Featured_item; // int
    protected $items_model; // string
    protected $items_picture; // string
    protected $items_bpicture; // string
    protected $items_3picture; // string
    protected $items_4picture; // string
    protected $items_5picture; // string
    protected $items_price; // double
    protected $items_basis_price; // double
    protected $items_price_option; // string
    protected $items_price_uvp; // double
    protected $unit_price_option; // string
    protected $unit_price_factor; // double
    protected $delivery_time_id; // int
    protected $shipping_zone_1; // double
    protected $shipping_zone_2; // double
    protected $items_date_added; // datetime
    protected $items_last_modified; // datetime
    protected $items_date_available; // datetime
    protected $items_date_available_end; // datetime
    protected $items_weight; // int
    protected $items_status; // int
    protected $domainservice_status; // int
    protected $domainservice_zahlungsintervall; // int
    protected $items_tax_class_id; // int
    protected $producers_id; // int
    protected $items_ordered; // int
    protected $items_percentage; // int
    protected $syn; // string
    protected $neu; // int
    protected $herstellernummer; // string
    protected $items_ship_price; // double
    protected $items_ship_price_two; // double
    protected $items_sort; // int
    protected $items_bundle; // string
    protected $shipping_greatship; // double
    protected $extra_item_costumer_input; // int
    protected $item_costumer_input_width; // string
    protected $item_costumer_input_height; // string
    protected $iss_masseinheit; // int
    protected $iss_download; // int
    protected $upc_ean_isbn; // string
    protected $shipping_range_group_id; // int
    protected $fsk18; // int
    
    private $resource; // obj
    
    // zusätzliche Datenbankfelder
    protected $additionalFields = array(); // numeric array 
    
    function __construct( $id = '') {
    
      if ( $id != '' ) {
        $this -> items_id = $id;
        $this -> init();
      } else {
        $this -> items_id = '';
        $this -> setDefault();
      }
    }
    
    protected function init() {
    
      global $db;
    
      if ( $db->where('items_id', $this->items_id)->has('items') === true ) {
      
        $this->resource = items::where('items_id', $this->items_id)->ObjectBuilder()->getOne();
        $item = $this->resource->data;
     
        $this->isItem = true;
        
        $this->items_quantity = isset($item['items_quantity']) ? $item['items_quantity'] : '';
        $this->Featured_item = isset($item['Featured_item']) ? $item['Featured_item'] : '';
        $this->items_model = isset($item['items_model']) ? $item['items_model'] : '';
        $this->items_picture = isset($item['items_picture']) ? $item['items_picture'] : '';
        $this->items_bpicture = isset($item['items_bpicture']) ? $item['items_bpicture'] : '';
        $this->items_3picture = isset($item['items_3picture']) ? $item['items_3picture'] : '';
        $this->items_4picture = isset($item['items_4picture']) ? $item['items_4picture'] : '';
        $this->items_5picture = isset($item['items_5picture']) ? $item['items_5picture'] : '';
        $this->items_price = isset($item['items_price']) ? $item['items_price'] : '';
        $this->items_basis_price = isset($item['items_basis_price']) ? $item['items_basis_price'] : '';
        $this->items_price_option = isset($item['items_price_option']) ? $item['items_price_option'] : '';
        $this->items_price_uvp = isset($item['items_price_uvp']) ? $item['items_price_uvp'] : '';
        $this->unit_price_option = isset($item['unit_price_option']) ? $item['unit_price_option'] : '';
        $this->unit_price_factor = isset($item['unit_price_factor']) ? $item['unit_price_factor'] : '';
        $this->delivery_time_id = isset($item['delivery_time_id']) ? $item['delivery_time_id'] : '';
        $this->shipping_zone_1 = isset($item['shipping_zone_1']) ? $item['shipping_zone_1'] : '';
        $this->shipping_zone_2 = isset($item['shipping_zone_2']) ? $item['shipping_zone_2'] : '';
        $this->items_date_added = isset($item['items_date_added']) ? $item['items_date_added'] : '0000-00-00 00:00:00';
        $this->items_last_modified = isset($item['items_last_modified']) ? $item['items_last_modified'] : '0000-00-00 00:00:00';
        $this->items_date_available = isset($item['items_date_available']) ? $item['items_date_available'] : '';
        $this->items_date_available_end = isset($item['items_date_available_end']) ? $item['items_date_available_end'] : '';
        $this->items_weight = isset($item['items_weight']) ? $item['items_weight'] : '';
        $this->items_status = isset($item['items_status']) ? $item['items_status'] : '';
        $this->domainservice_status = isset($item['domainservice_status']) ? $item['domainservice_status'] : '';
        $this->domainservice_zahlungsintervall = isset($item['domainservice_zahlungsintervall']) ? $item['domainservice_zahlungsintervall'] : '';
        $this->items_tax_class_id = isset($item['items_tax_class_id']) ? $item['items_tax_class_id'] : '';
        $this->producers_id = isset($item['producers_id']) ? $item['producers_id'] : '';
        $this->items_ordered = isset($item['items_ordered']) ? $item['items_ordered'] : '';
        $this->items_percentage = isset($item['items_percentage']) ? $item['items_percentage'] : '';
        $this->syn = isset($item['syn']) ? $item['syn'] : '';
        $this->neu = isset($item['neu']) ? $item['neu'] : '';
        $this->herstellernummer = isset($item['herstellernummer']) ? $item['herstellernummer'] : '';
        $this->items_ship_price = isset($item['items_ship_price']) ? $item['items_ship_price'] : '';
        $this->items_ship_price_two = isset($item['items_ship_price_two']) ? $item['items_ship_price_two'] : '';
        $this->items_sort = isset($item['items_sort']) ? $item['items_sort'] : '';
        $this->items_bundle = isset($item['items_bundle']) ? $item['items_bundle'] : '';
        $this->shipping_greatship = isset($item['shipping_greatship']) ? $item['shipping_greatship'] : '';
        $this->extra_item_costumer_input = isset($item['extra_item_costumer_input']) ? $item['extra_item_costumer_input'] : '';
        $this->item_costumer_input_width = isset($item['item_costumer_input_width']) ? $item['item_costumer_input_width'] : '';
        $this->item_costumer_input_height = isset($item['item_costumer_input_height']) ? $item['item_costumer_input_height'] : '';
        $this->iss_masseinheit = isset($item['iss_masseinheit']) ? $item['iss_masseinheit'] : '';
        $this->iss_download = isset($item['iss_download']) ? $item['iss_download'] : '';
        $this->upc_ean_isbn = isset($item['upc_ean_isbn']) ? $item['upc_ean_isbn'] : '';
        $this->shipping_range_group_id = isset($item['shipping_range_group_id']) ? $item['shipping_range_group_id'] : '';
        $this->fsk18 = isset($item['fsk18']) ? $item['fsk18'] : 'false';
          
        if ( count($this->additionalFields) > 0 ) {
          for ($i = 0; $i < count($this->additionalFields); $i++) {
            $field = $this->additionalFields[$i];
            $this->$field = isset($item[$field]) ? $item[$field] : '';
          }
        }
      }
      $this -> checkStatus();
    }
    
    protected function setDefault() {
         
      $this->items_quantity = 0;
      $this->Featured_item = 0;
      $this->items_model = '';
      $this->items_picture = '';
      $this->items_bpicture = '';
      $this->items_3picture = '';
      $this->items_4picture = '';
      $this->items_5picture = '';
      $this->items_price = '';
      $this->items_basis_price = '';
      $this->items_price_option = '';
      $this->items_price_uvp = '';
      $this->unit_price_option = '';
      $this->unit_price_factor = '';
      $this->delivery_time_id = '';
      $this->shipping_zone_1 = '';
      $this->shipping_zone_2 = '';
      $this->items_date_added = date('Y-m-d H:i:s', time());
      $this->items_last_modified = '0000-00-00 00:00:00';
      $this->items_date_available = '';
      $this->items_date_available_end = '';
      $this->items_weight = '';
      $this->items_status = 0;
      $this->domainservice_status = '';
      $this->domainservice_zahlungsintervall = '';
      $this->items_tax_class_id = '';
      $this->producers_id = '';
      $this->items_ordered = 0;
      $this->items_percentage = '';
      $this->syn = '';
      $this->neu = '';
      $this->herstellernummer = '';
      $this->items_ship_price = '';
      $this->items_ship_price_two = '';
      $this->items_sort = '';
      $this->items_bundle = '';
      $this->shipping_greatship = '';
      $this->extra_item_costumer_input = '';
      $this->item_costumer_input_width = '';
      $this->item_costumer_input_height = '';
      $this->iss_masseinheit = '';
      $this->iss_download = '';
      $this->upc_ean_isbn = '';
      $this->shipping_range_group_id = '';
      $this->fsk18 = 'false';
          
      if ( count($this->additionalFields) > 0 ) {
        for ($i = 0; $i < count($this->additionalFields); $i++) {
          $field = $this->additionalFields[$i];
          $this->$field = '';
        }
      }
    }
    
    protected function checkStatus() {
    
      $today = date( "Y-m-d" );
      $dateTimestamp1 = strtotime( $today );
      $dateTimestamp2 = strtotime( $this -> items_date_available );
      $dateTimestamp3 = strtotime( $this -> items_date_available_end );
      
      if ( $dateTimestamp1 < $dateTimestamp2 ) $this -> isItem = ( bool )false;
      if ( $this -> items_date_available_end != '0000-00-00 00:00:00' && $dateTimestamp1 > $dateTimestamp3 ) $this -> isItem = ( bool )false;
      if ( !defined('_VALID_BIG_ADMIN') && $this -> items_status != 1 ) $this -> isItem = ( bool )false;
    }
    
    public function getItemsId() {
    
      return $this -> items_id;
    }
    
    public function setStatus( $status ) {
    
      $this -> items_status = $status;
    }
    
    public function getStatus() {
    
      return $this -> items_status;
    }
    public function setItemsQuantity( $qty ) {
    
      $this->items_quantity = $qty;
    }
    public function getItemsQuantity() {
    
      return $this->items_quantity;
    }
    public function setFeaturedItem( $featured ) {
    
      $this->Featured_item = $featured;
    }
    public function getFeaturedItem() {
    
      return $this->Featured_item;
    }
    public function setItemsModel( $model ) {
    
      $this->items_model = $model;
    }
    public function getItemsModel() {
    
      return $this->items_model;
    }
    public function setItemsPicture( $picture ) {
    
      $this->items_picture = $picture;
    }
    public function getItemsPicture() {
    
      return $this->items_picture;
    }
    public function setItemsBPicture( $picture ) {
    
      $this->items_bpicture = $picture;
    }
    public function getItemsBPicture() {
    
      return $this->items_bpicture;
    }
    
    public function setItemsPicture3( $picture ) {
    
      $this->items_3picture = $picture;
    }
    
    public function getItemsPicture3() {
    
      return $this->items_3picture;    
    }
    
    public function setItemsPicture4( $picture ) {
    
      $this->items_4picture = $picture;
    }
    
    public function getItemsPicture4() {
    
      return $this->items_4picture;    
    }
    
    public function setItemsPicture5( $picture ) {
    
      $this->items_5picture = $picture;
    }
    
    public function getItemsPicture5() {
    
      return $this->items_5picture;    
    }
    
    public function setItemsPrice( $price ) {
    
      $this->items_price = $price;
    }
    public function getItemsPrice() {
    
      return $this->items_price;
    }
    /*
        $this->items_basis_price = isset($item['items_basis_price']) ? $item['items_basis_price'] : '';
        $this->items_price_option = isset($item['items_price_option']) ? $item['items_price_option'] : '';
        $this->items_price_uvp = isset($item['items_price_uvp']) ? $item['items_price_uvp'] : '';
        $this->unit_price_option = isset($item['unit_price_option']) ? $item['unit_price_option'] : '';
        $this->unit_price_factor = isset($item['unit_price_factor']) ? $item['unit_price_factor'] : '';
        $this->delivery_time_id = isset($item['delivery_time_id']) ? $item['delivery_time_id'] : '';
        $this->shipping_zone_1 = isset($item['shipping_zone_1']) ? $item['shipping_zone_1'] : '';
        $this->shipping_zone_2 = isset($item['shipping_zone_2']) ? $item['shipping_zone_2'] : '';
        $this->items_date_added = isset($item['items_date_added']) ? $item['items_date_added'] : '';
        $this->items_last_modified = isset($item['items_last_modified']) ? $item['items_last_modified'] : '';
        $this->items_date_available = isset($item['items_date_available']) ? $item['items_date_available'] : '';
        $this->items_date_available_end = isset($item['items_date_available_end']) ? $item['items_date_available_end'] : '';
    */
    public function setItemsWeight( $weight ) {
    
      $this->items_weight = $weight;
    }
    public function getItemsWeight() {
    
      return $this->items_weight;
    }
    /*
        $this->domainservice_status = isset($item['domainservice_status']) ? $item['domainservice_status'] : '';
        $this->domainservice_zahlungsintervall = isset($item['domainservice_zahlungsintervall']) ? $item['domainservice_zahlungsintervall'] : '';
    */
    public function setItemsTaxClassId( $id ) {
    
      $this->items_tax_class_id = $id;
    }
    public function getItemsTaxClassId() {
    
      return $this->items_tax_class_id;
    }
    /*
        $this->producers_id = isset($item['producers_id']) ? $item['producers_id'] : '';
        $this->items_ordered = isset($item['items_ordered']) ? $item['items_ordered'] : '';
        $this->items_percentage = isset($item['items_percentage']) ? $item['items_percentage'] : '';
        $this->syn = isset($item['syn']) ? $item['syn'] : '';
        $this->neu = isset($item['neu']) ? $item['neu'] : '';
        $this->herstellernummer = isset($item['herstellernummer']) ? $item['herstellernummer'] : '';
        $this->items_ship_price = isset($item['items_ship_price']) ? $item['items_ship_price'] : '';
        $this->items_ship_price_two = isset($item['items_ship_price_two']) ? $item['items_ship_price_two'] : '';
        $this->items_sort = isset($item['items_sort']) ? $item['items_sort'] : '';
        $this->items_bundle = isset($item['items_bundle']) ? $item['items_bundle'] : '';
        $this->shipping_greatship = isset($item['shipping_greatship']) ? $item['shipping_greatship'] : '';
        $this->extra_item_costumer_input = isset($item['extra_item_costumer_input']) ? $item['extra_item_costumer_input'] : '';
        $this->item_costumer_input_width = isset($item['item_costumer_input_width']) ? $item['item_costumer_input_width'] : '';
        $this->item_costumer_input_height = isset($item['item_costumer_input_height']) ? $item['item_costumer_input_height'] : '';
        $this->iss_masseinheit = isset($item['iss_masseinheit']) ? $item['iss_masseinheit'] : '';
        $this->iss_download = isset($item['iss_download']) ? $item['iss_download'] : '';
        $this->upc_ean_isbn = isset($item['upc_ean_isbn']) ? $item['upc_ean_isbn'] : '';
        $this->shipping_range_group_id = isset($item['shipping_range_group_id']) ? $item['shipping_range_group_id'] : '';
    */
    public function setFsk18( $status ) {
    
        $this->fsk18 = $status;
    }
    public function getFsk18() {
    
      return $this->fsk18;
    }
    public function __get($name) {
    
      if ( in_array($name, $this->additionalFields) ) {
        return $this->$name;
      } else {
        SimpleLogger::crit('Call method get() with undefined field ' . $name . '!');
        
        return false;
      }
    }
    public function __set($name, $value) {
    
      if ( in_array($name, $this->additionalFields) ) {
        $this->$name = $value;
      } else {
        SimpleLogger::crit('Call method set() with undefined field ' . $name . '!');
        
        return false;
      }
    }
    
    public function saveData() {
    
      $values = array('items_id' => $this->items_id,
                      'items_quantity' => $this->items_quantity,
                      'Featured_item' => $this->Featured_item,
                      'items_model' => $this->items_model,
                      'items_picture' => $this->items_picture,
                      'items_bpicture' => $this->items_bpicture,
                      'items_3picture' => $this->items_3picture,
                      'items_4picture' => $this->items_4picture,
                      'items_5picture' => $this->items_5picture,
                      'items_price' => $this->items_price,
                      'items_basis_price' => $this->items_basis_price,
                      'items_price_option' => $this->items_price_option,
                      'items_price_uvp' => $this->items_price_uvp,
                      'unit_price_option' => $this->unit_price_option,
                      'unit_price_factor' => $this->unit_price_factor,
                      'delivery_time_id' => $this->delivery_time_id,
                      'shipping_zone_1' => $this->shipping_zone_1,
                      'shipping_zone_2' => $this->shipping_zone_2,
                      'items_date_added' => $this->items_date_added,
                      'items_last_modified' => $this->items_last_modified,
                      'items_date_available' => $this->items_date_available,
                      'items_date_available_end' => $this->items_date_available_end,
                      'items_weight' => $this->items_weight,
                      'items_status' => $this->items_status,
                      'domainservice_status' => $this->domainservice_status,
                      'domainservice_zahlungsintervall' => $this->domainservice_zahlungsintervall,
                      'items_tax_class_id' => $this->items_tax_class_id,
                      'producers_id' => $this->producers_id,
                      'items_ordered' => $this->items_ordered,
                      'items_percentage' => $this->items_percentage,
                      'syn' => $this->syn,
                      'neu' => $this->neu,
                      'herstellernummer' => $this->herstellernummer,
                      'items_ship_price' => $this->items_ship_price,
                      'items_ship_price_two' => $this->items_ship_price_two,
                      'items_sort' => $this->items_sort,
                      'items_bundle' => $this->items_bundle,
                      'shipping_greatship' => $this->shipping_greatship,
                      'extra_item_costumer_input' => $this->extra_item_costumer_input,
                      'item_costumer_input_width' => $this->item_costumer_input_width,
                      'item_costumer_input_height' => $this->item_costumer_input_height,
                      'iss_masseinheit' => $this->iss_masseinheit,
                      'iss_download' => $this->iss_download,
                      'upc_ean_isbn' => $this->upc_ean_isbn,
                      'shipping_range_group_id' => $this->shipping_range_group_id,
                      'fsk18' => $this->fsk18
                      );

      if ( $this->items_id == '' ) {
      
        $this->resource->isNew = true;
      }
      
      $res = $this->resource->save( $values );
      if ( $this->items_id == '' ) $this->items_id = $res;
                 
      $this->init();
      
      return $res;
    }
  }
?>