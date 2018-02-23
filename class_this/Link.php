<?php
  
  class Link {
  
    public static function hrefLink($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true, $secure = false, $admin = false) {
    
      global $request_type, $session_started, $SID;
      
      $seo = ( defined('SEO_URLS') ? SEO_URLS : false );
      $seo_rewrite_type = ( defined('SEO_URLS_TYPE') ? SEO_URLS_TYPE : false );
      $seo_pages = array(FILENAME_NAME_START, FILENAME_NAME_ITEM);
      if ( !in_array($page, $seo_pages) ) $seo = false;
      
      if (!go_not_null($page)) {
        die('<p><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!</p>');
      }
      
      if ($page == '/') $page = '';
      
      if ($connection == 'NONSSL') {
        $link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
        $seo_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
        $seo_rewrite_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
      } elseif ($connection == 'SSL') {
        if (ENABLE_SSL == true) {
          $link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
          $seo_link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
          $seo_rewrite_link = HTTPS_SERVER . FOLDER_RELATIV_HTTPS_CATALOG;
        } else {
          $link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
          $seo_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
          $seo_rewrite_link = HTTP_SERVER . FOLDER_RELATIV_HTTP_CATALOG;
        }
      } else {
        die('<p><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b></p>');
      }
      
      if (go_not_null($parameters)) {
        $link .= $page . '?' . go_output_string($parameters);      
        $separator = '&';
        # Start exploding the parameters to extract the values
        # Also, we could use analysis_str($parameters) and would probably be more clean
        if ($seo == 'true'){
          $p = explode('&', $parameters);
          krsort($p);
          $params = array();
          
          if ( $seo_rewrite_type == 'Rewrite' ) {
            foreach ($p as $index => $valuepair) {
              $p2 = explode('=', $valuepair);
              switch ($p2[0]) {
                case 'items_id':
                  $rewrite_item = true;
                  if ( defined('ITEM_NAME_'.$p2[1]) ) {
                    $rewrite_page_item = short_name(constant('ITEM_NAME_'.$p2[1])) . '-p-' . $p2[1] . '.html';
                  } else { 
                    $seo = false; 
                  }
                  break;
                case 'bigPfad': 
                  $rewrite_category = true;
                  if ( defined('CATEGORY_NAME_'.$p2[1]) ) {
                    $rewrite_page_category = short_name(constant('CATEGORY_NAME_'.$p2[1])) . '-c-' . $p2[1] . '.html';
                  } else { 
                    $seo = false;
                  }
                  break; 
                case 'producers_id': 
                  $rewrite_producer = true;
                  if ( defined('PRODUCER_NAME_'.$p2[1]) ) {
                    $rewrite_page_producer = short_name(constant('PRODUCER_NAME_'.$p2[1])) . '-m-' . $p2[1] . '.html';
                  } else { 
                    $seo = false;
                  }
                  break; 
                default:
                  $params[$p2[0]] = $p2[1]; 
                  break;
              } # switch
            } # end foreach
            
            $params_stripped = implode_assoc($params);
            switch (true) {
              case ( $rewrite_item && $rewrite_category ):
              case ( $rewrite_item ):
                $rewrite_page = $rewrite_page_item;
                $rewrite_category = false;
                break;
              case ( $rewrite_category ):
                $rewrite_page = $rewrite_page_category;
                break; 
              case ( $rewrite_producer ):
                $rewrite_page = $rewrite_page_producer;
                break; 
              default:
                $seo = false;
                break;
            } #end switch true  
            
            $seo_rewrite_link .= $rewrite_page . ( go_not_null($params_stripped) ? '?'.go_output_string($params_stripped) : '' ); 
            
            $separator = ( go_not_null($params_stripped) ? '&' : '?' );
            
          } else {
            foreach ($p as $index => $valuepair) {
              $p2 = explode('=', $valuepair);
              switch ($p2[0]) {
                case 'items_id':
                  if ( defined('ITEM_NAME_'.$p2[1]) ) {
                    $params['pName'] = constant('ITEM_NAME_'.$p2[1]);
                  } else { 
                    $seo = false; 
                  }
                  break;
                case 'bigPfad': 
                  if ( defined('CATEGORY_NAME_'.$p2[1]) ) {
                    $params['cName'] = constant('CATEGORY_NAME_'.$p2[1]);
                  } else { 
                    $seo = false;
                  }
                  break; 
                case 'producers_id': 
                  if ( defined('PRODUCER_NAME_'.$p2[1]) ) {
                    $params['mName'] = constant('PRODUCER_NAME_'.$p2[1]);
                  } else { 
                    $seo = false; 
                  }
                  break; 
                default:
                  $params[$p2[0]] = $p2[1]; 
                  break;
              } # switch
            } # end foreach 
            
            $params_stripped = implode_assoc($params);  
            $seo_link .= $page . '?'.go_output_string($params_stripped);   
            $separator = '&';
          } # end if/else
        } # end if $seo
      } else {
        $link .= $page;
        $separator = '?';
        $seo = false;
      } # end if(go_not_null($parameters)
      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1); 
        if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
          if (go_not_null($SID)) {
            $_sid = $SID;
          } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
            if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
              $_sid = go_session_name() . '=' . go_session_id();
          }
        }
      }
      if ( ('SEARCH_ENGINE_FRIENDLY_URLS' == 'true') && ($search_engine_safe == true) ) {
        while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);
        while (strstr($seo_link, '&&')) $seo_link = str_replace('&&', '&', $seo_link);
        $link = str_replace('?', '/', $link);
        $link = str_replace('&', '/', $link);
        $link = str_replace('=', '/', $link);
        $seo_link = str_replace('?', '/', $seo_link);
        $seo_link = str_replace('&', '/', $seo_link);
        $seo_link = str_replace('=', '/', $seo_link);
        $seo_rewrite_link = str_replace('?', '/', $seo_rewrite_link);
        $seo_rewrite_link = str_replace('&', '/', $seo_rewrite_link);
        $seo_rewrite_link = str_replace('=', '/', $seo_rewrite_link);
        $separator = '?';
      }
      if (isset($_sid)) {
        $link .= $separator . go_output_string($_sid);
        $seo_link .= $separator . go_output_string($_sid);
        $seo_rewrite_link .= $separator . go_output_string($_sid);
      }
      
      if ($secure === true) {
        $link .= $separator . go_output_string($_SESSION['csrf_token']);
        $seo_link .= $separator . go_output_string($_SESSION['csrf_token']);
        $seo_rewrite_link .= $separator . go_output_string($_SESSION['csrf_token']);
      }
      
      if ($seo == 'true') {
        return ($seo_rewrite_type == 'Rewrite' ? $seo_rewrite_link : $seo_link);
      } else {
        return $link;
      }
    }
    
    public static function hrefAdminLink($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    
      $this->hrefLink($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true, $secure = true, $admin = true) {
    }
    
    public static function pictureLink($src, $alt = '', $width = '', $height = '', $parameters = '') {
    
      if ( (empty($src) || ($src == FOLDER_RELATIV_PICTURES)) && (PICTURE_REQUIRED == 'false') ) {
        return false;
      }
      
      //////// if in the database a picture with "http://" from extern.
      $control_is_extern = $src;
      $pos1 = substr_count($control_is_extern, 'http://');
      $pos2 = substr_count($control_is_extern, 'https://');
      if ($pos1 > 1) {
        $control_is_extern = substr($control_is_extern, 7);
        $src = strstr($control_is_extern, 'http://');
      }
      elseif($pos1 > 0) {
        $src = strstr($control_is_extern, 'http://');
      }
      if ($pos2 > 1) {
        $control_is_extern = substr($control_is_extern, 8);
        $src = strstr($control_is_extern, 'https://');
      }
      elseif($pos2 > 0) {
        $src = strstr($control_is_extern, 'https://');
      }
      /////////////////// extern end
      
      $picture = '<img src="' . go_output_string($src) . '" alt="' . go_output_string($alt) . '"';
      if (go_not_null($alt)) {
        $picture .= ' title=" ' . go_output_string($alt) . ' "';
      } 
      
      global $binary_gateway;
      
      if ($binary_gateway == '') { 
        if ( (CONFIG_CALCULATE_PICTURE_SIZE == 'true') && (empty($width) || empty($height)) ) {
          if ($picture_size = @getimagesize($src)) {
            if (empty($width) && go_not_null($height)) {
              $ratio = $height / $picture_size[1];
              $width = $picture_size[0] * $ratio;
            } elseif (go_not_null($width) && empty($height)) {
              $ratio = $width / $picture_size[0];
              $height = $picture_size[1] * $ratio;
            } elseif (empty($width) && empty($height)) {
              $width = $picture_size[0];
              $height = $picture_size[1];
            }
          } elseif (PICTURE_REQUIRED == 'false') {
            return false;
          }
        } 
      } 
      if (go_not_null($width) && go_not_null($height)) {
        $picture .= ' width="' . go_output_string($width) . '" height="' . go_output_string($height) . '"';
      }
      if (go_not_null($parameters)) $picture .= ' ' . $parameters;
      $picture .= '>';
      
      return $picture;
    }

  }
?>