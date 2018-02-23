<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int languages_id
 * @property string name
 * @property string code
 * @property string picture
 * @property string directory
 * @property int status
 * @property int sort_order
 */

  class languages extends dbObject {
    protected $dbTable = "languages";
    protected $primaryKey = "languages_id";
    protected $dbFields = Array (
        'languages_id' => Array ('int'),
        'name' => Array ('text'),
        'code' => Array ('text'),
        'picture' => Array ('text'),
        'directory' => Array ('text'),
        'status' => Array ('int'),
        'sort_order' => Array ('int'),
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>