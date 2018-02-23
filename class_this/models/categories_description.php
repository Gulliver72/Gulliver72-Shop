<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int categories_id
 * @property int language_id
 * @property string categories_name
 * @property string categories_desc
 */
  
  class categories_description extends dbObject {
    protected $dbTable = "categories_description";
    protected $primaryKey = "categories_id";
    protected $dbFields = Array (
        'categories_id' => Array ('int'),
        'language_id' => Array ('int'),
        'categories_name' => Array ('text'),
        'categories_desc' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $hidden = Array();
    protected $relations = Array (
        'categories' => Array ("hasOne", "categories", "categories_id")
    );
  }
?>