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

  class currencies extends dbObject {
    protected $dbTable = "currencies";
    protected $primaryKey = "currencies_id";
    protected $dbFields = Array (
        'currencies_id' => Array ('int'),
        'title' => Array ('text'),
        'code' => Array ('text'),
        'status' => Array ('int'),
        'symbol_left' => Array ('text'),
        'symbol_right' => Array ('text'),
        'decimal_point' => Array ('text'),
        'thousands_point' => Array ('text'),
        'decimal_places' => Array ('text'),
        'roundings' => Array ('text'),
        'value' => Array ('double'),
        'last_updated' => Array ('datetime'),
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>