<?php
/**
 * To make IDEs autocomplete happy
 *
 * @property int association_id
 * @property int zone_land_id
 * @property int zone_id
 * @property int geo_zone_id
 * @property datetime last_modified
 * @property datetime date_added
 */

  class zones_to_geo_zones extends dbObject {
    protected $dbTable = "zones_to_geo_zones";
    protected $primaryKey = "association_id";
    protected $dbFields = Array (
        'association_id' => Array ('int'),
        'zone_land_id' => Array ('int'),
        'zone_id' => Array ('int')
        'geo_zone_id' => Array ('int')
        'last_modified' => Array ('datetime')
        'date_added' => Array ('datetime')
    );
//    protected $timestamps = Array ();
//    protected $relations = Array ();
  }
?>