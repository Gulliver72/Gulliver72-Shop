<?php
/**
* ###################################################################################
* Bigware Shop 3.0
* Release Datum: 30.05.2016
* 
* Bigware Shop
* http://www.bigware.de
* 
* Copyright (c) 2017 Bigware LTD
* $Id: DirectoryToAddress.php 0001 2017-06-04 18:09:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
*/


  class DirectoryToAddress {

    public $isNew; // bool
    protected $directory_to_address_id; // int
    protected $attendees_id; // int
    protected $entry_gender; // string
    protected $entry_firstname; // string
    protected $entry_lastname; // string
    protected $entry_company; // string
    protected $entry_cf; // string
    protected $entry_piva; // string
    protected $entry_street_address; // string
    protected $entry_street_address2; // string
    protected $entry_suburb; // string
    protected $entry_postcode; // string
    protected $entry_city; // string
    protected $entry_state; // string
    protected $entry_land_id; // int
    protected $entry_zone_id; // int
    
    function __construct($attendeeId, $addressId = '', $new = false) {
    
      $this -> attendees_id = (int)$attendeeId;
      $this -> directory_to_address_id = (int)$addressId;
      $this -> isNew = $new;
      if ( $this -> directory_to_address_id != '' && $this -> isNew === false ) {
        $this -> init();
      } else {
        $this -> setDefault();
      }
    }
    
    private function init() {
    
      /*
      $address = $bigcon -> select(DB_TBL_DIRECTORY_TO_ADDRESS)
                            -> which('directory_to_address_id, attendees_id, entry_gender, entry_firstname, entry_lastname, entry_company, entry_cf, entry_piva, entry_street_address, entry_street_address2, entry_suburb, entry_postcode, $entry_city, $entry_state, entry_land_id, entry_zone_id');
                            -> where('attendees_id = "' . $this -> attendees_id . '" and directory_to_address_id = "' . $this -> directory_to_address_id . '"');
                            -> result();
      */
      $checkAddress = go_db_query("select * from " . DB_TBL_DIRECTORY_TO_ADDRESS . " where attendees_id = '" . $this -> attendees_id . "' and directory_to_address_id = '" . $this -> directory_to_address_id . "'");
      $address = go_db_fetch_array($checkAddress);
      
      if ( isset($address['directory_to_address_id']) ) {
        $this -> directory_to_address_id = isset($address['directory_to_address_id']) ? $address['directory_to_address_id'] : '';
        $this -> entry_gender = isset($address['entry_gender']) ? $address['entry_gender'] : '';
        $this -> entry_company = isset($address['entry_company']) ? $address['entry_company'] : '';
        $this -> entry_cf = isset($address['entry_cf']) ? $address['entry_cf'] : '';
        $this -> entry_piva = isset($address['entry_piva']) ? $address['entry_piva'] : '';
        $this -> entry_firstname = isset($address['entry_firstname']) ? $address['entry_firstname'] : '';
        $this -> entry_lastname = isset($address['entry_lastname']) ? $address['entry_lastname'] : '';
        $this -> entry_street_address = isset($address['entry_street_address']) ? $address['entry_street_address'] : '';
        $this -> entry_street_address2 = isset($address['entry_street_address2']) ? $address['entry_street_address2'] : '';
        $this -> entry_suburb = isset($address['entry_suburb']) ? $address['entry_suburb'] : '';
        $this -> entry_postcode = isset($address['entry_postcode']) ? $address['entry_postcode'] : '';
        $this -> entry_city = isset($address['entry_city']) ? $address['entry_city'] : '';
        $this -> entry_state = isset($address['entry_state']) ? $address['entry_state'] : '';
        $this -> entry_land_id = isset($address['entry_land_id']) ? $address['entry_land_id'] : 0;
        $this -> entry_zone_id = isset($address['entry_zone_id']) ? $address['entry_zone_id'] : 0;
      
      } else {
        $this -> setDefault();
      }
    }
    
    private function setDefault() {
    
      $this -> directory_to_address_id = '';
      $this -> entry_gender = '';
      $this -> entry_company = '';
      $this -> entry_cf = '';
      $this -> entry_piva = '';
      $this -> entry_firstname = '';
      $this -> entry_lastname = '';
      $this -> entry_street_address = '';
      $this -> entry_street_address2 = '';
      $this -> entry_suburb = '';
      $this -> entry_postcode = '';
      $this -> entry_city = '';
      $this -> entry_state = '';
      $this -> entry_land_id = 0;
      $this -> entry_zone_id = 0;
    
    }
    
    public function getId() {
    
      return $this -> directory_to_address_id;
    }
    
    public function getGender() {
    
      return $this -> entry_gender;
    }
    
    public function setGender($gender) {
    
      $this -> entry_gender = $gender;
    }
    
    public function getFirstname() {
    
      return $this -> entry_firstname;
    }
    
    public function setFirstname($firstname) {
    
      $this -> entry_firstname = $firstname;
    }
    
    public function getLastname() {
    
      return $this -> entry_lastname;
    }
    
    public function setLastname($lastname) {
    
      $this -> entry_lastname = $lastname;
    }
    
    public function getCompany() {
    
      return $this -> entry_company;
    }
    
    public function setCompany($company) {
    
      $this -> entry_company = $company;
    }
    
    public function getCf() {
    
      return $this -> entry_cf;
    }
    
    public function setCf($cf) {
    
      $this -> entry_cf = $cf;
    }
    
    public function getPiva() {
    
      return $this -> entry_piva;
    }
    
    public function setPiva($piva) {
    
      $this -> entry_piva = $piva;
    }
    
    public function getAddress() {
    
      return $this -> entry_street_address;
    }
    
    public function setAddress($address) {
    
      $this -> entry_street_address = $address;
    }
    
    public function getAddress2() {
    
      return $this -> entry_street_address2;
    }
    
    public function setAddress2($address2) {
    
      $this -> entry_street_address2 = $address2;
    }
    
    public function getSuburb() {
    
      return $this -> entry_suburb;
    }
    
    public function setSuburb($suburb) {
    
      $this -> entry_suburb = $suburb;
    }
    
    public function getPostcode() {
    
      return $this -> entry_postcode;
    }
    
    public function setPostcode($postcode) {
    
      $this -> entry_postcode = $postcode;
    }
    
    public function getCity() {
    
      return $this -> entry_city;
    }
    
    public function setCity($city) {
    
      $this -> entry_city = $city;
    }
    
    public function getState() {
    
      return $this -> entry_state;
    }
    
    public function setState($state) {
    
      $this -> entry_state = $state;
    }
    
    public function getLandId() {
    
      return $this -> entry_land_id;
    }
    
    public function setLandId($id) {
    
      $this -> entry_land_id = $id;
    }
    
    public function getZoneId() {
    
      return $this -> entry_zone_id;
    }
    
    public function setZoneId($id) {
    
      $this -> entry_zone_id = $id;
    }
    
    public function saveData() {
    
      if ( $this -> directory_to_address_id == '' ) {
        $values = array('attendees_id' => $this -> attendees_id,
                        'entry_gender' => $this -> entry_gender,
                        'entry_firstname' => $this -> entry_firstname,
                        'entry_lastname' => $this -> entry_lastname,
                        'entry_company' => $this -> entry_company,
                        'entry_cf' => $this -> entry_cf,
                        'entry_piva' => $this -> entry_piva,
                        'entry_street_address' => $this -> entry_street_address,
                        'entry_street_address2' => $this -> entry_street_address2,
                        'entry_suburb' => $this -> entry_suburb,
                        'entry_postcode' => $this -> entry_postcode,
                        'entry_city' => $this -> entry_city,
                        'entry_state' => $this -> entry_state,
                        'entry_land_id' => $this -> entry_land_id,
                        'entry_zone_id' => $this -> entry_zone_id
                        );
        /*
        $bigcon -> insert(DB_TBL_DIRECTORY_TO_ADDRESS)
                -> values($values);
        $this -> attendees_id = $bigcon -> lastInsertId();
        */
        go_db_carry(DB_TBL_DIRECTORY_TO_ADDRESS, $values);
        $this -> directory_to_address_id = go_db_insert_id();
        $this -> init();
      } else {
        $values = array('attendees_id' => $this -> attendees_id,
                        'entry_gender' => $this -> entry_gender,
                        'entry_firstname' => $this -> entry_firstname,
                        'entry_lastname' => $this -> entry_lastname,
                        'entry_company' => $this -> entry_company,
                        'entry_cf' => $this -> entry_cf,
                        'entry_piva' => $this -> entry_piva,
                        'entry_street_address' => $this -> entry_street_address,
                        'entry_street_address2' => $this -> entry_street_address2,
                        'entry_suburb' => $this -> entry_suburb,
                        'entry_postcode' => $this -> entry_postcode,
                        'entry_city' => $this -> entry_city,
                        'entry_state' => $this -> entry_state,
                        'entry_land_id' => $this -> entry_land_id,
                        'entry_zone_id' => $this -> entry_zone_id
                        );
        /*
        $bigcon -> update(DB_TBL_DIRECTORY_TO_ADDRESS)
                -> values($values)
                -> where('attendees_id = "' . $this -> attendees_id . '" and directory_to_address_id = "' . $this -> directory_to_address_id . '"');
        */
        go_db_carry(DB_TBL_DIRECTORY_TO_ADDRESS, $values, 'update', "attendees_id = '" . $this -> attendees_id . "' and directory_to_address_id = '" . $this -> directory_to_address_id . "'");
        $this -> init();
      }
    }
  }
?>
