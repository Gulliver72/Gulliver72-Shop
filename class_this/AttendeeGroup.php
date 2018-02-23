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
* $Id: AttendeeGroup.php 0001 2017-06-04 18:09:11Z Gulliver72
* 
* Released under the GNU General Public License
* 
* ###################################################################################
*/


  class AttendeeGroup {

    public $isAttendeesGroup; // bool
    protected $attendeesGroupId; // int
    protected $attendeesGroupName; // string
    protected $standard; // int
    protected $status; // int
    protected $attendeesGroupDiscount; // double
    protected $colorBar; // string
    protected $groupPaymentUnallowed; // string
    protected $groupTax; // string
    
    private $resource; // obj
    
    function __construct($id = '') {
    
      if ( $id != '' ) {
        $this->attendeesGroupId = (int)$id;
        $this->init();
      } else {
        $this->setDefault();
      }
    }
    
    private function init() {
    
      global $db;
    
      if ( $db->where('attendees_group_id', $this->attendeesGroupId)->has('attendees_groups') === true ) {
      
        $this->resource = attendees_groups::where('attendees_group_id', $this->attendeesGroupId)->ObjectBuilder()->getOne();
        $checkAttendeesGroup = $checkAttendeesGroup->data; 
      
        $this->attendeesGroupName = isset($attendeesGroup['attendees_group_name']) ? $attendeesGroup['attendees_group_name'] : '';
        $this->standard = isset($attendeesGroup['standard']) ? $attendeesGroup['standard'] : '';
        $this->status = isset($attendeesGroup['status']) ? $attendeesGroup['status'] : '';
        $this->attendeesGroupDiscount = isset($attendeesGroup['attendees_group_discount']) ? $attendeesGroup['attendees_group_discount'] : '';
        $this->colorBar = isset($attendeesGroup['color_bar']) ? $attendeesGroup['color_bar'] : '';
        $this->groupPaymentUnallowed = isset($attendeesGroup['group_payment_unallowed']) ? $attendeesGroup['group_payment_unallowed'] : 0;
        $this->groupTax = isset($attendeesGroup['group_tax']) ? $attendeesGroup['group_tax'] : 0;
        
        $this->isAttendeesGroup = true;
      } else {
        $this->setDefault();
      }
    
    }
    
    private function setDefault() {
    
      $this->attendeesGroupId = 0;
      $this->attendeesGroupName = '';
      $this->standard = '';
      $this->status = '';
      $this->attendeesGroupDiscount = '';
      $this->colorBar = '';
      $this->groupPaymentUnallowed = 0;
      $this->groupTax = 0;
    
      $this->isAttendeesGroup = false;
    }
    
    public function getGroupByAttendee($attendeeId) {
    
      $attendee = new Attendee($attendeeId);
      
      $this->attendeesGroupId = $attendee->getAttendeesGroupId();
      $this->init();
      return $this->attendeesGroupId;
    }
    
    public function getId() {
    
      return $this->attendeesGroupId;
    }
    
    public function getGroupName() {
    
      return $this->attendeesGroupName;
    }
    
    public function setGroupName($name) {
    
      $this->attendeesGroupName = $name;
    }
    
    public function getStandard() {
    
      return $this->standard;
    }
    
    public function setStandard($standard) {
    
      $this->standard = $standard;
    }
    
    public function getStatus() {
    
      return $this->status;
    }
    
    public function setStatus($status) {
    
      $this->status = $status;
    }
    
    public function getAttendeesGroupDiscount() {
    
      return $this->attendeesGroupDiscount;
    }
    
    public function setAttendeesGroupDiscount($discount) {
    
      $this->attendeesGroupDiscount = $discount;
    }
    
    public function getColorBar() {
    
      return $this->colorBar;
    }
    
    public function setColorBar($color) {
    
      $this->colorBar = $color;
    }
    
    public function getGroupPaymentUnallowed() {
    
      return $this->groupPaymentUnallowed;
    }
    
    public function setGroupPaymentUnallowed($payment) {
    
      $this->groupPaymentUnallowed = $payment;
    }
    
    public function getGroupTax() {
    
      return $this->groupTax;
    }
    
    public function setGroupTax($tax) {
    
      $this->groupTax = $tax;
    }
    
    public function saveData() {
    
      $values = array('attendees_group_id' => $this->attendeesGroupId,
                      'attendees_group_name' => $this->attendeesGroupName,
                      'standard' => $this->standard,
                      'status' => $this->status,
                      'attendees_group_discount' => $this->attendeesGroupDiscount,
                      'color_bar' => $this->colorBar,
                      'group_payment_unallowed' => $this->groupPaymentUnallowed,
                      'group_tax' => $this->groupTax
                      );

      if ( $this->attendeesGroupId == '' ) {
      
        $this->resource->isNew = true;
      }
      
      $res = $this->resource->save( $values );
      if ( $this->attendeesGroupId == '' ) $this->attendeesGroupId = $res;
            
      $this->init();
      
      return $res;
    }
  }
?>
