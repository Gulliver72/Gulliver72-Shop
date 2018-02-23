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
* $Id: ItemsDesc.php 0001 2016-07-20 19:47:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
*/

  class ItemsDesc extends Item {

    protected $items_id; // int
    protected $items_name; // string
    protected $items_name2; // string
    protected $language_id; // int
    protected $items_description; // string
    protected $items_url; // string
    protected $items_viewed; // int
    protected $bezeichnung1; // string
    protected $inhalt1; // string
    protected $bezeichnung2; // string
    protected $inhalt2; // string
    protected $bezeichnung3; // string
    protected $inhalt3; // string
    protected $bezeichnung4; // string
    protected $inhalt4; // string
    protected $bezeichnung5; // string
    protected $inhalt5; // string
    protected $bezeichnung6; // string
    protected $inhalt6; // string
    protected $extra_item_costumer_input_text_preceded; // string
    protected $items_description_in_cat_list; // string
    
    function __construct( $id = '') {
    
      if ( $id != '' ) {
        $this -> items_id = $id;
      } else {
        $this -> items_id = '';
      }
      $this -> init();
      $this -> setDesc();
    }
    
    protected function setDesc() {
    
      if ( $this->items_id != '' ) {
        $itemsDesc = items_description::where('language_id', $_SESSION['languages_id'])->where('items_id', $this->items_id)->ArrayBuilder()->getOne();
        
        $this->items_name = isset($itemsDesc['items_name']) ? $itemsDesc['items_name'] : ''; // string
        $this->items_name2 = isset($itemsDesc['items_name2']) ? $itemsDesc['items_name2'] : ''; // string
        $this->language_id = isset($itemsDesc['language_id']) ? $itemsDesc['language_id'] : ''; // int
        $this->items_description = isset($itemsDesc['items_description']) ? $itemsDesc['items_description'] : ''; // string
        $this->items_url = isset($itemsDesc['items_url']) ? $itemsDesc['items_url'] : ''; // string
        $this->items_viewed = isset($itemsDesc['items_viewed']) ? $itemsDesc['items_viewed'] : ''; // int
        $this->bezeichnung1 = isset($itemsDesc['bezeichnung1']) ? $itemsDesc['bezeichnung1'] : ''; // string
        $this->inhalt1 = isset($itemsDesc['inhalt1']) ? $itemsDesc['inhalt1'] : ''; // string
        $this->bezeichnung2 = isset($itemsDesc['bezeichnung2']) ? $itemsDesc['bezeichnung2'] : ''; // string
        $this->inhalt2 = isset($itemsDesc['inhalt2']) ? $itemsDesc['inhalt2'] : ''; // string
        $this->bezeichnung3 = isset($itemsDesc['bezeichnung3']) ? $itemsDesc['bezeichnung3'] : ''; // string
        $this->inhalt3 = isset($itemsDesc['inhalt3']) ? $itemsDesc['inhalt3'] : ''; // string
        $this->bezeichnung4 = isset($itemsDesc['bezeichnung4']) ? $itemsDesc['bezeichnung4'] : ''; // string
        $this->inhalt4 = isset($itemsDesc['inhalt4']) ? $itemsDesc['inhalt4'] : ''; // string
        $this->bezeichnung5 = isset($itemsDesc['bezeichnung5']) ? $itemsDesc['bezeichnung5'] : ''; // string
        $this->inhalt5 = isset($itemsDesc['inhalt5']) ? $itemsDesc['inhalt5'] : ''; // string
        $this->bezeichnung6 = isset($itemsDesc['bezeichnung6']) ? $itemsDesc['bezeichnung6'] : ''; // string
        $this->inhalt6 = isset($itemsDesc['inhalt6']) ? $itemsDesc['inhalt6'] : ''; // string
        $this->extra_item_costumer_input_text_preceded = isset($itemsDesc['extra_item_costumer_input_text_preceded']) ? $itemsDesc['extra_item_costumer_input_text_preceded'] : ''; // string
        $this->items_description_in_cat_list = isset($itemsDesc['items_description_in_cat_list']) ? $itemsDesc['items_description_in_cat_list'] : ''; // string
        
        $this->isItem = (bool)true;
      }
    }
    public function getItemsName() {
    
      return $this -> items_name;
    }
    public function setItemsName( $name ) {
    
      $this -> items_name = $name;
    }
    public function getItemsName2() {
    
      return $this -> items_name2;
    }
    public function setItemsName2( $name ) {
    
      $this -> items_name2 = $name;
    }
    public function getLanguageId() {
    
      return $this -> language_id;
    }
    public function setLanguageId( $id ) {
    
      $this -> language_id = $id;
    }
    public function getItemsDesc() {
    
      return $this -> items_description;
    }
    public function setItemsDesc( $desc ) {
    
      $this -> items_description = $desc;
    }
    public function getItemsUrl() {
    
      return $this -> items_url;
    }
    public function setItemsUrl( $url ) {
    
      $this -> items_url = $url;
    }
    public function getItemsViewed() {
    
      return $this -> items_viewed;
    }
    public function setItemsViewed( $count ) {
    
      $this -> items_viewed = $count;
    }
    public function getBezeichnung($which) {
    
      if ($which > 0 && $which < 7) {
        return $this -> bezeichnung . $which;
      } else {
        return '';
      }
    }
    public function setBezeichnung( $which, $data ) {
    
      if ($which > 0 && $which < 7) {
        $this -> bezeichnung . $which = $data;
      } else {
        return false;
      }
    }
    public function getInhalt($which) {
    
      if ($which > 0 && $which < 7) {
        return $this -> inhalt . $which;
      } else {
        return '';
      }
    }
    public function setInhalt( $which, $data ) {
    
      if ($which > 0 && $which < 7) {
        $this -> inhalt . $which = $data;
      } else {
        return false;
      }
    }
    public function getExtraItemCostumerInputTextPreceded() {
    
      return $this -> extra_item_costumer_input_text_preceded;
    }
    public function setExtraItemCostumerInputTextPreceded( $input ) {
    
      $this -> extra_item_costumer_input_text_preceded = $input;
    }
    public function getItemsDescriptionInCatList() {
    
      return $this -> items_description_in_cat_list;
    }
    public function setItemsDescriptionInCatList( $desc ) {
    
      $this -> items_description_in_cat_list = $desc;
    }
    /*
    public function save() {
    
      $this -> saveData();
    }
    private function saveData() {
    
      if ( $this -> newSpecial === true ) {
        $values = array( 'items_id => "' . $this -> itemsId . '",
                          specials_new_items_price => "' . str_replace(',', '.', $this -> specialPrice) . '",
                          specials_date_added => "' . date('Y-m-d H:i:s') . '",
                          specials_last_modified => "' . $this -> lastModified . '",
                          start_date => "' . $this -> startDate . '",
                          expires_date => "' . $this -> endDate . '",
                          status => "' . $this -> status . '",
                          attendees_group_id => "' . $this -> groupId . '"');
/*
        $bigcon -> insert(DB_TBL_SPECIALS)
                -> values($values);
        $this -> id = $bigcon -> lastInsertId();
*/
/*
        go_db_carry(DB_TBL_SPECIALS, $values);
        $this -> id = go_db_insert_id();
        $this -> init();
      } else {
        $values = array( 'items_id => "' . $this -> itemsId . '",
                          specials_new_items_price => "' . str_replace(',', '.', $this -> specialPrice) . '",
                          specials_date_added => "' . $this -> dateAdded . '",
                          specials_last_modified = "' . date('Y-m-d H:i:s') . '",
                          start_date => "' . $this -> startDate . '",
                          expires_date => "' . $this -> endDate . '",
                          status => "' . $this -> status . '",
                          attendees_group_id => "' . $this -> groupId . '"');
/*
        $bigcon -> update(DB_TBL_SPECIALS)
                -> values($values)
                -> where('specials_id = '" . $this -> id . "'');
*/
/*
        go_db_carry(DB_TBL_SPECIALS, $values, 'update', 'specials_id = "' . (int)$this -> id . '"');
        $this -> init();
      }
    }
    */
  }
?>