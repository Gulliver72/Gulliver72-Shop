<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int attendees_id
 * @property string attendees_gender
 * @property string attendees_firstname
 * @property string attendees_lastname
 * @property string attendees_dob
 * @property string attendees_email_address
 * @property int attendees_default_address_id
 * @property string attendees_telephone
 * @property string attendees_fax
 * @property string attendees_password
 * @property string attendees_newsletter
 * @property int attendees_invoice
 * @property string guest_flag
 * @property int attendees_group_id
 * @property int member_level
 * @property string password_code
 * @property int reset_time
 */

  class attendees extends dbObject {
    protected $dbTable = "attendees";
    protected $primaryKey = "attendees_id";
    protected $dbFields = Array (
        'attendees_id' => Array ('int'),
        'attendees_gender' => Array ('text'),
        'attendees_firstname' => Array ('text'),
        'attendees_lastname' => Array ('text'),
        'attendees_dob' => Array ('datetime'),
        'attendees_email_address' => Array ('text'),
        'attendees_default_address_id' => Array ('int'),
        'attendees_telephone' => Array ('text'),
        'attendees_fax' => Array ('text'),
        'attendees_password' => Array ('text'),
        'attendees_newsletter' => Array ('text'),
        'attendees_invoice' => Array ('int'),
        'guest_flag' => Array ('text'),
        'attendees_group_id' => Array ('int'),
        'member_level' => Array ('int'),
        'password_code' => Array ('text'),
        'reset_time' => Array ('int')
    );
//    protected $timestamps = Array ();
    protected $hidden = Array('attendees_password');
    protected $relations = Array ('attendees_groups' => Array ("hasOne", "attendees_groups", "attendees_group_id"),
                                  'attendees_basket' => Array ("hasOne", "attendees_basket", "")
    );
  }
?>