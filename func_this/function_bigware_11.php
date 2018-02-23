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
$date_availability = 'AND (to_days(items_date_available_end) IS NULL OR (to_days(items_date_available_end) IS NOT NULL AND to_days(now()) <= to_days(items_date_available_end)))';

function go_set_specials_status($specials_id, $status) {
	return go_db_query("update " . DB_TBL_SPECIALS . " set status = '" . $status . "', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
}  
function go_expire_specials() {
	$specials_query = go_db_query("select specials_id from " . DB_TBL_SPECIALS . " where status = '1' and now() >= expires_date and expires_date > 0");
	if (go_db_num_rows($specials_query)) {
		while ($specials = go_db_fetch_array($specials_query)) {
			go_set_specials_status($specials['specials_id'], '0');
		}
	}
}


if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
	require(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/func_this/function_bigware_11.php');
} else {	
	function go_display_konfigurator($items_id,$array_selected_konfigurator_values,$items_price_konfigurator){
		global $languages_id,$currencies;

		$parent_values_array = array();
		$selected_characteristic = array();

		$item_info_query = go_db_query("select p.items_tax_class_id from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_status = '1' and p.items_id = '" . $items_id . "' and pd.items_id = p.items_id and pd.language_id = '" . (int)$languages_id . "' $date_availability ");
		$item_info = go_db_fetch_array($item_info_query);

		if (sizeof($array_selected_konfigurator_values)==0) {
			$parent_values_array[] = "( patrib.options_conf_parent_id='0' and patrib.options_conf_values_parent_id='0')";
			$array_selected_konfigurator_values = array();
		}
		else { 
			$parent_values_array[] = " (patrib.options_conf_parent_id='0' and patrib.options_conf_values_parent_id='0')";
			$parent_keys = array_keys($array_selected_konfigurator_values);
			for ($a=0; $a<sizeof($parent_keys); $a++){
				if (($array_selected_konfigurator_values[$parent_keys[$a]]!=0)||(sizeof($array_selected_konfigurator_values[$parent_keys[$a]])!=0)){
					$value = $array_selected_konfigurator_values[$parent_keys[$a]];
					if (is_array($array_selected_konfigurator_values[$parent_keys[$a]])){
						$value = implode(", ",$array_selected_konfigurator_values[$parent_keys[$a]]);
					}
					$value_list = "(".$value.")";
					if ($value_list!="()") $parent_values_array[] = "  (patrib.options_conf_parent_id='".$parent_keys[$a]."' and patrib.options_conf_values_parent_id in ".$value_list." )";
				}
			}//for a 
		}
		$parent_values = implode(" or ",$parent_values_array);


		$parent_values_default = go_find_default($items_id, $languages_id, $parent_values, $array_selected_konfigurator_values); 
		$parent_values_array = array_merge($parent_values_array, $parent_values_default);


		$parent_values = implode(" or ",$parent_values_array); 
		$mehroptionen = "<form action='".go_href_link($GLOBALS[CONFIG_NAME_FILE][main_bigware_39], go_get_all_get_parameter(array('action')) ) ."' method='post' name=\"konfigurator\">";  
		$items_options_name_query = go_db_query("select distinct popt.items_options_conf_id, popt.items_options_conf_name,patrib.options_conf_parent_id,patrib.options_conf_values_parent_id from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib where patrib.items_id='" . $items_id . "' and patrib.options_conf_id = popt.items_options_conf_id and popt.language_id = '" . (int)$languages_id . "' and (".$parent_values.") order by popt.items_options_conf_name");
		$mehroptionen .= '<table border="0" cellspacing="2" cellpadding="0">';
		$i = 0; $selected_defaults=array();
		while ($items_options_name = go_db_fetch_array($items_options_name_query)) {
			$i++;
			$mehroptionen .= "<tr><td class=\"main\" style=\"vertical-align:top\"><-items_options_name$i-></td><td class=\"main\" width=\"250px\"><-items_options_select$i-></td></tr>"; 
		} 
		$mehroptionen .= "<tr><td>&nbsp;</td></tr>";
		$mehroptionen .= "<tr><td colspan=2 align=\"right\" style='font-size:12pt; font-weight:600'><-items_price_konfigurator-></td></tr>";
		$mehroptionen .= '</table>';

		$i = 0; 
		$items_options_name_query = go_db_query("select distinct popt.items_options_conf_id, popt.items_options_conf_name,patrib.options_conf_parent_id,patrib.options_conf_values_parent_id from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib where patrib.items_id='" . $items_id . "' and patrib.options_conf_id = popt.items_options_conf_id and popt.language_id = '" . (int)$languages_id . "' and (".$parent_values.") order by popt.items_options_conf_name");
		while ($items_options_name = go_db_fetch_array($items_options_name_query)) {
			$items_options_array = array();  
			$items_options_query = go_db_query("select distinct  pov.items_options_conf_values_id, pov.items_options_conf_values_name, patrib.options_conf_values_price,patrib.price_prefix from " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib, " . DB_TBL_ITEMS_OPTIONS_CONF_VALUES . " pov where patrib.items_id = '" . $items_id . "' and patrib.options_conf_id = '" . (int)$items_options_name['items_options_conf_id'] . "' and patrib.options_conf_values_id = pov.items_options_conf_values_id and pov.language_id = '" . (int)$languages_id . "' and (". $parent_values.")");
			while ($items_options = go_db_fetch_array($items_options_query)) {
				$items_options_array[] = array('id' => $items_options['items_options_conf_values_id'], 'text' => $items_options['items_options_conf_values_name']);
				if ($items_options['options_conf_values_price'] != '0') {
					$option_price_display = ' (' . $items_options['price_prefix'] . 
							$currencies->display_price($items_options['options_conf_values_price'], go_get_tax_rate($item_info['items_tax_class_id'])) .') ';
					if (PRICES_LOGGED_IN == 'false') {
						$option_price_display_d = $option_price_display;
					}
					if ((PRICES_LOGGED_IN == 'true') && (!go_session_is_registered('attendee_id'))) {
						$option_price_display_d = '';
					}  else  {
						$option_price_display_d = $option_price_display;
					}   
					$items_options_array[sizeof($items_options_array)-1]['text'] .= $option_price_display_d;
				}
			}  
			if ((isset($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]))&&($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]!=0)&&(sizeof($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']])!=0)){
				$selected_characteristic[$items_options_name['items_options_conf_id']] = $array_selected_konfigurator_values[$items_options_name['items_options_conf_id']];
			} elseif ($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]==="0") {$selected_characteristic[$items_options_name['items_options_conf_id']] = false;}
			else { 
				$query_find_default = go_db_query("select items_options_conf_values_id  from ".DB_TBL_ITEMS_DEFAULT_OPTIONS_CONF." where items_options_conf_id='".(int)$items_options_name['items_options_conf_id']."' and items_id='" . $items_id . "'");
				if (go_db_num_rows($query_find_default)!=0) { $find_default = mysqli_result($query_find_default, 0, 'items_options_conf_values_id'); $selected_characteristic[$items_options_name['items_options_conf_id']]=$find_default;}
				else																				{ $selected_characteristic[$items_options_name['items_options_conf_id']] = false;}
			}
			$i++;

			$mehroptionen = preg_replace("/<-items_options_name$i->/",  $items_options_name['items_options_conf_name'] . ':', $mehroptionen); 
			$items_type=go_items_type($items_options_name['items_options_conf_id'],$languages_id);

			if ($items_type=='selectframe') { 
				reset($selected_characteristic);
				$items_options_array_null = array();
				$items_options_array_null[] = array('id'=>0,'text'=>"---------");
				$items_options_array_with_null = array_merge($items_options_array_null,$items_options_array); 
				$desc = go_display_value_desc($items_options_name['items_options_conf_id'],"",$array_selected_konfigurator_values,$items_id);
				if ($desc!="")  $list_selectframe = "<p style=\"margin:4px\"></p><div class=\"option_desc\">".$desc."</div><p style=\"margin:4px\"></p>"; else $list_selectframe="";
				$mehroptionen = preg_replace("/<-items_options_select$i->/",  go_fetch_pull_down_menu('id[' . $items_options_name['items_options_conf_id'] . ']', $items_options_array_with_null, $selected_characteristic[$items_options_name['items_options_conf_id']],' onChange="konfigurator.submit()"').$list_selectframe,$mehroptionen);
			}//selectframe
			elseif ($items_type=='radioframe'){ 
				$items_options_array_null = array();
				$items_options_array_null[] = array('id'=>"0",'text'=>"keine");
				$items_options_array_with_null = array_merge($items_options_array_null,$items_options_array);
				$mehroptionen = preg_replace("/<-items_options_select$i->/",  go_fetch_radio_list_menu('id[' . $items_options_name['items_options_conf_id'] . ']', $items_options_array_with_null, $selected_characteristic,' onClick="konfigurator.submit()"',$items_id,$items_options_name['items_options_conf_id'],$array_selected_konfigurator_values),$mehroptionen);
			}//radioframe
			elseif ($items_type=='checkbox'){ 
				$items_options_array_null = array(); 
				$items_options_array_with_null = array_merge($items_options_array_null,$items_options_array);
				$mehroptionen = preg_replace("/<-items_options_select$i->/",  go_fetch_check_list_menu('id[' . $items_options_name['items_options_conf_id'] . ']', $items_options_array_with_null, $selected_characteristic,' onClick="konfigurator.submit()"',$items_id,$items_options_name['items_options_conf_id'],$array_selected_konfigurator_values),$mehroptionen);
			}//radioframe
		}

		$mehroptionen .= "</form>";
		$mehroptionen = preg_replace("/<-items_price_konfigurator->/",$currencies->display_price_conf(add_options_price($items_id,$items_price_konfigurator,$array_selected_konfigurator_values,go_get_tax_rate($item_info['items_tax_class_id']))),$mehroptionen);
		return $mehroptionen;
	}

	function add_options_price($items_id,$items_price_konfigurator,$array_selected_konfigurator_values,$tax){
		global $languages_id,$currencies,$konfigurator,$selected_defaults;
		$flag=false; 

		if ((is_array($array_selected_konfigurator_values)&&(sizeof($array_selected_konfigurator_values)!=0))||((is_array($selected_defaults)&&(sizeof($selected_defaults)!=0)))) { 
			$array_id_value_pairs=" and (";
			$array_id_value_pairs_array = array();
			$konfigurator_keys = array_keys($array_selected_konfigurator_values);
			for ($i=0; $i<sizeof($konfigurator_keys);$i++) {
				if (($konfigurator_keys[$i]!=0)&&($array_selected_konfigurator_values[$konfigurator_keys[$i]]!=0)) { 
					$value=$array_selected_konfigurator_values[$konfigurator_keys[$i]];
					if (is_array($array_selected_konfigurator_values[$konfigurator_keys[$i]])) {$value = implode(", ",$array_selected_konfigurator_values[$konfigurator_keys[$i]]);}
					$value_list = "(".$value.")";
					$array_id_value_pairs_array[] = "  (options_conf_id='".$konfigurator_keys[$i]."' and options_conf_values_id in ".$value_list." ) ";
					$flag=true;
				}//if 
			}//for


			$array_id_selected_defaults_pairs_array = array();
			if (is_array($selected_defaults)){
				$konfigurator_selected_defaults_keys = array_keys($selected_defaults);
				for ($j=0; $j<sizeof($konfigurator_selected_defaults_keys);$j++) {
					if (($konfigurator_selected_defaults_keys[$j]!=0)&&($selected_defaults[$konfigurator_selected_defaults_keys[$j]]!=0)) { 
						$parent=true;
						$cur_parent = go_parent($konfigurator_selected_defaults_keys[$j],$selected_defaults[$konfigurator_selected_defaults_keys[$j]],$items_id); 
						$cur_parent_keys = array_keys($cur_parent);
						for ($pa=0; $pa<sizeof($cur_parent_keys);$pa++){ 
							if (($cur_parent[$cur_parent_keys[$pa]]!=0)&&($selected_defaults[$cur_parent_keys[$pa]]!=$cur_parent[$cur_parent_keys[$pa]])&&($array_selected_konfigurator_values[$cur_parent_keys[$pa]]!=$cur_parent[$cur_parent_keys[$pa]])) {$parent=false;break;}
						}//for parent

						if ($parent) { 			
							$array_id_selected_defaults_pairs_array[] = "  (options_conf_id='".$konfigurator_selected_defaults_keys[$j]."' and options_conf_values_id='".$selected_defaults[$konfigurator_selected_defaults_keys[$j]]."') ";
						} 
						$flag=true;
					}//if 
				}//for
			} 
			$array_id_value_pairs_array = array_merge($array_id_value_pairs_array,$array_id_selected_defaults_pairs_array);
			$array_id_value_pairs .= implode(' or ', $array_id_value_pairs_array);
			$array_id_value_pairs .= ")";
			if ($array_id_value_pairs==" and ()") {$array_id_value_pairs="";}   
			if ($flag) {
				$items_options_query = go_db_query("select pov.items_options_conf_values_id, pov.items_options_conf_values_name, patrib.options_conf_values_price,patrib.price_prefix from " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib, " . DB_TBL_ITEMS_OPTIONS_CONF_VALUES . " pov where patrib.items_id = '" . $items_id . "' ".$array_id_value_pairs." and patrib.options_conf_values_id = pov.items_options_conf_values_id and pov.language_id = '" . (int)$languages_id . "'");
				while ($items_options = go_db_fetch_array($items_options_query)) { 
					if ($items_options['options_conf_values_price'] != '0') { 
						$price_option = $items_options['options_conf_values_price'];
						if ($items_options['price_prefix']=='+'){
							$items_price_konfigurator += go_add_tax($price_option, $tax);
						} 
						elseif ($items_options['price_prefix']=='-'){
							$items_price_konfigurator -= go_add_tax($price_option, $tax);
						}
					}//if     
				}//while
			}//if flag  
		}
		return $items_price_konfigurator;
	}//add_options_price


	function go_find_default($items_id, $languages_id, $parent_values, $array_selected_konfigurator_values){  
		$parent_values_array = array();
		$find_default_query_basic = go_db_query("select distinct popt.items_options_conf_id, popt.items_options_conf_name,patrib.options_conf_parent_id,patrib.options_conf_values_parent_id from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib where patrib.items_id='" . $items_id . "' and patrib.options_conf_id = popt.items_options_conf_id and popt.language_id = '" . (int)$languages_id . "' and (".$parent_values.") order by popt.items_options_conf_id");
		while ($find_default_basic = go_db_fetch_array($find_default_query_basic)) {
			$parent_values_default_array = $find_default_basic['items_options_conf_id'];
			$find_default_query = go_db_query("select * from ".DB_TBL_ITEMS_DEFAULT_OPTIONS_CONF." where items_id='".$items_id."' and items_options_conf_id='".$parent_values_default_array."'");
			if (go_db_num_rows($find_default_query)!=0) { 
				if ($array_selected_konfigurator_values[$parent_values_default_array]!=="0") {
					$default_option_id = mysqli_result($find_default_query , 0, 'items_options_conf_id');
					$default_option_value_id = mysqli_result($find_default_query, 0, 'items_options_conf_values_id'); 
					$parent=true;
					$cur_parent = go_parent($default_option_id,$default_option_value_id,$items_id); 
					$cur_parent_keys = array_keys($cur_parent);
					for ($pa=0; $pa<sizeof($cur_parent_keys);$pa++){ 
						if (($cur_parent[$cur_parent_keys[$pa]]!=0)&&($array_selected_konfigurator_values[$cur_parent_keys[$pa]]!=$cur_parent[$cur_parent_keys[$pa]])) {$parent=false;break;}
					}//for parent

					if (is_array($array_selected_konfigurator_values)) {
						if (($parent)&&((!array_key_exists($default_option_id,$array_selected_konfigurator_values))||($array_selected_konfigurator_values[$default_option_id]==="0")||(sizeof($array_selected_konfigurator_values)==0))) {
							$parent_values_array[] = " (patrib.options_conf_parent_id='".$default_option_id."' and patrib.options_conf_values_parent_id='".$default_option_value_id."')";
						}
					}
				}  
			}
		}//while 
		return  $parent_values_array;
	}


	function go_parent($key, $value, $items_id){
		$query = go_db_query("select options_conf_parent_id, options_conf_values_parent_id from ".DB_TBL_ITEMS_CHARACTERISTICS_CONF." where items_id='".$items_id."' and options_conf_id='".$key."' and options_conf_values_id='".$value."'");
		if (go_db_num_rows($query)!=0) {
			$parent_key = mysqli_result($query, 0, 'options_conf_parent_id'); 
			$parent_value = mysqli_result($query, 0, 'options_conf_values_parent_id');}
		else {$parent_key=0;$parent_value=0;}
		$parent_array = array($parent_key=>$parent_value);	

		if (($parent_key!=0)&&($parent_value!=0)) { 
			$high_level_parent_array = go_parent($parent_key,$parent_value,$items_id); 
			$parent_array = $parent_array+$high_level_parent_array;
		}

		return 	$parent_array;
	}	

	function go_display_value_each_desc($items_options_conf_id,$items_options_value_conf_id,$items_id){
		global $languages_id;
		$value_desc_query = go_db_query("select items_options_conf_values_desc from ".DB_TBL_ITEMS_OPTIONS_CONF_VALUES." where items_options_conf_values_id = '" . $items_options_value_conf_id . "' and language_id = '" . (int)$languages_id . "'");
		if (go_db_num_rows($value_desc_query)!=0) {$value_desc = mysqli_result($value_desc_query, 0, 'items_options_conf_values_desc'); return $value_desc;} else {return "";}
	}

	function go_display_value_desc($items_options_conf_id,$items_option_value_conf_id,$array_selected_konfigurator_values,$items_id){
		global $languages_id,$selected_defaults; 
		if ((isset($array_selected_konfigurator_values[$items_options_conf_id]))&&($array_selected_konfigurator_values[$items_options_conf_id]!=0)){ 
			if ($items_option_value_conf_id=="") {
				$value_desc_query = go_db_query("select items_options_conf_values_desc from ".DB_TBL_ITEMS_OPTIONS_CONF_VALUES." where items_options_conf_values_id = '" . $array_selected_konfigurator_values[$items_options_conf_id] . "' and language_id = '" . (int)$languages_id . "'");
			}
			else {
				$value_desc_query = go_db_query("select items_options_conf_values_desc from ".DB_TBL_ITEMS_OPTIONS_CONF_VALUES." where items_options_conf_values_id = '" . $items_option_value_conf_id . "' and language_id = '" . (int)$languages_id . "'");
			}
			if (go_db_num_rows($value_desc_query)!=0) {$value_desc = mysqli_result($value_desc_query, 0, 'items_options_conf_values_desc'); return $value_desc;} else {return "";}
		}
		elseif ($array_selected_konfigurator_values[$items_options_conf_id]==="0") {return "";}
		else { 
			$find_default_query = go_db_query("select * from ".DB_TBL_ITEMS_DEFAULT_OPTIONS_CONF." where items_id='".$items_id."' and items_options_conf_id='".$items_options_conf_id."'");
			if (go_db_num_rows($find_default_query)!=0) {
				$find_default = mysqli_result($find_default_query, 0, 'items_options_conf_values_id');
				$value_desc_query = go_db_query("select items_options_conf_values_desc from ".DB_TBL_ITEMS_OPTIONS_CONF_VALUES." where items_options_conf_values_id = '" . $find_default . "' and language_id = '" . (int)$languages_id . "'");
				if (go_db_num_rows($value_desc_query)!=0) {
					$value_desc = mysqli_result($value_desc_query, 0, 'items_options_conf_values_desc'); 
					$parent=true;
					$cur_parent = go_parent($items_options_conf_id,$find_default,$items_id); 
					$selected_defaults[$items_options_conf_id]=$find_default;
					$cur_parent_keys = array_keys($cur_parent);
					for ($pa=0; $pa<sizeof($cur_parent_keys);$pa++){ 
						if (($cur_parent[$cur_parent_keys[$pa]]!=0)&&($selected_defaults[$cur_parent_keys[$pa]]!=$cur_parent[$cur_parent_keys[$pa]])&&($array_selected_konfigurator_values[$cur_parent_keys[$pa]]!=$cur_parent[$cur_parent_keys[$pa]])) {$parent=false;break;}
					}//for parent

					if ($parent) {return $value_desc;} else {return "";}
				} else {return "";}
			}//if 
			else {return "";}
		}//else
	}

	function go_items_type($items_options_conf_id,$languages_id){
		$items_type_query = go_db_query("select items_type from ".DB_TBL_ITEMS_OPTIONS_CONF." where items_options_conf_id='".(int)$items_options_conf_id."' and language_id='".$languages_id."'");
		if (go_db_num_rows($items_type_query)!=0) {
			$items_type = mysqli_result($items_type_query, 0, 'items_type');
			return $items_type;
		}
	}

	function go_fetch_radio_list_menu($name, $value_array, $checked_array, $parameters,$items_id,$items_options_conf_id,$array_selected_konfigurator_values){
		for ($i=0; $i<sizeof($value_array);$i++){
			if (in_array($value_array[$i]['id'],$checked_array)) {$checked=true;} else {$checked=false;}
			$list_radio .= go_fetch_radio_field($name, $value_array[$i]['id'], $checked, $parameters ). $value_array[$i]['text']."<br>";
			if ($checked) {
				$desc = go_display_value_desc($items_options_conf_id,$value_array[$i]['id'],$array_selected_konfigurator_values,$items_id);
				if ($desc!="") $list_radio .= "<p style=\"margin:4px\"></p><div class=\"option_desc\">".$desc."</div><p style=\"margin:4px\"></p>";}
		}
		return $list_radio;
	}//go_fetch_radio_list_menu

	function go_fetch_check_list_menu($name, $value_array, $checked_array, $parameters,$items_id,$items_options_conf_id,$array_selected_konfigurator_values){ 
		$checked_array_keys = array_keys($checked_array);
		for ($i=0; $i<sizeof($value_array);$i++){
			for ($c=0; $c<sizeof($checked_array_keys);$c++) {
				if (is_array($checked_array[$checked_array_keys[$c]])) {
					if ((in_array($value_array[$i]['id'],$checked_array[$checked_array_keys[$c]]))&&((!in_array("0",$checked_array[$checked_array_keys[$c]]))||($value_array[$i]['id']=="0"))) {$checked=true;break;} else {$checked=false;}
				}
				else {if ($value_array[$i]['id']==$checked_array[$checked_array_keys[$c]]) {$checked=true;break;} else {$checked=false;}}	
			}
			$list_radio .= go_fetch_checkbox_field($name.'['.$i.']', $value_array[$i]['id'], $checked, $parameters ). $value_array[$i]['text']."<br>";
			if ($checked) {
				$desc = go_display_value_desc($items_options_conf_id,$value_array[$i]['id'],$array_selected_konfigurator_values,$items_id);
				if ($desc!="")	 $list_radio .= "<p style=\"margin:4px\"></p><div class=\"option_desc\">".$desc."</div><p style=\"margin:4px\"></p>";}
		}
		return $list_radio;
	}//go_fetch_radio_list_menu


	function go_display_hidden_konfigurator($items_id,$array_selected_konfigurator_values){
		global $languages_id,$currencies;

		$parent_values_array = array();
		$selected_characteristic = array();

		$item_info_query = go_db_query("select p.items_tax_class_id from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_status = '1' and p.items_id = '" . $items_id . "' and pd.items_id = p.items_id and pd.language_id = '" . (int)$languages_id . "' $date_availability ");
		$item_info = go_db_fetch_array($item_info_query);


		if (sizeof($array_selected_konfigurator_values)==0) {
			$parent_values_array[] = "( patrib.options_conf_parent_id='0' and patrib.options_conf_values_parent_id='0')";
			$array_selected_konfigurator_values = array();
		}
		else { 
			$parent_values_array[] = " (patrib.options_conf_parent_id='0' and patrib.options_conf_values_parent_id='0')";
			$parent_keys = array_keys($array_selected_konfigurator_values);
			for ($a=0; $a<sizeof($parent_keys); $a++){
				if (($array_selected_konfigurator_values[$parent_keys[$a]]!=0)||(sizeof($array_selected_konfigurator_values[$parent_keys[$a]])!=0)){
					$value = $array_selected_konfigurator_values[$parent_keys[$a]];
					if (is_array($array_selected_konfigurator_values[$parent_keys[$a]])){
						$value = implode(", ",$array_selected_konfigurator_values[$parent_keys[$a]]);
					}
					$value_list = "(".$value.")";
					$parent_values_array[] = "  (patrib.options_conf_parent_id='".$parent_keys[$a]."' and patrib.options_conf_values_parent_id in ".$value_list." )";
				}
			}//for a 
		}
		$parent_values = implode(" or ",$parent_values_array);


		$parent_values_default = go_find_default($items_id, $languages_id, $parent_values, $array_selected_konfigurator_values); 
		$parent_values_array = array_merge($parent_values_array, $parent_values_default);


		$parent_values = implode(" or ",$parent_values_array); 
		$mehroptionen = "";
		$items_options_name_query = go_db_query("select distinct popt.items_options_conf_id, popt.items_options_conf_name,patrib.options_conf_parent_id,patrib.options_conf_values_parent_id from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib where patrib.items_id='" . $items_id . "' and patrib.options_conf_id = popt.items_options_conf_id and popt.language_id = '" . (int)$languages_id . "' and (".$parent_values.") order by popt.items_options_conf_name");
		$mehroptionen .= '<table border="0" cellspacing="2" cellpadding="0">';
		$i = 0; $selected_defaults=array();
		while ($items_options_name = go_db_fetch_array($items_options_name_query)) {
			$i++;
			$mehroptionen .= "<tr><td class=\"main\"><-items_options_select$i-></td></tr>"; 
		} 
		$mehroptionen .= '</table>';

		$i = 0; 
		$items_options_name_query = go_db_query("select distinct popt.items_options_conf_id, popt.items_options_conf_name,patrib.options_conf_parent_id,patrib.options_conf_values_parent_id from " . DB_TBL_ITEMS_OPTIONS_CONF . " popt, " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib where patrib.items_id='" . $items_id . "' and patrib.options_conf_id = popt.items_options_conf_id and popt.language_id = '" . (int)$languages_id . "' and (".$parent_values.") order by popt.items_options_conf_name");
		while ($items_options_name = go_db_fetch_array($items_options_name_query)) {
			$items_options_array = array();  
			$items_options_query = go_db_query("select distinct  pov.items_options_conf_values_id, pov.items_options_conf_values_name, patrib.options_conf_values_price,patrib.price_prefix from " . DB_TBL_ITEMS_CHARACTERISTICS_CONF . " patrib, " . DB_TBL_ITEMS_OPTIONS_CONF_VALUES . " pov where patrib.items_id = '" . $items_id . "' and patrib.options_conf_id = '" . (int)$items_options_name['items_options_conf_id'] . "' and patrib.options_conf_values_id = pov.items_options_conf_values_id and pov.language_id = '" . (int)$languages_id . "' and (". $parent_values.")");
			while ($items_options = go_db_fetch_array($items_options_query)) {
				$items_options_array[] = array('id' => $items_options['items_options_conf_values_id'], 'text' => $items_options['items_options_conf_values_name']);

			}  
			if ((isset($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]))&&($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]!=0)&&(sizeof($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']])!=0)){
				$selected_characteristic[$items_options_name['items_options_conf_id']] = $array_selected_konfigurator_values[$items_options_name['items_options_conf_id']];
			} elseif ($array_selected_konfigurator_values[$items_options_name['items_options_conf_id']]==="0") {$selected_characteristic[$items_options_name['items_options_conf_id']] = false;}
			else { 
				$query_find_default = go_db_query("select items_options_conf_values_id  from ".DB_TBL_ITEMS_DEFAULT_OPTIONS_CONF." where items_options_conf_id='".(int)$items_options_name['items_options_conf_id']."' and items_id='" . $items_id . "'");
				if (go_db_num_rows($query_find_default)!=0) { $find_default = mysqli_result($query_find_default, 0, 'items_options_conf_values_id'); $selected_characteristic[$items_options_name['items_options_conf_id']]=$find_default;}
				else																				{ $selected_characteristic[$items_options_name['items_options_conf_id']] = false;}
			}
			$i++;

			$mehroptionen = preg_replace("/<-items_options_name$i->/",  $items_options_name['items_options_conf_name'] . ':', $mehroptionen); 
			$items_type=go_items_type($items_options_name['items_options_conf_id'],$languages_id);

			if ($items_type=='checkbox'){
				$items_options_array_null = array(); $hidden_check = "";
				$items_options_array_with_null = array_merge($items_options_array_null,$items_options_array);

				if (is_array($selected_characteristic[$items_options_name['items_options_conf_id']])) {	
					$selected_characteristic_keys = array_keys($selected_characteristic[$items_options_name['items_options_conf_id']]);
					for ($sa=0;$sa<sizeof($selected_characteristic_keys); $sa++){ 
						$flag_hidden=false;
						for ($ce=0; $ce<sizeof($items_options_array_with_null);$ce++){
							if ($items_options_array_with_null[$ce]['id']==$selected_characteristic[$items_options_name['items_options_conf_id']][$selected_characteristic_keys[$sa]]) {$flag_hidden=true;break;}
						}
						if (!$flag_hidden) {$select_hidden=false;}
						else {$select_hidden=$selected_characteristic[$items_options_name['items_options_conf_id']][$selected_characteristic_keys[$sa]];}
						$hidden_check .= go_fetch_hidden_field('id[' . $items_options_name['items_options_conf_id'] . ']['.$selected_characteristic_keys[$sa].']', $select_hidden);

					}//for sa
				}//if array
				else { 
					$flag_hidden=false;
					for ($ce=0; $ce<sizeof($items_options_array_with_null);$ce++){
						if ($items_options_array_with_null[$ce]['id']==$selected_characteristic[$items_options_name['items_options_conf_id']]) {$flag_hidden=true;break;}
					}
					if (!$flag_hidden) {$select_hidden=false;}
					else {$select_hidden=$selected_characteristic[$items_options_name['items_options_conf_id']];}
					$hidden_check = go_fetch_hidden_field('id[' . $items_options_name['items_options_conf_id'] . ']', $select_hidden);
				} 
				$mehroptionen = preg_replace("/<-items_options_select$i->/", $hidden_check ,$mehroptionen);
			}
			else {
				$items_options_array_null = array();
				$items_options_array_with_null = array_merge($items_options_array_null,$items_options_array); 
				$flag_hidden=false;
				for ($ce=0; $ce<sizeof($items_options_array_with_null);$ce++){
					if ($items_options_array_with_null[$ce]['id']==$selected_characteristic[$items_options_name['items_options_conf_id']]) {$flag_hidden=true;break;}
				}
				if (!$flag_hidden) {$select_hidden=false;}
				else {$select_hidden=$selected_characteristic[$items_options_name['items_options_conf_id']];}
				$mehroptionen = preg_replace("/<-items_options_select$i->/",  go_fetch_hidden_field('id[' . $items_options_name['items_options_conf_id'] . ']', $select_hidden),$mehroptionen);
			}


		}

		return $mehroptionen;
	}

}	
?>
