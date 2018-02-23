<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int attendees_basket_characteristics_id
 * @property int attendees_id
 * @property string items_id
 * @property int items_options_id
 * @property int items_options_value_id
 * @property int items_characteristics_id
 * @property double options_values_price
 * @property string price_prefix
 * @property string items_options_name
 * @property string items_options_values_name
 * @property string qty
 */

  class attendees_basket_characteristics extends dbObject {
    protected $dbTable = "attendees_basket_characteristics";
    protected $primaryKey = "attendees_basket_characteristics_id";
    protected $dbFields = Array (
        'attendees_basket_characteristics_id' => Array ('int'),
        'attendees_id' => Array ('int'),
        'items_id' => Array ('text'),
        'items_options_id' => Array ('int'),
        'items_options_value_id' => Array ('int'),
        'items_characteristics_id' => Array ('int'),
        'options_values_price' => Array ('double'),
        'price_prefix' => Array ('text'),
        'items_options_name' => Array ('text'),
        'items_options_values_name' => Array ('text'),
        'qty' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>