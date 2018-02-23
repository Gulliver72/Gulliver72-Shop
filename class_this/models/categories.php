<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int categories_id
 * @property string categories_picture
 * @property int parent_id
 * @property int sort_order
 * @property datetime date_added
 * @property datetime last_modified
 * @property int delivery_time_id
 * @property string distri_katname
 * @property string distri_aufschlag
 * @property string distri_firstkat
 * @property string syn
 * @property int categories_status
 * @property string spezial
 * @property int folge
 * @property string default_gewicht
 */

  class categories extends dbObject {
    protected $dbTable = "categories";
    protected $primaryKey = "categories_id";
    protected $dbFields = Array (
        'categories_id' => Array ('int'),
        'categories_picture' => Array ('text'),
        'parent_id' => Array ('int'),
        'sort_order' => Array ('int'),
        'date_added' => Array ('datetime'),
        'last_modified' => Array ('datetime'),
        'delivery_time_id' => Array ('int'),
        'distri_katname' => Array ('text'),
        'distri_aufschlag' => Array ('text'),
        'distri_firstkat' => Array ('text'),
        'syn' => Array ('text'),
        'categories_status' => Array ('int'),
        'spezial' => Array ('text'),
        'folge' => Array ('int'),
        'default_gewicht' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $hidden = Array();
    protected $relations = Array (
        'categories_description' => Array ("hasMany", "categories_description", "categories_id")
    );
    protected function hasChildren($catId) {
      global $db;
      $count = $db->rawQuery("SELECT COUNT(*) AS total FROM categories WHERE parent_id = '" . $catId . "'");
      
      if ($count[0]['total'] > 0) {
        return true;
      } else {
        return false;
      }
    }
  }
?>