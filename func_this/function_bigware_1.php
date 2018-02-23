<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2016 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2016  Bigware LTD
  
  $Id: class Banners.php 4200 2017-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

/*

  Funktionen in /class/this/Banner.php verlagert
  
  auf Klasse umstellen
  
*/  

  function go_set_banner_status($banners_id, $status) {
    if ($status == '1') {
      return go_db_query("update " . DB_TBL_BANNERS . " set status = '1', date_status_change = now(), date_scheduled = NULL where banners_id = '" . (int)$banners_id . "'");
    } elseif ($status == '0') {
      return go_db_query("update " . DB_TBL_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . (int)$banners_id . "'");
    } else {
      return -1;
    }
  }  
  function go_activate_banners() {
    $banners_query = go_db_query("select banners_id, date_scheduled from " . DB_TBL_BANNERS . " where date_scheduled != ''");
    if (go_db_num_rows($banners_query)) {
      while ($banners = go_db_fetch_array($banners_query)) {
        if (date('Y-m-d H:i:s') >= $banners['date_scheduled']) {
          go_set_banner_status($banners['banners_id'], '1');
        }
      }
    }
  }  
  function go_expire_banners() {
    $banners_query = go_db_query("select b.banners_id, b.expires_date, b.expires_impressions, sum(bh.banners_shown) as banners_shown from " . DB_TBL_BANNERS . " b, " . DB_TBL_BANNERS_HISTORY . " bh where b.status = '1' and b.banners_id = bh.banners_id group by b.banners_id");
    if (go_db_num_rows($banners_query)) {
      while ($banners = go_db_fetch_array($banners_query)) {
        if (go_not_null($banners['expires_date'])) {
          if (date('Y-m-d H:i:s') >= $banners['expires_date']) {
            go_set_banner_status($banners['banners_id'], '0');
          }
        } elseif (go_not_null($banners['expires_impressions'])) {
          if ( ($banners['expires_impressions'] > 0) && ($banners['banners_shown'] >= $banners['expires_impressions']) ) {
            go_set_banner_status($banners['banners_id'], '0');
          }
        }
      }
    }
  }  
  function go_display_banner($action, $identifier) {
    if ($action == 'dynamic') {
      $banners_query = go_db_query("select count(*) as count from " . DB_TBL_BANNERS . " where status = '1' and banners_group = '" . $identifier . "'");
      $banners = go_db_fetch_array($banners_query);
      if ($banners['count'] > 0) {
        $banner = go_random_select("select banners_id, banners_title, banners_picture, banners_html_text from " . DB_TBL_BANNERS . " where status = '1' and banners_group = '" . $identifier . "'");
      } else {
        return '<b>TEP ERROR! (go_display_banner(' . $action . ', ' . $identifier . ') -> No banners with group \'' . $identifier . '\' found!</b>';
      }
    } elseif ($action == 'static') {
      if (is_array($identifier)) {
        $banner = $identifier;
      } else {
        $banner_query = go_db_query("select banners_id, banners_title, banners_picture, banners_html_text from " . DB_TBL_BANNERS . " where status = '1' and banners_id = '" . (int)$identifier . "'");
        if (go_db_num_rows($banner_query)) {
          $banner = go_db_fetch_array($banner_query);
        } else {
          return '<b>TEP ERROR! (go_display_banner(' . $action . ', ' . $identifier . ') -> Banner with ID \'' . $identifier . '\' not found, or status inactive</b>';
        }
      }
    } else {
      return '<b>TEP ERROR! (go_display_banner(' . $action . ', ' . $identifier . ') -> Unknown $action parameter value - it must be either \'dynamic\' or \'static\'</b>';
    }
    if (go_not_null($banner['banners_html_text'])) {
      $banner_string = $banner['banners_html_text'];
    } else {
      $banner_string = '<a href="' . go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_63], 'action=banner&goto=' . $banner['banners_id']) . '" target="_blank">' . go_picture(FOLDER_RELATIV_PICTURES . $banner['banners_picture'], $banner['banners_title']) . '</a>';
    }
    go_update_banner_display_count($banner['banners_id']);
    return $banner_string;
  }  
  function go_banner_exists($action, $identifier) {
    if ($action == 'dynamic') {
      return go_random_select("select banners_id, banners_title, banners_picture, banners_html_text from " . DB_TBL_BANNERS . " where status = '1' and banners_group = '" . $identifier . "'");
    } elseif ($action == 'static') {
      $banner_query = go_db_query("select banners_id, banners_title, banners_picture, banners_html_text from " . DB_TBL_BANNERS . " where status = '1' and banners_id = '" . (int)$identifier . "'");
      return go_db_fetch_array($banner_query);
    } else {
      return false;
    }
  }  
  function go_update_banner_display_count($banner_id) {
    $banner_check_query = go_db_query("select count(*) as count from " . DB_TBL_BANNERS_HISTORY . " where banners_id = '" . (int)$banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
    $banner_check = go_db_fetch_array($banner_check_query);
    if ($banner_check['count'] > 0) {
      go_db_query("update " . DB_TBL_BANNERS_HISTORY . " set banners_shown = banners_shown + 1 where banners_id = '" . (int)$banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
    } else {
      go_db_query("insert into " . DB_TBL_BANNERS_HISTORY . " (banners_id, banners_shown, banners_history_date) values ('" . (int)$banner_id . "', 1, now())");
    }
  }  
  function go_update_banner_click_count($banner_id) {
    go_db_query("update " . DB_TBL_BANNERS_HISTORY . " set banners_clicked = banners_clicked + 1 where banners_id = '" . (int)$banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
  }
?>