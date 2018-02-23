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
if (file_exists(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator')) {
  require(FOLDER_ABSOLUT_CATALOG . 'modules/konfigurator/class_this/classes_bigware_9.php');
}else{	
 class konfigurator {
   var $chosen_values = array();
	 var $cleanlist = array(); 
    function konfigurator($item_id) {
      $this->chosen_values[$items_id] = array();
    }
		function update_values($array_options,$items_id){
		 if (is_array($array_options)){
	    $global_parent_keys = array_keys($array_options);
       for ($i=0; $i<sizeof($global_parent_keys); $i++){
	      if (($array_options[$global_parent_keys[$i]]!=0)||(sizeof($array_options[$global_parent_keys[$i]])!=0)) {   
		     $cur_parent = $this->parent($global_parent_keys[$i],$array_options[$global_parent_keys[$i]], $items_id); 
		     $cur_parent_key = array_keys($cur_parent);
				 for ($cur=0; $cur<sizeof($cur_parent_key);$cur++){ 
					if (is_array($array_options[$cur_parent_key[$cur]])) {
					 for ($arr=0; $arr<sizeof($array_options[$cur_parent_key[$cur]]); $arr++){
					  if (($array_options[$cur_parent_key[$cur]][$arr]!=$cur_parent[$cur_parent_key[$cur]])&&($cur_parent[$cur_parent_key[$cur]]!=0)) {$this->cleanlist[] = $global_parent_keys[$i];}
					 }//arr
					}
					else {
					 if (($array_options[$cur_parent_key[$cur]]!=$cur_parent[$cur_parent_key[$cur]])&&($cur_parent[$cur_parent_key[$cur]]!=0)) {$this->cleanlist[] = $global_parent_keys[$i];}
					} 
				 }//for cur    
		    } 
	     }//for 
	     for ($i=0; $i<sizeof($global_parent_keys); $i++){
			  
			  if (in_array($global_parent_keys[$i],$this->cleanlist)) {
				 $this->chosen_values[$items_id][$global_parent_keys[$i]]=0;
				}
				else {
	       $this->chosen_values[$items_id][$global_parent_keys[$i]]=$array_options[$global_parent_keys[$i]];
				}//else cleaenlist 
	     }
		 }//if is array 
		 return $this->chosen_values[$items_id];
	 }
	 
	 function parent($key, $value, $items_id){ 
		if (is_array($value)){
		 if (in_array("0",$value)) {$value= "0";}
		 else {$value = implode(", ",$value);}
		}
		$value_list = "(".$value.")"; 
	  $query = go_db_query("select options_conf_parent_id, options_conf_values_parent_id from ".DB_TBL_ITEMS_CHARACTERISTICS_CONF." where items_id='".$items_id."' and options_conf_id='".$key."' and options_conf_values_id in ".$value_list." ");
	  if (go_db_num_rows($query)!=0) {
	   $parent_key = mysqli_result($query, 0, 'options_conf_parent_id'); 
		 $parent_value = mysqli_result($query, 0, 'options_conf_values_parent_id');}
	  else {$parent_key=0;$parent_value=0;}
	  $parent_array = array($parent_key=>$parent_value);	
		
		if (($parent_key!=0)&&($parent_value!=0)) { 
		$high_level_parent_array = $this->parent($parent_key,$parent_value,$items_id); 
		$parent_array = $parent_array+$high_level_parent_array;
		}
		
	  return 	$parent_array;
	}	
		
		
  }
}
?>
