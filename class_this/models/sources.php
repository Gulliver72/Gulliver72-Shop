<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int sources_id
 * @property string sources_name
 */

  class sources extends dbObject {
    protected $dbTable = "sources";
    protected $primaryKey = "sources_id";
    protected $dbFields = Array (
        'sources_id' => Array ('int'),
        'sources_name' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>