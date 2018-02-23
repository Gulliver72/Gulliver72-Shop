<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015  Bigware LTD
  
  $Id: DeliveryTime.php 4200 2016-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

  Class DeliveryTime {
  
    private $table = "info_delivery_time";
    public $last_id = 1;
    public $options = array();
    public $option_array = array();  
    public $language_id;
    
    function __construct($language_id = 5){
//      $this->language_id = (int)$language_id;
      $this->init();
    }
    
    private function init(){
        $this->option_array = $dd = $this->get_options($_SESSION['languages_id']);
        $this->options = array();
        $this->options[] = array("id"=>"","text"=>"");
        foreach (  $dd as $id=>$text){
          $this->options[] = array("id"=>$id, "text"=>array_shift($text));
        }
    }
    
    public function get_options($language_id = null){
 
      $where ='where language_id = '.(int)$_SESSION['languages_id'];
      $sql=go_db_query("SELECT * FROM {$this->table}  {$where}  order by Id");
      $dd = array();
      while ( $row=go_db_fetch_array($sql)){
        $this->last_id = $row['Id'];
        if (!empty($row['Text'])) $dd[$row['Id']][$row['language_id']] = go_db_prepare_output($row['Text']); 
      };
      return $dd;
    }
    
    public function find_option($item){
      if (empty($item['delivery_time_id'])){
        $item = $this->find_category_option($item['items_id']);
      }
      $id = $item['delivery_time_id'];  
      
      return !empty($this->option_array[$id])? array_shift( $this->option_array[$id] ):null; 
    }
    
    private function find_category_option($items_id){
         $items_query = go_db_query("select ic.items_id, c.delivery_time_id from " . DB_TBL_ITEMS_TO_CATEGORIES . " ic left join " . DB_TBL_CATEGORIES . " c on ic.categories_id = c.categories_id  where ic.items_id = ".$items_id); 
         $result = go_db_fetch_array($items_query);
         return $result;  
    }
    
    public function render_option(){
      $c  ='<table class="table">';
      $c .= $this->table_header(array("&nbsp;","&nbsp;"));
      $options = $this->get_options() ;
      foreach ($options as $k=>$v){
        $actions = '<br /><input type="submit" value="'.PICTURE_UPDATE.'"></form>&nbsp;&nbsp;';
        $actions .= '<form action="?action=delete_option" method="post">';
        $actions .=  go_fetch_hidden_field("id",$k).go_fetch_hidden_field("active",1);
        $actions .= '<input type="submit" value="'.PICTURE_DELETE.'"></form>';

        $c .= $this->table_row(array($this->form_option($k,$v,'update_option'), $actions));
      }
      $last_id = $this->last_id + 1;
      $actions = '<br /><input type="submit" value="'.TEXT_ADD.'"></form>&nbsp;&nbsp;';
      $c .= $this->table_row(array($this->form_option($last_id,array(),'add_option'), $actions));
      $c .= "</table>";
      return $c;
    }
    
    private function form_option($id, $options,$action){
      $languages = go_get_languages();
      $content = sprintf('<form id="option_form_%s" method="post" action="?action=%s" >', $id,$action);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $language_id =  $languages[$i]['id'];
        $text = isset($options[$language_id])?$options[$language_id]:"";
        $content .=  "<br />".go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $languages[$i]['directory'] . '/picture/' . $languages[$i]['picture'], $languages[$i]['name']) . '&nbsp;';
        $content .= sprintf('<input type="text"  name="option[%s]"  value="%s">',  $language_id, $text);
      } 
      $content .=  go_fetch_hidden_field("id",$id).go_fetch_hidden_field("active",1);
      return $content;
    }
    
    public function save_option($id,$input){
      foreach ($input as $k=>$v){
        go_db_carry($this->table, array("Id"=>(int)$id,"language_id"=>(int)$k,"Text"=>go_db_producing_input($v)));
        $this->init();
      }
    }
    
    public function update_option($id,$input){
      if ($input == false){
        go_db_query("Delete From {$this->table} where Id=".(int)$id );
        go_db_carry(DB_TBL_ITEMS,array("delivery_time_id"=>'null'),'update','delivery_time_id='.(int)$id);
        go_db_carry(DB_TBL_CATEGORIES, array("delivery_time_id"=>'null'),'update','delivery_time_id='.(int)$id);
      }else{
        foreach ($input as $k=>$v){
          go_db_carry($this->table, array("Text"=>go_db_producing_input($v)),'update','Id='.(int)$id.' and  language_id ='.(int)$k);
        }
      }
      $this->init();
    }
    
    public function category_option(){
      $text = '';
      $c = sprintf('<form id="option_form_%s">', 'category');
//      $c .= "<table cellpadding=3 cellspacing=5>";
      foreach ($this->go_get_category_tree() as $v){
        if (!isset($v['delivery_time_id'])) $v['delivery_time_id'] = '';
        if ($v['id'] > 0 ){
          $text = sprintf('<span style="margin-left:%spx;">%s</span>', substr_count($v['text'], '/',1)*40 , substr(strrchr($v['text'], "/"), 1));
        }else{
          if (isset($v['text']) && substr_count($v['text'], '/',1) == 0) $text = "/".$text;
        }
        $c .= '<p class="text-center"><label for="category[' . $v['id'] . ']">' . $text . '</label>&nbsp;&nbsp;&nbsp;' . go_fetch_pull_down_menu("category[{$v['id']}]",$this->options,$v['delivery_time_id']) . '</p>'; 
      }
//      $c .= "</table></form>";
      $c .='<br /><p class="text-center"><button id="update_category" class="option_action" data-id="category" >' . PICTURE_UPDATE . '</button></p></form>';
      return $c;
    }
    
    public function go_get_category_tree(){
      $cat = $this->get_category_tree();
      $cat[0]['text']='/';
      return array_values($cat);
    }
    
    public function get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
        if (!is_array($category_tree_array)) $category_tree_array = array();
        if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => '');
        $categories_query = go_db_query("select c.categories_id, cd.categories_name, c.parent_id ,c.delivery_time_id from " . DB_TBL_CATEGORIES . " c, " . DB_TBL_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
        while ($categories = go_db_fetch_array($categories_query)) {
          $spacing = $category_tree_array[$categories['parent_id']]['text'] . '/';
          if ($exclude != $categories['categories_id']) $category_tree_array[$categories['categories_id']] = array('id' => $categories['categories_id'], 'parent_id'=>$categories['parent_id'], 'text' => $spacing.$categories['categories_name'],'delivery_time_id'=>$categories['delivery_time_id']);
          $category_tree_array = $this->get_category_tree($categories['categories_id'], $spacing , $exclude, $category_tree_array);
        }
        return $category_tree_array;
    }
    
    public function update_category($data){
      unset($data[0]);
      foreach ($data as $k=>$v){
        $v = empty($v)? 'null':$v;
        go_db_carry(DB_TBL_CATEGORIES, array("delivery_time_id"=>$v),'update','categories_id ='.(int)$k);
      }
    }
    
    public function product_option(){
        $cate_tree = $this->go_get_category_tree();
        $cate_tree[0] =array("id"=>"","text"=> PULL_DOWN_DEFAULT);
//        $c = go_fetch_form('optionformproduct', 'delivery_time.php?action=add_product_option', 'id="option_form_product"', 'post');
        $c = '<form id="option_form_product" action="?action=add_product_option" method="post">';
        $c .= '<p class="text-center"><label for="category_id">' . TEXT_VALID_CATEGORIES_NAME . '</label>&nbsp;&nbsp;&nbsp;' . go_fetch_pull_down_menu('category_id' ,$cate_tree,'','required',true) . '</p>';
        $c .=  '<p class="text-center"><label for="item_id">' . TEXT_VALID_ITEMS_NAME . '</label>&nbsp;&nbsp;&nbsp;<span id="item_id_combo">' . go_fetch_pull_down_menu('item_id' ,array(),'','required',true) . '</span></p>';
        $c .=  '<p class="text-center"><label for="delivery_time_id">' . SITE_TITLE . '</label>&nbsp;&nbsp;&nbsp;' . go_fetch_pull_down_menu('delivery_time_id' ,$this->options,'','required',true) . go_fetch_hidden_field('active',3) . '</p>';
        $c .=  '<p class="text-center"><input type="submit"   value="'.TEXT_ADD.'" /></p>';
        $c .= "</form><br /><br />";
        $c .= $this->item_lists();
        $this->item_lists();
      return $c;
    }
    
    private function item_lists(){
        $c ='<table id="item_lists" class="table">';
        $c .= $this->table_header(array( TEXT_VALID_ITEMS_NAME, SITE_TITLE ));
        $items_query = go_db_query("select  p.items_id, p.items_model, pd.items_name, pd.items_name2, p.delivery_time_id from " . DB_TBL_ITEMS . " p, " . DB_TBL_ITEMS_DESCRIPTION . " pd where p.items_id = pd.items_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.items_id = pd.items_id and p.delivery_time_id > 0"); 
        while ($items = go_db_fetch_array($items_query)) {
          $c .= $this->table_row(array($items['items_name'],    go_fetch_pull_down_menu('delivery_time_id' ,$this->options,$items['delivery_time_id']).go_fetch_hidden_field('items_id',$items['items_id'])));
        }
        $c .= "</table>";
        return $c;
    }
    
    public function add_product_option($data){
      if (empty($data['delivery_time_id'] )) $data['delivery_time_id'] = 'null';
      go_db_carry(DB_TBL_ITEMS,array("delivery_time_id"=>$data['delivery_time_id']),'update','items_id='.(int)$data['items_id']);
    }
    
    public function get_category_item($category_id){
         $items_query = go_db_query("select p.items_id, pd.items_name from " . DB_TBL_ITEMS . " p left join " . DB_TBL_ITEMS_DESCRIPTION . " pd on p.items_id = pd.items_id where  p.delivery_time_id is null and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.items_id in ( select items_id from "  . DB_TBL_ITEMS_TO_CATEGORIES . " where categories_id = ".$category_id.") "); 
        $ddd = array();
        while ($items = go_db_fetch_array($items_query)) {
          $ddd[] = array("id"=>$items['items_id'], "text"=>$items['items_name' ]);
        }
        return $ddd;  
    }
    
    private function table_header($data){
      $p = '<td>%s</td>';
      $c = '<tr class="bg-info">';
      foreach ($data as $v) {
        $c .= sprintf($p,$v);
      }
      $c .= "</tr>";
      return $c;
    }
    
    private function table_row($data){
      $p = '<td>%s</td>';
      $c ='<tr>';
      foreach ($data as $v){
        $c .= sprintf($p,$v);
      }
      $c .= "</tr>";
      return $c;
    }
    
    public function shipping_tag($item){
      $delivery_time_option = $this->find_option($item);
      $tag = array('textDeliveryTime' => DELIVERY_TIME,
                   'textShippingInfo' => TEXT_SHIPPING_INFO,
                   'option' => $delivery_time_option,
                   'link' => go_href_link(FILENAME_NAME_SHIPPING)
                   );
      return $tag;
    }
  }
?>