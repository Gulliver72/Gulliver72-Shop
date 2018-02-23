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

  class lands_name extends dbObject {
    protected $dbTable = "lands_name";
    protected $primaryKey = "";
    protected $dbFields = Array (
        'lands_id' => Array ('int'),
        'lands_name' => Array ('text'),
        'language' => Array ('text')
    );
//    protected $timestamps = Array ();
    protected $relations = Array (//'lands_id' => Array ("hasOne", "lands"),
                                  'lands_name' => Array ("hasMany", "lands", "language")
                                  );
  }
?>