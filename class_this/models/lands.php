<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int lands_id
 * @property int items_id
 * @property int attendees_group_id
 * @property int quantity
 * @property double scaled_price
 */

  class lands extends dbObject {
    protected $dbTable = "lands";
    protected $primaryKey = "lands_id";
    protected $dbFields = Array (
        'lands_id' => Array ('int'),
        'lands_iso_code_2' => Array ('text'),
        'lands_iso_code_3' => Array ('text'),
        'form_of_address_id' => Array ('int'),
        'status' => Array ('int')
    );
//    protected $timestamps = Array ();
    protected $relations = Array ('lands_name' => Array ("hasMany", "lands_name", "lands_id"));
  }
?>