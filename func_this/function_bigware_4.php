<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015

  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2012	Bigware LTD

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
// Konstanten für Array-Typ definieren Gulliver72 
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 'MYSQLI_ASSOC');
if (!defined('MYSQLI_NUM')) define('MYSQLI_NUM', 'MYSQLI_NUM');

if (function_exists('mysqli_set_charset') === false) {
	function mysqli_set_charset($link_identifier = null, $charset) { // Parameter getauscht Gulliver72
		if ($link_identifier == null) {
			return mysqli_query('SET CHARACTER SET "'.$charset.'"');
		} else {
			return mysqli_query($link_identifier, 'SET CHARACTER SET "'.$charset.'"');
		}
	}
}
function go_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
	global $$link;
	if (USE_PCONNECT == 'true') {
		$$link = mysqli_pconnect($server, $username, $password);
	} else {
		$$link = mysqli_connect($server, $username, $password);
	}
	if ($$link) {
		if (defined('DB_CHARSET')) mysqli_set_charset($$link, DB_CHARSET); // geprüfte Konstante in Hochkommata gesetzt und Parameter getauscht Gulliver72
		mysqli_select_db($$link, $database);// projekt-interner Funktionsname Gulliver72 
	}
	return $$link;
}
function go_db_select_db($link = 'db_link', $database) {// Parameter ergänzt Gulliver72 
  global $$link; // globale Variable hinzugefügt Gulliver72 
  return mysqli_select_db($$link, $database);
}
function go_db_close($link = 'db_link') {
	global $$link;
	return mysqli_close($$link);
}
function go_db_error($query, $errno, $error) {
	die('<font color="#000000"><b>' . $errno . ' - ' . $error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></b></font>');
}
function go_db_query($query, $link = 'db_link') {
	global $$link, $logger;
  if(!$$link) {
    go_db_connect();
  }
	if (defined('SHOP_DB_TRANSACTIONS') && (SHOP_DB_TRANSACTIONS == 'true')) {
		if (!is_object($logger)) $logger = new logger;
		$logger->write($query, 'QUERY');
	}
	$result = mysqli_query($$link, $query) or go_db_error($query, mysqli_errno($$link), mysqli_error($$link));
	if (defined('SHOP_DB_TRANSACTIONS') && (SHOP_DB_TRANSACTIONS == 'true')) {
		if (mysqli_error($$link)) $logger->write(mysqli_error($$link), 'ERROR');// mysqli_error() benötigt Datenbankverbindungsobjekt Gulliver72 
	}
	return $result;
}
function go_db_carry($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
	reset($data);
	if ($action == 'insert') {
		$query = 'insert into ' . $table . ' (';
		while (list($columns, ) = each($data)) {
			$query .= $columns . ', ';
		}
		$query = substr($query, 0, -2) . ') values (';
		reset($data);
		while (list(, $value) = each($data)) {
			switch ((string)$value) {
				case 'now()':
					$query .= 'now(), ';
				break;
				case 'null':
					$query .= 'null, ';
				break;
				default:
					$query .= '\'' . go_db_input($value) . '\', ';
				break;
			}
		}
		$query = substr($query, 0, -2) . ')';
	} elseif ($action == 'update') {
		$query = 'update ' . $table . ' set ';
		while (list($columns, $value) = each($data)) {
			switch ((string)$value) {
				case 'now()':
				$query .= $columns . ' = now(), ';
				break;
				case 'null':
				$query .= $columns .= ' = null, ';
				break;
				default:
				$query .= $columns . ' = \'' . go_db_input($value) . '\', ';
				break;
			}
		}
		$query = substr($query, 0, -2) . ' where ' . $parameters;
	}
	return go_db_query($query);
}
function go_db_table_exists($table, $link = 'db_link') {// projekt-interner Funktionsname Gulliver72 
	global $$link, $logger;
	$query = "SELECT 1 FROM `$table` LIMIT 0";
	if (defined('SHOP_DB_TRANSACTIONS') && (SHOP_DB_TRANSACTIONS == 'true')) {
		if (!is_object($logger)) $logger = new logger;
		$logger->write($query, 'QUERY');
	}
	$exists = @mysqli_query($$link, $query); // Fehler werden nicht ausgegeben; // Parameter getauscht Gulliver72
	if (isset($exists)) {
    return true;// Prüfung Variable gefixt Gulliver72 
	} else {// else Schleife hinzugefügt Gulliver72 
    return false;
  }
}
// Funktion um Array-Typen ergänzt Gulliver72 
function go_db_fetch_array($db_query, $result_type = 'MYSQLI_ASSOC') {
  if ($result_type == 'MYSQLI_ASSOC') {
    // liefert ein assoziatives Array zurück 
    $res = mysqli_fetch_array($db_query, MYSQLI_ASSOC);
  } else {
    // liefert ein numerisches Array zurück 
    $res = mysqli_fetch_array($db_query, MYSQLI_NUM);
  }
  // Demaskierung der Inhalte 
  if (is_array($res)) {
    reset($res);
    while (list($key, $value) = each($res)) {
      $res[$key] = stripcslashes($value);
    }
  }
  return is_array($res) ? $res : array();
}
function go_db_num_rows($db_query) {
	return mysqli_num_rows($db_query);
}
function go_db_numrows($db_query) {
  return mysqli_num_rows($db_query);
}
function go_db_data_seek($db_query, $row_number) {
	return mysqli_data_seek($db_query, $row_number);
}
function go_db_insert_id() {
  global $db_link;
	return mysqli_insert_id($db_link);
}
function go_db_free_result($db_query) {
	return mysqli_free_result($db_query);
}
function go_db_fetch_field($db_query) { // Funktion hinzugefügt, da beide (fetch_field und fetch_fields) existieren Gulliver72 
	return mysqli_fetch_field($db_query);
}
function go_db_fetch_fields($db_query) {
	return mysqli_fetch_fields($db_query); // auf fetch_fields korrigiert Gulliver72
}
function go_db_field_name($db_query, $count) {
	return mysqli_field_name($db_query, $count);
}
function go_db_mysqli_num_fields($db_query) {
	return mysqli_num_fields($db_query);
}
function go_db_affected_rows($link) {
	return mysqli_affected_rows($link);
}
function go_db_fetch_object($db_query) {
	return mysqli_fetch_object($db_query);
}
function go_db_fetch_row($db_query) {
	return mysqli_fetch_row($db_query);
}
function go_db_real_escape_string($db_query) {
  global $db_link; // globale Variable hinzugefügt Gulliver72 
	return mysqli_real_escape_string($db_link, $db_query); // Funktion benötigt Datenbankverbindungsobjekt Gulliver72 
}

if (!function_exists('mysqli_result')) {

  function mysqli_result($res, $row = 0, $col = 0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
      mysqli_data_seek($res, $row);
      $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
      if (isset($resrow[$col])) {
        return $resrow[$col];
      }
    }
    return false;
  }

}

function go_db_result($db_query, $row = 0, $col = 0) {
	return mysqli_result($db_query, $row, $col);
}
// Funktion für verschiedene Charsets erweitert 
function go_db_output($string, $flag = "ENT_COMPAT", $double = true) { // Parameter erweitert Gulliver72 
	return isohtmlspecialchars($string, $flag, strtoupper(CHARSET), $double); // Parameter erweitert Gulliver72 
}
// Funktion zum Demaskieren hinzugefügt Gulliver72 
function go_db_prepare_output($data) {
  return stripcslashes($data);
}
// Funktion wandelt alle geeigneten Zeichen in entsprechende HTML-Codes Gulliver72 
function go_db_htmlentities($data) {
  if (defined('DB_CHARSET') && DB_CHARSET == 'utf8') {
    return htmlentities($data, ENT_QUOTES, CHARSET, false);
  } else {
    return go_db_output($data, ENT_QUOTES);
  }
}
// Funktion zum Konvertieren aller benannten HTML-Zeichen Gulliver72 
function go_db_html_entity_decode($data) {
  return html_entity_decode($data, ENT_QUOTES, CHARSET);
}
function go_db_input($string) {
	return addslashes($string);
}
function go_db_producing_input($string) {
  global $db_link;
	if (is_string($string)) {
		return trim(mysqli_real_escape_string($db_link, stripslashes($string)));
	} elseif (is_array($string)) {
		reset($string);
		while (list($key, $value) = each($string)) {
			$string[$key] = go_db_producing_input($value);
		}
		return $string;
	} else {
		return $string;
	}
}
?>