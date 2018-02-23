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

  class items_to_categories extends dbObject {
    protected $dbTable = "items_to_categories";
    protected $primaryKey = "";
    protected $dbFields = Array (
        'items_id' => Array ('int'),
        'categories_id' => Array ('int'),
    );
//    protected $timestamps = Array ();
    protected $relations = Array (
        'items' => Array ("hasOne", "items", "items_id"),
        'categories' => Array ("hasOne", "categories", "categories_id")
    );
    protected function countItems($catId) {
      global $db;
      $count = $db->rawQuery("SELECT COUNT(*) AS total FROM items_to_categories WHERE categories_id = '" . $catId . "'");
      
      return $count[0]['total'];
    }
  }
?>