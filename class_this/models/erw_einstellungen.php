<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int id
 * @property string default_land
 * @property string default_newsletter
 * @property string zusatzfeld_anmelde_name
 * @property string keywords
 * @property int default_start
 * @property string linie
 * @property string allg_metas
 * @property string logo_pfad
 */

  class erw_einstellungen extends dbObject {
    protected $dbTable = "erw_einstellungen";
    protected $primaryKey = "id";
    protected $dbFields = Array (
        'id' => Array ('int'),
        'default_land' => Array ('text'),
        'default_newsletter' => Array ('text'),
        'zusatzfeld_anmelde_name' => Array ('text'),
        'keywords' => Array ('text'),
        'default_start' => Array ('int'),
        'linie' => Array ('text'),
        'allg_metas' => Array ('text'),
        'logo_pfad' => Array ('text')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>