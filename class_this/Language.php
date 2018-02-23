<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2016 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2016  Bigware LTD
  
  $Id: Language.php 4200 2017-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/
  class Language {
    var $languages, $catalog_languages, $browser_languages, $language;
    function __construct($lng = '') {
      $this->language($lng);
    }
    function language($lng) {
      $this->languages = array('ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
                               'bg' => 'bg|bulgarian',
                               'br' => 'pt[-_]br|brazilian portuguese',
                               'ca' => 'ca|catalan',
                               'cs' => 'cs|czech',
                               'da' => 'da|danish',
                               'de' => 'de([-_][[:alpha:]]{2})?|de',
                               'el' => 'el|greek',
                               'en' => 'en([-_][[:alpha:]]{2})?|english',
                               'es' => 'es([-_][[:alpha:]]{2})?|spanish',
                               'et' => 'et|estonian',
                               'fi' => 'fi|finnish',
                               'fr' => 'fr([-_][[:alpha:]]{2})?|french',
                               'gl' => 'gl|galician',
                               'he' => 'he|hebrew',
                               'hu' => 'hu|hungarian',
                               'id' => 'id|indonesian',
                               'it' => 'it|italian',
                               'ja' => 'ja|japanese',
                               'ko' => 'ko|korean',
                               'ka' => 'ka|georgian',
                               'lt' => 'lt|lithuanian',
                               'lv' => 'lv|latvian',
                               'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
                               'no' => 'no|norwegian',
                               'pl' => 'pl|polish',
                               'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
                               'ro' => 'ro|romanian',
                               'ru' => 'ru|russian',
                               'sk' => 'sk|slovak',
                               'sr' => 'sr|serbian',
                               'sv' => 'sv|swedish',
                               'th' => 'th|thai',
                               'tr' => 'tr|turkish',
                               'uk' => 'uk|ukrainian',
                               'tw' => 'zh[-_]tw|chinese traditional',
                               'zh' => 'zh|chinese simplified');
      $this->catalog_languages = array();
      $languages_query = go_db_query("select languages_id, name, code, picture, directory from " . DB_TBL_LANGUAGES . " order by sort_order");
      while ($languages = go_db_fetch_array($languages_query)) {
        $this->catalog_languages[$languages['code']] = array('id' => $languages['languages_id'],
                                                             'name' => $languages['name'],
                                                             'picture' => $languages['picture'],
                                                             'directory' => $languages['directory']);
      }
      $this->browser_languages = '';
      $this->language = '';
      $this->set_language($lng);
    }
    function set_language($language) {
      if ( (go_not_null($language)) && (isset($this->catalog_languages[$language])) ) {
        $this->language = $this->catalog_languages[$language];
      } else {
        $this->language = $this->catalog_languages[DEFAULT_LANGUAGE];
      }
    }
    function get_browser_language() {
      $this->browser_languages = explode(',', getenv('HTTP_ACCEPT_LANGUAGE'));
      for ($i=0, $n=sizeof($this->browser_languages); $i<$n; $i++) {
        reset($this->languages);
        while (list($key, $value) = each($this->languages)) {
          if (preg_match('/^(' . $value . ')(;q=[0-9]\\\\.[0-9])?$/i', $this->browser_languages[$i]) && isset($this->catalog_languages[$key])) {
            $this->language = $this->catalog_languages[$key];
            break 2;
          }
        }
      }
    }
  }
?>
