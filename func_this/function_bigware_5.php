<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2016 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2016  Bigware LTD
  
  $Id: class Special.php 4200 2017-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

/*

  Funktionen in /class/this/Special.php verlagert
  
  in /load_this/load_this_bigware_10.php auskommentiert
  
*/  
   
  function go_set_featured_status($featured_id, $status) {
    return go_db_query("update " . DB_TBL_FEATURED . " set status = '" . $status . "', date_status_change = now() where featured_id = '" . $featured_id . "'");
  }  
  function go_expire_featured() {
    $featured_query = go_db_query("select featured_id from " . DB_TBL_FEATURED . " where status = '1' and now() >= expires_date and expires_date > 0");
    if (go_db_num_rows($featured_query)) {
      while ($featured = go_db_fetch_array($featured_query)) {
        go_set_featured_status($featured['featured_id'], '0');
      }
    }
  }
?>
