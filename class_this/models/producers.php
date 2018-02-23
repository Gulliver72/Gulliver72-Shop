<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int producers_id
 * @property string producers_name
 * @property string producers_picture
 * @property datetime date_added
 * @property datetime last_modified
 */

  class producers extends dbObject {
    protected $dbTable = "producers";
    protected $primaryKey = "producers_id";
    protected $dbFields = Array (
        'producers_id' => Array ('int'),
        'producers_name' => Array ('text'),
        'producers_picture' => Array ('text'),
        'date_added' => Array ('datetime'),
        'last_modified' => Array ('datetime'),
    );
//    protected $timestamps = Array (date_added, last_modified);
//    protected $relations = Array ();
  }
?>