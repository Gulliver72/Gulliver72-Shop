<?php
/* -----------------------------------------------------------------------------------------
   $Id: get_ip_address.php 2017-02-10 20:35:25Z Gulliver72 $   

   Bigware Shopsoftware
   http://www.bigware.de

   Copyright (c) 2017 Bigware
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003 nextcommerce (xtc_get_ip_address.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2015 modified Shopsoftware (xtc_get_ip_address.inc.php,v 1.3 2003/08/13); www.modified-shop.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  function get_ip_address() {
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] != '') {
        $tmp_ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
      } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
        $tmp_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '') {
        $tmp_ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $tmp_ip = $_SERVER['REMOTE_ADDR'];
      }
      $ip_array = explode(',', $tmp_ip);
      $ip = trim($ip_array[0]);
    } else {
      if (getenv('HTTP_X_CLUSTER_CLIENT_IP') != '') {
        $tmp_ip = getenv('HTTP_X_CLUSTER_CLIENT_IP');
      } elseif (getenv('HTTP_X_FORWARDED_FOR') != '') {
        $tmp_ip = getenv('HTTP_X_FORWARDED_FOR');
      } elseif (getenv('HTTP_CLIENT_IP') != '') {
        $tmp_ip = getenv('HTTP_CLIENT_IP');
      } else {
        $tmp_ip = getenv('REMOTE_ADDR');
      }
      $ip_array = explode(',', $tmp_ip);
      $ip = trim($ip_array[0]);
    }

    return $ip;
  }
?>