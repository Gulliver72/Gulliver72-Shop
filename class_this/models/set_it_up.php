<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int set_it_up_id
 * @property string set_it_up_title
 * @property string set_it_up_key
 * @property string set_it_up_value
 * @property string set_it_up_description
 * @property int set_it_up_group_id
 * @property int sort_order
 * @property datetime last_modified
 * @property datetime date_added
 * @property string use_function
 * @property string set_function
 */

  class set_it_up extends dbObject {
    protected $dbTable = "set_it_up";
    protected $primaryKey = "set_it_up_id";
    protected $dbFields = Array (
        'set_it_up_id' => Array ('int'),
        'set_it_up_title' => Array ('text'),
        'set_it_up_key' => Array ('text'),
        'set_it_up_value' => Array ('text'),
        'set_it_up_description' => Array ('text'),
        'set_it_up_group_id' => Array ('int'),
        'sort_order' => Array ('int'),
        'last_modified' => Array ('datetime'),
        'date_added' => Array ('datetime'),
        'use_function' => Array ('text'),
        'set_function' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>