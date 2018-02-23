<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int attendees_basket_id
 * @property int attendees_id
 * @property int items_id
 * @property int attendees_basket_quantity 
 * @property int attendees_basket_laenge
 * @property int attendees_basket_breite
 * @property int attendees_basket_price_option
 * @property double final_price
 * @property double items_basis_price
 * @property date attendees_basket_date_added
 * @property string price_option_comment
 * @property string add_cart_variable_serialize
 *
 */

  class attendees extends dbObject {
    protected $dbTable = "attendees_basket";
    protected $primaryKey = "attendees_basket_id";
    protected $dbFields = Array (
        'attendees_basket_id' => Array ('int'),
        'attendees_id' => Array ('int'),
        'items_id' => Array ('int'),
        'attendees_basket_quantity' => Array ('int'),     
        'attendees_basket_laenge' => Array ('int'),        
        'attendees_basket_breite' => Array ('int'),      
        'attendees_basket_price_option' => Array ('int'),
        'final_price' => Array ('double'),
        'items_basis_price' => Array ('double'),
        'attendees_basket_date_added' => Array ('date'),
        'price_option_comment' => Array ('text'),
        'add_cart_variable_serialize' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $hidden = Array();
//    protected $relations = Array ();
  }
?>