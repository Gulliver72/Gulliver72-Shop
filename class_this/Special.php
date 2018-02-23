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
* $Id: Special.php 0001 2016-07-20 19:47:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
* ###################################################################################
*/

  class Special {

    public $isSpecial; // bool
    private $itemsId; // int
    private $groupId; // int
    private $id; // int
    private $status; // int
    private $startDate; // date
    private $endDate; // date
    private $dateAdded; // datetime
    private $lastModified; // datetime
    private $specialPrice; // double
    private $lastID = 0; // int
    private $newSpecial; // bool
    
    private $resource; // obj
    
    function __construct( $id = '' ) {
          
      if ( $id != '' ) {
        $this->id = $id;
        $this->isSpecial = ( bool )false;
        $this->newSpecial = ( bool )false;
        $this->init();
      } else {
        $this->setDefault();
      }
    }
    private function init() {
    
      $db = MysqliDb::getInstance();
    
      if ( $db->where('specials_id', $this->id)->has('specials') === true ) {
      
        $this->resource = specials::where('specials_id', $this->id)->ObjectBuilder()->getOne();
        $special = $this->resource->data;

        $this->isSpecial = ( bool )true;
        $this->id = $special['specials_id'];
        $this->status = $special['status'];
        $this->startDate = $special['start_date'];
        $this->endDate = $special['expires_date'];
        $this->dateAdded = $special['specials_date_added'];
        $this->lastModified = $special['specials_last_modified'];
        $this->specialPrice = $special['specials_new_items_price'];
      }
      $this->checkStatus();
    }
     
    private function setDefault() {

      $this->resource = specials::ObjectBuilder()->get();
      
      $this->id = '';
      $this->newSpecial = ( bool )true;
      $this->isSpecial = ( bool )false;
      $this->status = 0;
      $this->startDate = '0000-00-00';
      $this->endDate = '0000-00-00';
      $this->dateAdded = '';
      $this->lastModified = '';
      $this->specialPrice = '';
    }
    
    private function checkStatus() {
    
      $today = date( "Y-m-d" );
      $dateTimestamp1 = strtotime( $today );
      $dateTimestamp2 = strtotime( $this->startDate );
      $dateTimestamp3 = strtotime( $this->endDate );
      
      if ( $dateTimestamp1 < $dateTimestamp2 ) $this->isSpecial = ( bool )false;
      if ( $this->endDate != '0000-00-00' && $dateTimestamp1 > $dateTimestamp3 ) $this->isSpecial = ( bool )false;
      if ( !defined('_VALID_BIG_ADMIN') && $this->status != 1 ) $this->isSpecial = ( bool )false;
      
      if ( $this->isSpecial === false ) {
        $this->status = 0;
        $this->saveData();
      }
    }
    
    public function getId() {
    
      return $this->id;
    }
    
    public function setGroupId( $id ) {
    
      $this->groupId = $id;
    }
    
    public function getGroupId() {
    
      return $this->groupId;
    }
    
    public function setItemsId( $id ) {
    
      $this->itemsId = $id;
    }
    
    public function getItemsId() {
    
      return $this->itemsId;
    }
    
    public function setStatus( $status ) {
    
      $this->status = $status;
    }
    
    public function getStatus() {
    
      return $this->status;
    }
    
    public function setStartDate( $date ) {
    
      $this->startDate = $date;
      $this->checkStatus();
    }
    
    public function getStartDate() {
    
      return $this->startDate;
    }
    
    public function setEndDate( $date ) {
    
      $this->endDate = $date;
      $this->checkStatus();
    }
    
    public function getEndDate() {
    
      return $this->endDate;
    }
    
    public function setSpecialPrice( $price ) {
    
      $this->specialPrice = $price;
    }
    
    public function getSpecialPrice() {
    
      return $this->specialPrice;
    }
    
    public function getDateAdded() {
    
      return $this->dateAdded;
    }
    
    public function getLastModified() {
    
      return $this->lastModified;
    }
    
    public function save() {
    
      $this->saveData();
    }
    
    private function saveData() {
    
      $values = array('specials_id' => $this->id, 
                      'items_id' => $this->itemsId,
                      'specials_new_items_price' => str_replace(',', '.', $this->specialPrice),
                      'specials_date_added' => date('Y-m-d H:i:s'),
                      'specials_last_modified' => $this->lastModified,
                      'start_date' => $this->startDate,
                      'expires_date' => $this->endDate,
                      'status' => $this->status,
                      'attendees_group_id' => $this->groupId
                      );

      if ( $this->id == '' ) {
      
        $this->resource->isNew = true;
      }
      
      $res = $this->resource->save( $values );
      if ( $this->id == '' ) $this->id = $res;
            
      $this->init();
      
      return $res;
    }
  }
?>