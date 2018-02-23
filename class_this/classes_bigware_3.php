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
 class cache {
# temp resource container
	var $cache_query;
# cache memory container and parameter
	var $data, $keep_in_memory;
# languages
	var $lang_id;	
  function __construct($languages_id, $memory = false) {
    $this->cache($languages_id, $memory = false);
  }
 	# initialize with the actual languages_id or pass an integer
	function cache($languages_id, $memory = false){
		$this->lang_id = (int)$languages_id; 
		$this->keep_in_memory = $memory; 
		$this->data = array(); 
		$this->cache_gc(); 
	} # end class constructor
 	function save_cache($name, $value, $method='RETURN', $gzip=1, $global=0, $expires = '30/days'){
		# convert $expires to date in the future 
		$expires = $this->convert_time($expires); 
		
		# if the method is ARRAY serialize the data
		if ($method == 'ARRAY' ) $value = serialize($value);
		
		# check to see if it should be compressed
		$value = ( $gzip === 1 ? base64_encode(gzdeflate($value, 1)) : addslashes($value) ); 
		
		# initialize the data array for either insert or update
		$sql_data_array = array('cache_id' => md5($name), 
								'cache_language_id' => (int)$this->lang_id,
								'cache_name' => $name,
								'cache_data' => $value,
								'cache_global' => (int)$global,
								'cache_gzip' => (int)$gzip,
								'cache_method' => $method,
								'cache_date' => date("Y-m-d h:i:s"),
								'cache_expires' => $expires
								);
								
		# check whether it is already in the database
		# $is_cached and $is_expired is passed by reference!
		$this->is_cached($name, $is_cached, $is_expired);
		
		# $is_cached is returned from above as either true / false
		$cache_check = ( $is_cached ? 'true' : 'false' ); 
		
		# swtich to find out whether we need to update or insert
		switch ( $cache_check ) {
			case 'true': 
				go_db_carry('cache', $sql_data_array, 'update', "cache_id='".md5($name)."'");
				break;
				
			case 'false': 
				go_db_carry('cache', $sql_data_array, 'insert');
				break;
				
			default: 
				break;
		} # end switch ($cache check)
		
		# unset the variables...clean as we go
		unset($value, $expires, $sql_data_array);
		
	}# end function save_cache()
	
 	function get_cache($name = 'GLOBAL', $local_memory = false){
		# define the column select list
		$select_list = 'cache_id, cache_language_id, cache_name, cache_data, cache_global, cache_gzip, cache_method, cache_date, cache_expires';
		
		# global check, used below
		$global = ( $name == 'GLOBAL' ? true : false ); 
		
		# switch the $name to determine the right query to run
		switch($name){
			case 'GLOBAL': 
				$this->cache_query = go_db_query("SELECT ".$select_list." FROM cache WHERE cache_language_id='".(int)$this->lang_id."' AND cache_global='1'");
				break;
				
			default: 
				$this->cache_query = go_db_query("SELECT ".$select_list." FROM cache WHERE cache_id='".md5($name)."' AND cache_language_id='".(int)$this->lang_id."'");
				break;
		} # end switch ($name)
		
		# number of rows for the query
		$num_rows = go_db_num_rows($this->cache_query);
		
		if ( $num_rows ){ 
			$container = array();
			while($cache = go_db_fetch_array($this->cache_query)){
				# grab the cache name
				$cache_name = $cache['cache_name']; 
				
				# check to see if it is expired
				if ( $cache['cache_expires'] > date("Y-m-d h:i:s") ) { 
				
					# determine whether data was compressed
					$cache_data = ( $cache['cache_gzip'] == 1 ? gzinflate(base64_decode($cache['cache_data'])) : stripslashes($cache['cache_data']) );
					
					# switch on the method
					switch($cache['cache_method']){
						case 'EVAL': 
							@eval("$cache_data");
							break;
							
						case 'ARRAY': 
							$cache_data = unserialize($cache_data);							
						case 'RETURN': 
						default:
							break;
					} # end switch ($cache['cache_method'])
					
					# copy the data to an array
					if ($global) $container['GLOBAL'][$cache_name] = $cache_data; 
					else $container[$cache_name] = $cache_data; 
				
				} else { 
					if ($global) $container['GLOBAL'][$cache_name] = false; 
					else $container[$cache_name] = false; 
				}# end if ( $cache['cache_expires'] > date("Y-m-d h:i:s") )
			
				# if keep_in_memory is true save to array
				if ( $this->keep_in_memory || $local_memory ) {
					if ($global) $this->data['GLOBAL'][$cache_name] = $container['GLOBAL'][$cache_name]; 
					else $this->data[$cache_name] = $container[$cache_name]; 
				}			
				
			} # end while ($cache = go_db_fetch_array($this->cache_query))
			
			# unset some varaibles...clean as we go
			unset($cache_data);
			go_db_free_result($this->cache_query);
			
			# switch on true, case num_rows
			switch (true) {
				case ($num_rows == 1): 
					if ($global){ 
						# the value is false or is not set, return false
						if ($container['GLOBAL'][$cache_name] == false || !isset($container['GLOBAL'][$cache_name])) return false;
						else return $container['GLOBAL'][$cache_name]; 
					} else { 
						# the valu is false or is not set, return false
						if ($container[$cache_name] == false || !isset($container[$cache_name])) return false;
						else return $container[$cache_name]; 
					} # end if ($global)
					
				case ($num_rows > 1): 
				default: 
					return $container; 
					break;
			}# end switch (true)
			
		} else { 
			return false;
		}# end if ( $num_rows )
		
	} # end function get_cache()
 	function get_cache_memory($name, $method = 'RETURN'){
		# check to see if there is GLOBAL in memory first
		# if so, use that over non-GLOBAL		
		$data = ( isset($this->data['GLOBAL'][$name]) ? $this->data['GLOBAL'][$name] : $this->data[$name] );
		
		# sanity check to make sure the data has content
		if ( isset($data) && !empty($data) && $data != false ){ 
			
			# switch on the method
			switch($method){
				case 'EVAL': 
					eval("$data");
					return true;
					break;
					
				case 'ARRAY': 
				case 'RETURN':
				default:
					return $data;
					break;
			} # end switch ($method)
		
		} else { 
			return false;
		} # end if (isset($data) && !empty($data) && $data != false)
		 		
	} # end function get_cache_memory()
 	function cache_gc(){
		# just deleting entries that are expired
		go_db_query("DELETE FROM cache WHERE cache_expires <= '" . date("Y-m-d h:i:s") . "'" );
	}
 	function convert_time($expires){ 
		# explode the passed parameter
		$expires = explode('/', $expires);
		switch( strtolower($expires[1]) ){ 
			case 'seconds':
				$expires = mktime( date("h"), date("i"), date("s")+(int)$expires[0], date("m"), date("d"), date("Y") );
				break;
			
			case 'minutes':
				$expires = mktime( date("h"), date("i")+(int)$expires[0], date("s"), date("m"), date("d"), date("Y") );
				break;
			
			case 'hours':
				$expires = mktime( date("h")+(int)$expires[0], date("i"), date("s"), date("m"), date("d"), date("Y") );
				break;
			
			case 'days':
				$expires = mktime( date("h"), date("i"), date("s"), date("m"), date("d")+(int)$expires[0], date("Y") );
				break;
			
			case 'months':
				$expires = mktime( date("h"), date("i"), date("s"), date("m")+(int)$expires[0], date("d"), date("Y") );
				break;
			
			case 'years':
				$expires = mktime( date("h"), date("i"), date("s"), date("m"), date("d"), date("Y")+(int)$expires[0] );
				break;
			
			default: 
				$expires = mktime( date("h"), date("i"), date("s"), date("m")+1, date("d"), date("Y") );
				break;
		} # end switch( strtolower($expires[1]) )
		
		# return the converted expiration date
		return date("Y-m-d h:i:s", $expires);
				
	} # end function convert_time()
 	function is_cached($name, &$is_cached, &$is_expired){ 
		# query for the expiration date
		$this->cache_query = go_db_query("SELECT cache_expires FROM cache WHERE cache_id='".md5($name)."' AND cache_language_id='".(int)$this->lang_id."' LIMIT 1");
		
		# check to see if there were any rows returned
		$is_cached = ( go_db_num_rows($this->cache_query ) ? true : false );
		
		if ($is_cached){ 
			# fetch the array
			$check = go_db_fetch_array($this->cache_query);
			
			# check to see if it is expired
			$is_expired = ( $check['cache_expires'] <= date("Y-m-d h:i:s") ? true : false );
			
			# unset $check...clean as we go
			unset($check);
		}
		
		# free the result...clean as we go
		go_db_free_result($this->cache_query);
	}# end function is_cached()
	
} # end of cache class
?>