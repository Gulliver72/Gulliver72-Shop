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

function my_session_register(){
	$args = func_get_args();
	foreach ($args as $key){
		$_SESSION[$key]=$GLOBALS[$key];
	}
}

function my_session_is_registered($key){
	return isset($_SESSION[$key]);
}

function my_session_unregister($key){
	unset($_SESSION[$key]);
}

if (SHOP_SESSIONS == 'mysql') {
	if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
		$SESS_LIFE = 1440;
	}
	function _sess_open($save_path, $session_name) {
		return true;
	}
	function _sess_close() {
		return true;
	}
	function _sess_read($key) {
		$value_query = go_db_query("select value from " . DB_TBL_SESSIONS . " where sesskey = '" . go_db_input($key) . "' and expiry > '" . time() . "'");
		$value = go_db_fetch_array($value_query);
		if (isset($value['value'])) {
			return $value['value'];
		}
		return false;
	}
	function _sess_write($key, $val) {
		global $SESS_LIFE;
		$expiry = time() + $SESS_LIFE;
		$value = $val;
		$check_query = go_db_query("select count(*) as total from " . DB_TBL_SESSIONS . " where sesskey = '" . go_db_input($key) . "'");
		$check = go_db_fetch_array($check_query);
		if ($check['total'] > 0) {
			return go_db_query("update " . DB_TBL_SESSIONS . " set expiry = '" . go_db_input($expiry) . "', value = '" . go_db_input($value) . "' where sesskey = '" . go_db_input($key) . "'");
		} else {
			return go_db_query("insert into " . DB_TBL_SESSIONS . " values ('" . go_db_input($key) . "', '" . go_db_input($expiry) . "', '" . go_db_input($value) . "')");
		}
	}
	function _sess_destroy($key) {
		return go_db_query("delete from " . DB_TBL_SESSIONS . " where sesskey = '" . go_db_input($key) . "'");
	}
	function _sess_gc($maxlifetime) {
		go_db_query("delete from " . DB_TBL_SESSIONS . " where expiry < '" . time() . "'");
		return true;
	}
	session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
}

//-----------------------
function go_session_start() {
	global $_GET, $_POST, $_COOKIE;

	$sane_session_id = true;

	if (isset($_GET[go_session_name()])) {
		if (preg_match('/^[a-zA-Z0-9]+$/', $_GET[go_session_name()]) == false) {
			unset($_GET[go_session_name()]);
			$sane_session_id = false;
		}
	} elseif (isset($_POST[go_session_name()])) {
		if (preg_match('/^[a-zA-Z0-9]+$/', $_POST[go_session_name()]) == false) {
			unset($_POST[go_session_name()]);
			$sane_session_id = false;
		}
	} elseif (isset($_COOKIE[go_session_name()])) {
		if (preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE[go_session_name()]) == false) {
			$session_data = session_get_cookie_params();
			setcookie(go_session_name(), '', time()-42000, $session_data['path'], $session_data['domain']);
			$sane_session_id = false;
		}
	}

	if ($sane_session_id == false) {
		go_forward(go_href_link(NAME_OF_FILE_LOGIN, '', 'NONSSL', false));
	}

	$ret = session_start();

	if (!ini_get('register_globals')) {
		foreach ($_SESSION as $key => $val){
			$GLOBALS[$key] = &$_SESSION[$key];
		}
	}
	return $ret;
} 
//------------------- 
function go_session_register($variable) {
	if (!ini_get('register_globals')) {
		$_SESSION[$variable] = &$GLOBALS[$variable];
	}
	if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		return my_session_register($variable);
	} else {
		$_SESSION[$variable];
	}
}

function go_session_is_registered($variable) {
	if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		return my_session_is_registered($variable);
	} else {
		return $_SESSION[$variable];
	}
}

function go_session_unregister($variable) {
	if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		return my_session_unregister($variable);
	} else {
		return $_SESSION[$variable];
	}
}

function go_session_id($sessid = '') {
	if (!empty($sessid)) {
		return session_id($sessid);
	} else {
		return session_id();
	}
}
function go_session_name($name = '') {
	if (!empty($name)) {
		return session_name($name);
	} else {
		return session_name();
	}
}

function go_session_close() {
	if (function_exists('session_close')) {
		return session_close();
	}
}

function go_session_destroy() {
	return session_destroy();
}

function go_session_save_path($path = '') {
	if (!empty($path)) {
		return session_save_path($path);
	} else {
		return session_save_path();
	}
}

function go_session_recreate() {
	if (version_compare(PHP_VERSION, '4.1.0') >= 0) {
		$session_backup = $_SESSION;
		unset($_COOKIE[go_session_name()]);
		go_session_destroy();
		if (SHOP_SESSIONS == 'mysql') {
			session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
		}
		go_session_start();
		$_SESSION = $session_backup;
		unset($session_backup);
	}
}
?>
