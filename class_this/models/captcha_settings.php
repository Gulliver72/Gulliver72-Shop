<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int id
 * @property string captcha_enable_8
 * @property string captcha_enable_10
 * @property string captcha_enable_11
 * @property string captcha_enable_37
 * @property string captcha_enable_49
 * @property string captcha_enable_53
 * @property string captcha_key
 * @property string possible
 * @property string cap_width
 * @property string cap_height
 * @property string characters
 * @property string background_color
 * @property string text_color
 * @property string noise_color
 * @property string captcha_font
 */

  class captcha_settings extends dbObject {
    protected $dbTable = "captcha_settings";
    protected $primaryKey = "id";
    protected $dbFields = Array (
        'id' => Array ('int'),
        'captcha_enable_8' => Array ('text'),
        'captcha_enable_10' => Array ('text'),
        'captcha_enable_11' => Array ('text'),
        'captcha_enable_37' => Array ('text'),
        'captcha_enable_49' => Array ('text'),
        'captcha_enable_53' => Array ('text'),
        'captcha_key' => Array ('text'),
        'possible' => Array ('text'),
        'cap_width' => Array ('text'),
        'cap_height' => Array ('text'),
        'characters' => Array ('text'),
        'background_color' => Array ('text'),
        'text_color' => Array ('text'),
        'noise_color' => Array ('text'),
        'captcha_font' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>