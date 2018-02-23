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
class splitPageResults {
	var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name;
  function __construct($query, $max_rows, $count_key = '*', $page_holder = 'page') {
    $this->splitPageResults($query, $max_rows, $count_key = '*', $page_holder = 'page');
  }
	function splitPageResults($query, $max_rows, $count_key = '*', $page_holder = 'page') {
		global $_GET, $_POST;
		$this->sql_query = $query;
		$this->page_name = $page_holder;
		if (isset($_GET[$page_holder])) {
			$page = $_GET[$page_holder];
		} elseif (isset($_POST[$page_holder])) {
			$page = $_POST[$page_holder];
		} else {
			$page = '';
		}
		if (empty($page) || !is_numeric($page)) $page = 1;
		$this->current_page_number = $page;
		$this->number_of_rows_per_page = $max_rows;
		$pos_to = strlen($this->sql_query);
		$pos_from = stripos($this->sql_query, ' from', 0);
		$pos_group_by = stripos($this->sql_query, ' group by', $pos_from);
		if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;
		$pos_having = stripos($this->sql_query, ' having', $pos_from);
		if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;
		$pos_order_by = stripos($this->sql_query, ' order by', $pos_from);
		if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;
		if ($count_key != '*' && (stripos($this->sql_query, 'distinct') || stripos($this->sql_query, 'group by'))) {
			$count_string = 'distinct ' . go_db_input($count_key);
		} else {
			$count_string = go_db_input($count_key);
		}
		$count_query_sql = "select count(" . $count_string . ") as total " . substr($this->sql_query, $pos_from, ($pos_to - $pos_from));
		$count_query = go_db_query($count_query_sql);
		$count = go_db_fetch_array($count_query);
		$this->number_of_rows = $count['total'];
		$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);
		if ($this->current_page_number > $this->number_of_pages) {
			$this->current_page_number = $this->number_of_pages;
		}
		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		$this->sql_query .= " limit " . max($offset, 0) . ", " . $this->number_of_rows_per_page;

	}

	function display_links($max_page_links, $parameters = '') {
		global $PHP_SELF, $request_type;
		$display_links_string = '';
		$class = 'class="pageResults"';
		if (go_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&'; 
		if ($this->current_page_number > 1) $display_links_string .= '<a href="' . go_href_link(basename($PHP_SELF), $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;'; 
		$cur_window_num = intval($this->current_page_number / $max_page_links);
		if ($this->current_page_number % $max_page_links) $cur_window_num++;
		$max_window_num = intval($this->number_of_pages / $max_page_links); 
		if ($this->number_of_pages % $max_page_links) $max_window_num++; 
		if ($cur_window_num > 1) $display_links_string .= '<a href="' . go_href_link(basename($PHP_SELF), $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>'; 
		for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
			if ($jump_to_page == $this->current_page_number) {
				$display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
			} else {
				$display_links_string .= '&nbsp;<a href="' . go_href_link(basename($PHP_SELF), $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' "><u>' . $jump_to_page . '</u></a>&nbsp;';
			}
		} 
		if ($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . go_href_link(basename($PHP_SELF), $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;'; 
		if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . go_href_link(basename($PHP_SELF), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;';
		return $display_links_string;
	} 
	function display_count($text_output) {
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);
		if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;
		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		if ($to_num == 0) {
			$from_num = 0;
		} else {
			$from_num++;
		}
		return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
	}
}
?>
