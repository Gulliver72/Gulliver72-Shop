<?php
if (!function_exists('pagadors_strftime')){
  function pagadors_strftime($format, $timestamp = '') {
  	global $DATE_FORMAT_ARRAY;
  	
  	
  
  	
		// wenn keine Übersetzung vorhanden ist, nehme english
		if ((!isset($DATE_FORMAT_ARRAY)) OR ($DATE_FORMAT_ARRAY == '')) {
				$trans = array("Monday"=>"Monday", "Tuesday"=>"Tuesday", "Wednesday"=>"Wednesday", "Thursday"=>"Thursday", "Friday"=>"Friday", "Saturday"=>"Saturday", "Sunday"=>"Sunday", "Mon"=>"Mon", "Tue"=>"Tue", "Wed"=>"Wed", "Thu"=>"Thu", "Fri"=>"Fri", "Sat"=>"Sat", "Sun"=>"Sun", "January"=>"January", "February"=>"February", "March"=>"March", "April"=>"April", "May"=>"May", "June"=>"June", "July"=>"July", "August"=>"August", "September"=>"September", "October"=>"October", "November"=>"November", "December"=>"December", "Jan"=>"Jan", "Feb"=>"Feb", "Mar"=>"Mar", "Apr"=>"Apr", "May"=>"May", "Jun"=>"Jun", "Jul"=>"Jul", "Aug"=>"Aug", "Sep"=>"Sep", "Oct"=>"Oct", "Nov"=>"Nov", "Dec"=>"Dec");
		}else{
			$trans = $DATE_FORMAT_ARRAY;
		}


    if ($timestamp == '') {
        $timestamp = time();
    }
 
    //return strtr(strftime($format, $timestamp), $trans);
    //foreach($trans as $key=>$value) $s=str_replace($key,$value,strftime($format, $timestamp));
    //echo 1;
		return strtr(strftime($format, $timestamp),$trans);    
    
    //return $s;
}
}  

?>