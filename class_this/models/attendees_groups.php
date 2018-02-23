<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int id
 * @property int items_id
 * @property int attendees_group_id
 * @property int quantity
 * @property double scaled_price
 */

  class attendees_groups extends dbObject {
    protected $dbTable = "attendees_groups";
    protected $primaryKey = "attendees_group_id";
    protected $dbFields = Array (
        'attendees_group_id' => Array ('int'),
        'attendees_group_name' => Array ('text'),
        'standard' => Array ('int'),
        'status' => Array ('int'),
        'attendees_group_discount' => Array ('double'),
        'color_bar' => Array ('text'),
        'group_payment_unallowed' => Array ('text'),
        'group_tax' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>