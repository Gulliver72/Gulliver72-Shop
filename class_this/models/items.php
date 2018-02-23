<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int items_id
 * @property string items_quantity
 * @property string attendees_firstname
 * @property string attendees_lastname
 * @property string attendees_dob
 * @property string attendees_email_address
 * @property int attendees_default_address_id
 * @property string attendees_telephone
 * @property string attendees_fax
 * @property string attendees_password
 * @property string attendees_newsletter
 * @property int attendees_invoice
 * @property string guest_flag
 * @property int attendees_group_id
 * @property int member_level
 */

  class items extends dbObject {
    protected $dbTable = "items";
    protected $primaryKey = "items_id";
    protected $dbFields = Array (
        'items_id' => Array ('int'),
        'items_quantity' => Array ('text'),
        'Featured_item' => Array ('int'),
        'items_model' => Array ('text'),
        'items_picture' => Array ('text'),
        'items_bpicture' => Array ('text'),
        'items_3picture' => Array ('text'),
        'items_4picture' => Array ('text'),
        'items_5picture' => Array ('text'),
        'items_price' => Array ('double'),
        'items_basis_price' => Array ('double'),
        'items_price_option' => Array ('text'),
        'items_price_uvp' => Array ('double'),
        'unit_price_option' => Array ('text'),
        'unit_price_factor' => Array ('double'),
        'delivery_time_id' => Array ('int'),
        'shipping_zone_1' => Array ('double'),
        'shipping_zone_2' => Array ('double'),
        'items_date_added' => Array ('datetime'),
        'items_last_modified' => Array ('datetime'),
        'items_date_available' => Array ('datetime'),
        'items_date_available_end' => Array ('datetime'),
        'items_weight' => Array ('int'),
        'items_status' => Array ('int'),
        'domainservice_status' => Array ('int'),
        'domainservice_zahlungsintervall' => Array ('int'),
        'items_tax_class_id' => Array ('int'),
        'producers_id' => Array ('int'),
        'items_ordered' => Array ('int'),
        'items_percentage' => Array ('int'),
        'syn' => Array ('text'),
        'neu' => Array ('int'),
        'herstellernummer' => Array ('text'),
        'items_ship_price' => Array ('double'),
        'items_ship_price_two' => Array ('double'),
        'items_sort' => Array ('int'),
        'items_bundle' => Array ('text'),
        'shipping_greatship' => Array ('double'),
        'extra_item_costumer_input' => Array ('int'),
        'item_costumer_input_width' => Array ('text'),
        'item_costumer_input_height' => Array ('text'),
        'iss_masseinheit' => Array ('int'),
        'iss_download' => Array ('int'),
        'upc_ean_isbn' => Array ('text'),
        'shipping_range_group_id' => Array ('int'),
        'fsk18' => Array ('int')
    );
//    protected $timestamps = Array ();
    protected $relations = Array (
        'items_description' => Array ("hasMany", "items_description", "items_id"),
        'items_to_categories' => Array ("hasMany", "items_to_categories", "items_id")
    );
  }
?>