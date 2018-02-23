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

  class price_scale extends dbObject {
    protected $dbTable = "price_scale";
    protected $primaryKey = "id";
    protected $dbFields = Array (
        'id' => Array ('int'),
        'items_id' => Array ('int'),
        'attendees_group_id' => Array ('int'),
        'quantity' => Array ('int'),
        'scaled_price' => Array ('double')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>