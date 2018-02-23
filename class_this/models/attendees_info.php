<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int attendees_info_id
 * @property datetime attendees_info_date_of_last_logon
 * @property int attendees_info_number_of_logons
 * @property datetime attendees_info_date_member_created
 * @property datetime attendees_info_date_member_last_modified
 * @property int attendees_info_source_id
 * @property int global_item_informs
 */

  class attendees_info extends dbObject {
    protected $dbTable = "attendees_info";
    protected $primaryKey = "attendees_info_id";
    protected $dbFields = Array (
        'attendees_info_id' => Array ('int'),
        'attendees_info_date_of_last_logon' => Array ('datetime'),
        'attendees_info_number_of_logons' => Array ('int'),
        'attendees_info_date_member_created' => Array ('datetime'),
        'attendees_info_date_member_last_modified' => Array ('datetime'),
        'attendees_info_source_id' => Array ('int'),
        'global_item_informs' => Array ('int')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>