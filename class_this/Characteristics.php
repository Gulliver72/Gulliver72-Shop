<?php
/*
###################################################################################
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015  Bigware LTD
  
  $Id: Characteristics.php 4200 2016-12-23 19:47:11Z Gulliver72 $
  
  Released under the GNU General Public License
 ##################################################################################
*/

  Class Characteristics {
    /* table for characteristics */
    private $characteristicstable = DB_TBL_ITEMS_CHARACTERISTICS;
    /* last characteristics id */
    public $last_id = 1;
    /* array with options id`s */
    private $options_array;
    /* array with options values id`s */
    private $values_array;
    /* array with prefix '+' and '-' */
    private $price_prefix;
    public $language_id;
    public $characteristics;
    

    function __construct($language_id){
      $this->language_id = (int)$language_id;
      $this->options_array = $this->get_options_array();
      $this->values_array = $this->get_values_array();
      $this->price_prefix = array(array('id'=>'+', 'text'=>'+'),array('id'=>'-', 'text'=>'-'));
      $this->characteristics = array(array('options_id'=>'0', 'options_values_id'=>'0', 'options_values_price'=>'', 'price_prefix'=>'+','attributs_sort_order'=>'','qty'=>'0'));

    }

    public function getPrefix() {
    
      return $this -> price_prefix;
    }
    
    public function getOptionsArray() {
    
      return $this -> options_array;
    }
    
    public function getValuesArray() {
    
      return $this -> values_array;
    }
    public function category_option(){
      $c = sprintf('<form id="option_form_%s">', 'category');
      $c .= '<table class="table">';
      foreach ($this->go_get_category_tree() as $v){
        if ($v['id'] > 0 ){
          $text = sprintf('<span style="margin-left:%spx;">%s</span>', substr_count($v['text'], '/',1)*40 , substr(strrchr($v['text'], "/"), 1));
        }else{
          if (substr_count($v['text'], '/',1) == 0) $text = "/".$text;
        }
        $c .= "<tr><td>".$text.'</td><td>'.go_fetch_pull_down_menu("category[{$v['id']}]",$this->options_array,$v['delivery_time_id']).'</td></tr>'; 
      }
      $c .= "</table></form>";
      $c .='<br /><hr /><button id="update_category" class="option_action" data-id="category" >'.PICTURE_UPDATE.'</button>';
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
        $categories_query = go_db_query("select c.categories_id, cd.categories_name, c.parent_id ,c.delivery_time_id from " . DB_TBL_CATEGORIES . " c, " . DB_TBL_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $this->language_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
        while ($categories = go_db_fetch_array($categories_query)) {
          $spacing = $category_tree_array[$categories['parent_id']]['text'] . '/';
          if ($exclude != $categories['categories_id']) $category_tree_array[$categories['categories_id']] = array('id' => $categories['categories_id'], 'parent_id'=>$categories['parent_id'], 'text' => $spacing.go_db_html_entity_decode(go_db_prepare_output($categories['categories_name'])),'delivery_time_id'=>$categories['delivery_time_id']);
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
        $c = '<form id="option_form_product" action="?action=add_product_option" method="post"><p class="col-md-12 text-center">'.TEXT_VALID_CATEGORIES_NAME.'&nbsp;&nbsp;'.go_fetch_pull_down_menu('category_id' ,$cate_tree,'','required',true).'</p>';
        $c .=  '<p class="col-md-12 text-center">'.TEXT_VALID_ITEMS_NAME.'&nbsp;&nbsp;<span id="item_id_combo">'.go_fetch_pull_down_menu('item_id' ,array(array('id'=>0, 'text'=>'')),'','required',true).'</span>'.go_fetch_hidden_field('active',3).'</p>';
        $c .= '</form><p>&nbsp;</p><table class="table"><tr><td>';
        $c .= $this->item_lists();
        $c .= '</td></tr>';
        $c .= '</table>';
//        $this->item_lists();
      return $c;
    }
    public function item_lists(){
          $c ='<form id="characteristics_form" action="?action=add_characteristics" method="post"><table id="item_lists" border="0" width="100%" cellspacing="0" cellpadding="5">';
          $c .= $this->table_header(array( OPTIONS_NAME, VALUES_NAME, OPTIONS_VALUES_PRICE, PRICE_PREFIX, OPTIONS_VALUES_SORT, TEXT_QTY, '&nbsp;'));
          if (count($this->characteristics) == 1) {
            $button_txt = TEXT_ADD;
          } else {
            $button_txt = PICTURE_UPDATE;
          }
          foreach ($this->characteristics as $value) {
            $actions = '<input type="submit" value="'.$button_txt.'"></form>&nbsp;&nbsp;';
            $actions .= count($this->characteristics) > 1?'<form action="?action=delete_characteristics" method="post">' . 
                                                          go_fetch_hidden_field("char_id",$value['characteristics_id']) .
                                                          go_fetch_hidden_field('i_id', $value['items_id']) . 
                                                          '<input type="submit" value="'.PICTURE_DELETE.'"></form>':'';
            $c .= $this->table_row(array(go_fetch_pull_down_menu('options_id', $this->options_array,$value['options_id']),
                                         go_fetch_pull_down_menu('options_values_id', $this->values_array,$value['options_values_id']),
                                         go_fetch_inputfeld('options_values_price', $value['options_values_price']),
                                         go_fetch_pull_down_menu('price_prefix', $this->price_prefix, $value['price_prefix']),
                                         go_fetch_inputfeld('attributs_sort_order', $value['attributs_sort_order']),
                                         go_fetch_inputfeld('qty', $value['qty']),
                                         go_fetch_hidden_field('charac_id', isset($value['characteristics_id']) ? $value['characteristics_id'] : '') . 
                                         go_fetch_hidden_field('iid', isset($value['items_id']) ? $value['items_id'] : '') . $actions
                                         )); 
          }
          if (count($this->characteristics) > 1) $c .= $this->table_row(array('<form action="?action=add_characteristics" method="post">' . go_fetch_pull_down_menu('options_id' ,$this->options_array,'0'), go_fetch_pull_down_menu('options_values_id' ,$this->values_array,'0'), go_fetch_inputfeld('options_values_price', ''), go_fetch_pull_down_menu('price_prefix' ,$this->price_prefix, '+'), go_fetch_inputfeld('attributs_sort_order',''), go_fetch_inputfeld('qty', ''),go_fetch_hidden_field('item', $value['items_id']) . '<input type="submit" value="'.TEXT_ADD.'"></form>'));
          $c .= "</table>";
        return $c;
    }
/*    public function render_characteristics(){
      $c  ='<table border="0" width="100%" cellspacing="0" cellpadding="5">';
      $c .= $this->table_header(array("&nbsp;","&nbsp;"));
      $options = $this->get_options();
      foreach ($options as $k=>$v){
        $actions = '<input type="submit" value="'.PICTURE_UPDATE.'"></form>&nbsp;&nbsp;';
        $actions .= '<form action="?action=delete_option" method="post">';
        $actions .=  go_fetch_hidden_field("id",$k).go_fetch_hidden_field("opt_act",1);
        $actions .= '<input type="submit" value="'.PICTURE_DELETE.'"></form>';

        $c .= $this->table_row(array($this->form_option($k,$v,'update_option'), $actions));
      }
      $last_id = $this->last_options_id + 1;
      $actions = '<input type="submit" value="'.TEXT_ADD.'"></form>&nbsp;&nbsp;';
      $c .= $this->table_row(array($this->form_option($last_id,array(),'add_option'), $actions));
      $c .= "</table>";
      return $c;
    } */
    public function add_characteristics($data){
        if (isset($data) && is_array($data)) {
          $data_array = array('items_id' => (int)$data["items_id"],
                              'options_id' => (int)$data["options_id"],
                              'options_values_id' => (int)$data["options_values_id"],
                              'options_values_price' => go_db_producing_input($data["options_values_price"]),
                              'price_prefix' => $data["price_prefix"],
                              'attributs_sort_order' => go_db_producing_input($data["attributs_sort_order"]),
                              'qty' => go_db_producing_input($data["qty"]));

          if (isset($data['charac_id']) && $data['charac_id'] != '') {
            go_db_carry(DB_TBL_ITEMS_CHARACTERISTICS,$data_array,'update',$data['charac_id']);
          } else {
            go_db_carry(DB_TBL_ITEMS_CHARACTERISTICS,$data_array,'insert');
          }
        }
    }
    public function delete_characteristics($data){
        if (isset($data) && $data != '') {
          go_db_query('delete from ' . DB_TBL_ITEMS_CHARACTERISTICS . ' where items_characteristics_id="' . (int)$data . '"');
        }
    }
    public function get_category_item($category_id){
        if (!empty($category_id)) {
          $items_query = go_db_query("select p.items_id, pd.items_name from " . DB_TBL_ITEMS . " p left join " . DB_TBL_ITEMS_DESCRIPTION . " pd on p.items_id = pd.items_id where pd.language_id = '" . $this->language_id . "' and p.items_id in ( select items_id from "  . DB_TBL_ITEMS_TO_CATEGORIES . " where categories_id = ".$category_id.") "); 
          $ddd = array();
          while ($items = go_db_fetch_array($items_query)) {
            $ddd[] = array("id"=>$items['items_id'], "text"=>go_db_html_entity_decode(go_db_prepare_output($items['items_name' ])));
          }
        } else {
          $ddd = array();
        }
        return $ddd;  
    }
    public function get_characteristics_item($item_id){
        $cd ='<table id="item_lists" border="0" width="100%" cellspacing="0" cellpadding="5">';
        $cd .= $this->table_header(array( OPTIONS_NAME, VALUES_NAME, OPTIONS_VALUES_PRICE, PRICE_PREFIX, OPTIONS_VALUES_SORT, TEXT_QTY));
        foreach ($this->characteristics as $value) {
          $cd .= $this->table_row(array(go_fetch_pull_down_menu('options_id', $this->options_array,$value['options_id']),
                                        go_fetch_pull_down_menu('options_values_id', $this->values_array,$value['options_values_id']),
                                        go_fetch_inputfeld('options_values_price', $value['options_values_price']),
                                        go_fetch_pull_down_menu('price_prefix', $this->price_prefix, $value['price_prefix']),
                                        go_fetch_inputfeld('attributs_sort_order', $value['attributs_sort_order']),
                                        '<input type="submit" value="'.TEXT_ADD.'">'));
        }
        $cd .= "</table>";
        return $cd;  
    }
    public function get_characteristics($item_id){
        $characteristics = array();
        if (isset($item_id)) {
          $items_characteristics_count_query = go_db_query("select count(*) as total from " . DB_TBL_ITEMS_CHARACTERISTICS . " where items_id = '" . $item_id . "'"); 
          $items_characteristics_count = go_db_fetch_array($items_characteristics_count_query);
          if ($items_characteristics_count['total'] != 0) {
            $items_characteristics_query = go_db_query("select items_id, items_characteristics_id, options_id, options_values_id, options_values_price, price_prefix, attributs_sort_order, qty from " . DB_TBL_ITEMS_CHARACTERISTICS . " where items_id = '" . $item_id . "' order by attributs_sort_order"); 
            while ($items_characteristics = go_db_fetch_array($items_characteristics_query)) {
              $characteristics[] = array('items_id'=>$items_characteristics['items_id'], 'characteristics_id'=>$items_characteristics['items_characteristics_id'], 'options_id'=>$items_characteristics['options_id'], 'options_values_id'=>$items_characteristics['options_values_id'], 'options_values_price'=>$items_characteristics['options_values_price'], 'price_prefix'=>$items_characteristics['price_prefix'], 'attributs_sort_order'=>$items_characteristics['attributs_sort_order'], 'qty'=>$items_characteristics['qty']);
            }
          } else {
            $characteristics[] = array('options_id'=>'0', 'options_values_id'=>'0', 'options_values_price'=>'', 'price_prefix'=>'+','attributs_sort_order'=>'','qty'=>'');
          }
        } else {
          $characteristics[] = array('options_id'=>'0', 'options_values_id'=>'0', 'options_values_price'=>'', 'price_prefix'=>'+','attributs_sort_order'=>'','qty'=>'');
        }
        $this->characteristics = $characteristics;
        return $characteristics;
    }
    public function get_options_array(){
        $items_options_query = go_db_query("select items_options_id, items_options_name from " . DB_TBL_ITEMS_OPTIONS . " where language_id = '" . $this->language_id . "'"); 
        $cco = array(array("id"=>'0', "text"=>TEXT_NO_VALUE));
        while ($items_options = go_db_fetch_array($items_options_query)) {
            $cco[] = array("id"=>$items_options['items_options_id'], "text"=>go_db_html_entity_decode(go_db_prepare_output($items_options['items_options_name' ])));
        }
        return $cco;  
    }
    public function get_values_array(){
        $items_options_values_query = go_db_query("select items_options_values_id, items_options_values_name from " . DB_TBL_ITEMS_OPTIONS_VALUES . " where language_id = '" . $this->language_id . "'"); 
        $ccv = array(array("id"=>'0', "text"=>TEXT_NO_VALUE));
        while ($items_options_values = go_db_fetch_array($items_options_values_query)) {
            $ccv[] = array("id"=>$items_options_values['items_options_values_id'], "text"=>go_db_html_entity_decode(go_db_prepare_output($items_options_values['items_options_values_name' ])));
        }
        return $ccv;  
    }
    private function table_header($data){
      $p = '<td class="tbUPDataInside">%s</td>';
                            $c = '<tr class="tbUPCountData">';
                            foreach ($data as $v){
                                $c .= sprintf($p,$v);
                            }
                            $c .= "</tr>";
                            return $c;
    }
    private function table_row($data){
       $p = '<td class="TbInsideData">%s</td>';
              $c ='<tr class="table_row tbCountData"  >';
                            foreach ($data as $v){
                                $c .= sprintf($p,$v);
                            }
                            $c .= "</tr>";
                            return $c;
    }
  }
?>
