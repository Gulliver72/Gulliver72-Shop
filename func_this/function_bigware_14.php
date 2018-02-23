<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015
  
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015	Bigware LTD

  Copyrightvermerke duerfen nicht entfernt werden.
  ------------------------------------------------------------------------
  Dieses Programm ist freie Software. Sie koennen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation
  veroeffentlicht, weitergeben und/oder modifizieren, entweder gemaess Version 2 
  der Lizenz oder (nach Ihrer Option) jeder spaeteren Version.
  Die Veroeffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen
  von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die
  implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FUER EINEN
  BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
  
  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
  Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

  Infos:
  ------------------------------------------------------------------------
  Der Bigware Shop wurde vor vielen Jahren bereits aus dem bekannten Shopsystem osCommerce
  weiter- und neuentwickelt.
  Der Bigware Shop legt im hohen Masse Wert auf Bedienerfreundlichkeit, beinhaltet eine leichte
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine grosse 
  Community, die bei Problemen weiterhelfen kann.
  
  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.
 
  Hilfe erhalten Sie im Forum auf www.bigware.de 
  
  -----------------------------------------------------------------------
  
 ##################################################################################




*/
?>
<?php
  function go_update_whos_online() {
    global $attendee_id, $spider_flag, $user_agent;
    if (go_session_is_registered('attendee_id')) {
      $wo_attendee_id = $attendee_id;
      $attendee_query = go_db_query("select attendees_firstname, attendees_lastname from " . DB_TBL_ATTENDEES . " where attendees_id = '" . (int)$attendee_id . "'");
      $attendee = go_db_fetch_array($attendee_query);
      $wo_full_name = $attendee['attendees_firstname'] . ' ' . $attendee['attendees_lastname'];
    } else {
      $wo_attendee_id = '';
      $wo_full_name = (($spider_flag)? $user_agent : 'Gast');
    }
    $wo_session_id = go_session_id();
    $wo_ip_address = getenv('REMOTE_ADDR');
    $wo_last_page_url = getenv('REQUEST_URI');
    $current_time = time();
    $xx_mins_ago = ($current_time - WHOS_ONLINE_AGO); 
    go_db_query("delete from " . DB_TBL_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");
    $stored_attendee_query = go_db_query("select count(*) as count from " . DB_TBL_WHOS_ONLINE . " where session_id = '" . go_db_input($wo_session_id) . "'");
    $stored_attendee = go_db_fetch_array($stored_attendee_query);
    if ($stored_attendee['count'] > 0) {
      go_db_query("update " . DB_TBL_WHOS_ONLINE . " set attendee_id = '" . (int)$wo_attendee_id . "', full_name = '" . go_db_input($wo_full_name) . "', ip_address = '" . go_db_input($wo_ip_address) . "', time_last_click = '" . go_db_input($current_time) . "', last_page_url = '" . go_db_input($wo_last_page_url) . "' where session_id = '" . go_db_input($wo_session_id) . "'");
    } else {
      go_db_query("insert into " . DB_TBL_WHOS_ONLINE . " (attendee_id, full_name, session_id, ip_address, time_entry, time_last_click, last_page_url) values ('" . (int)$wo_attendee_id . "', '" . go_db_input($wo_full_name) . "', '" . go_db_input($wo_session_id) . "', '" . go_db_input($wo_ip_address) . "', '" . go_db_input($current_time) . "', '" . go_db_input($current_time) . "', '" . go_db_input($wo_last_page_url) . "')");
    }
  }
?>
