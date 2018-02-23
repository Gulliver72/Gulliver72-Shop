<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int specials_id
 * @property int items_id
 * @property string specials_new_items_price
 * @property datetime specials_date_added
 * @property datetime specials_last_modified
 * @property datetime start_date
 * @property datetime expires_date
 * @property datetime date_status_change
 * @property int status
 * @property int attendees_group_id
 */

  class specials extends dbObject {
    protected $dbTable = "specials";
    protected $primaryKey = "specials_id";
    protected $dbFields = Array (
        'specials_id' => Array ('int'),
        'items_id' => Array ('int'),
        'specials_new_items_price' => Array ('double'),
        'specials_date_added' => Array ('datetime'),
        'specials_last_modified' => Array ('datetime'),
        'start_date' => Array ('datetime'),
        'expires_date' => Array ('datetime'),
        'date_status_change' => Array ('datetime'),
        'status' => Array ('int'),
        'attendees_group_id' => Array ('int')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>