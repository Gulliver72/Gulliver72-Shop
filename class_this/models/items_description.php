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

  class items_description extends dbObject {
    protected $dbTable = "items_description";
    protected $primaryKey = "items_id";
    protected $dbFields = Array (
        'items_id' => Array ('int'),
        'language_id' => Array ('int'),
        'items_name' => Array ('text'),
        'items_name2' => Array ('text'),
        'items_description' => Array ('text'),
        'items_url' => Array ('text'),
        'items_viewed' => Array ('int'),
        'bezeichnung1' => Array ('text'),
        'inhalt1' => Array ('text'),
        'bezeichnung2' => Array ('text'),
        'inhalt2' => Array ('text'),
        'bezeichnung3' => Array ('text'),
        'inhalt3' => Array ('text'),
        'bezeichnung4' => Array ('text'),
        'inhalt4' => Array ('text'),
        'bezeichnung5' => Array ('text'),
        'inhalt5' => Array ('text'),
        'bezeichnung6' => Array ('text'),
        'inhalt6' => Array ('text'),
        'extra_item_costumer_input_text_preceded' => Array ('text'),
        'items_description_in_cat_list' => Array ('text')
    );
//    protected $timestamps = Array ();
    protected $relations = Array (
        'items' => Array ("hasOne", "items", "items_id")
    );
  }
?>