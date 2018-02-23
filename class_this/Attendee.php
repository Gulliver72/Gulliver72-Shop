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
* $Id: Attendee.php 0001 2017-06-04 18:09:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
*/


  class Attendee {

    public $isAttendee; // bool
    protected $attendees_id; // int
    protected $attendees_gender; // string
    protected $attendees_firstname; // string
    protected $attendees_lastname; // string
    protected $attendees_dob; // date
    protected $attendees_email_address; // email
    protected $attendees_default_address_id; // int
    protected $attendees_telephone; // string
    protected $attendees_fax; // string
    protected $attendees_newsletter; // int
    protected $attendees_invoice; // int
    protected $guest_flag; // int
    protected $attendees_group_id; // int
    protected $member_level; // int
    protected $password_code; // string
    protected $reset_time; // int
    
    private $resource; // obj
    
    function __construct($id = '') {
    
      if ( $id != '' ) {
        $this->attendees_id = (int)$id;
        $this->init();
      } else {
        $this->setDefault();
      }
    }
    
    private function init() {
    
      global $db;
    
      if ( $db->where('attendees_id', $this->attendees_id)->has('attendees') === true ) {
      
        $this->resource = attendees::where('attendees_id', $this->attendees_id)->ObjectBuilder()->getOne();
        $attendee = $this->resource->data;
      
        $this->attendees_gender = isset($attendee['attendees_gender']) ? $attendee['attendees_gender'] : '';
        $this->attendees_firstname = isset($attendee['attendees_firstname']) ? $attendee['attendees_firstname'] : '';
        $this->attendees_lastname = isset($attendee['attendees_lastname']) ? $attendee['attendees_lastname'] : '';
        $this->attendees_dob = isset($attendee['attendees_dob']) ? $attendee['attendees_dob'] : '';
        $this->attendees_email_address = isset($attendee['attendees_email_address']) ? $attendee['attendees_email_address'] : '';
        $this->attendees_default_address_id = isset($attendee['attendees_default_address_id']) ? $attendee['attendees_default_address_id'] : 0;
        $this->attendees_telephone = isset($attendee['attendees_telephone']) ? $attendee['attendees_telephone'] : '';
        $this->attendees_fax = isset($attendee['attendees_fax']) ? $attendee['attendees_fax'] : '';
        $this->attendees_newsletter = isset($attendee['attendees_newsletter']) ? $attendee['attendees_newsletter'] : '';
        $this->attendees_invoice = isset($attendee['attendees_invoice']) ? $attendee['attendees_invoice'] : 0;
        $this->guest_flag = isset($attendee['guest_flag']) ? $attendee['guest_flag'] : 0;
        $this->attendees_group_id = isset($attendee['attendees_group_id']) ? $attendee['attendees_group_id'] : 0;
        $this->member_level = isset($attendee['member_level']) ? $attendee['member_level'] : 0;
        $this->password_code = isset($attendee['password_code']) ? $attendee['password_code'] : '';
        $this->reset_time = isset($attendee['reset_time']) ? $attendee['reset_time'] : '';
        
        $this->isAttendee = true;
      } else {
        $this->setDefault();
      }
    
    }
    
    private function setDefault() {
    
      $this->attendees_id = '';
      $this->attendees_gender = '';
      $this->attendees_firstname = '';
      $this->attendees_lastname = '';
      $this->attendees_dob = '';
      $this->attendees_email_address = '';
      $this->attendees_default_address_id = 0;
      $this->attendees_telephone = '';
      $this->attendees_fax = '';
      $this->attendees_newsletter = '';
      $this->attendees_invoice = 0;
      $this->guest_flag = 0;
      $this->attendees_group_id = 0;
      $this->member_level = 0;
      $this->password_code = '';
      $this->reset_time = '';
    
      $this->isAttendee = false;
    }
    
    public function getIdByEmail($email) {
    
      global $db;
    
      if ( $db->where('attendees_email_address', $email)->has('attendees') === true ) {
      
        $attendee = attendees::where('attendees_email_address', $email)->ArrayBuilder()->getOne();
        
        $this->attendees_id = $attendee['attendees_id'];
        $this->init();
        return true;
      } else {
        return false;
      }
    }
    
    public function getId() {
    
      return $this->attendees_id;
    }
    
    public function getGender() {
    
      return $this->attendees_gender;
    }
    
    public function setGender($gender) {
    
      $this->attendees_gender = $gender;
    }
    
    public function getFirstname() {
    
      return $this->attendees_firstname;
    }
    
    public function setFirstname($firstname) {
    
      $this->attendees_firstname = $firstname;
    }
    
    public function getLastname() {
    
      return $this->attendees_lastname;
    }
    
    public function setLastname($lastname) {
    
      $this->attendees_lastname = $lastname;
    }
    
    public function getDob() {
    
      return $this->attendees_dob;
    }
    
    public function setDob($dob) {
    
      $this->attendees_dob = $dob;
    }
    
    public function getEmailAddress() {
    
      return $this->attendees_email_address;
    }
    
    public function setEmailAddress($email) {
    
      $this->attendees_email_address = $email;
    }
    
    public function getDefaultAddressId() {
    
      return $this->attendees_default_address_id;
    }
    
    public function setDefaultAddressId($id) {
    
      $this->attendees_default_address_id = $id;
    }
    
    public function getTelephone() {
    
      return $this->attendees_telephone;
    }
    
    public function setTelephone($telephone) {
    
      $this->attendees_telephone = $telephone;
    }
    
    public function getFax() {
    
      return $this->attendees_fax;
    }
    
    public function setFax($fax) {
    
      $this->attendees_fax = $fax;
    }
    
    public function getNewsletter() {
    
      return $this->attendees_newsletter;
    }
    
    public function setNewsletter($newsletter) {
    
      $this->attendees_newsletter = $newsletter;
    }
    
    public function getInvoice() {
    
      return $this->attendees_invoice;
    }
    
    public function setInvoice($invoice) {
    
      $this->attendees_invoice = $invoice;
    }
    
    public function getGuestFlag() {
    
      return $this->guest_flag;
    }
    
    public function setGuestFlag($flag) {
    
      $this->guest_flag = $flag;
    }
    
    public function getAttendeesGroupId() {
    
      return $this->attendees_group_id;
    }
    
    public function setAttendeesGroupId($id) {
    
      $this->attendees_group_id = $id;
    }
    
    public function getMemberLevel() {
    
      return $this->member_level;
    }
    
    public function setMemberLevel($level) {
    
      $this->member_level = $level;
    }
    
    public function getPasswordCode() {
    
      return $this->password_code;
    }
    
    public function setPasswordCode($code) {
    
      $this->password_code = $code;
    }
    
    public function getResetTime() {
    
      return $this->reset_time;
    }
    
    public function setResetTime($timestamp) {
    
      $this->reset_time = $timestamp;
    }
    
    public function reset() {
    
      $this->init();
    }

    public function saveData() {
    
      $values = array('attendees_id' => $this->attendees_id,
                      'attendees_gender' => $this->attendees_gender,
                      'attendees_firstname' => $this->attendees_firstname,
                      'attendees_lastname' => $this->attendees_lastname,
                      'attendees_dob' => $this->attendees_dob,
                      'attendees_email_address' => $this->attendees_email_address,
                      'attendees_default_address_id' => $this->attendees_default_address_id,
                      'attendees_telephone' => $this->attendees_telephone,
                      'attendees_fax' => $this->attendees_fax,
                      'attendees_invoice' => $this->attendees_invoice,
                      'attendees_newsletter' => $this->attendees_newsletter,
                      'attendees_group_id' => $this->attendees_group_id,
                      'guest_flag' => $this->guest_flag,
                      'member_level' => $this->member_level,
                      'password_code' => $this->password_code,
                      'reset_time' => $this->reset_time
                      );

      if ( $this->attendees_id == '' ) {
      
        $this->resource->isNew = true;
      }
      
      $res = $this->resource->save( $values );
      if ( $this->attendees_id == '' ) $this->attendees_id = $res;
                 
      $this->init();
      
      return $res;
    }
  }
?>
