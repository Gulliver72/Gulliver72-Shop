<?php
  class Values {
  
    private $valuestable = "items_options_values";
    public $last_values_id;
    public $options_values = array();
    public $option_values_array = array();
    public $language_id;

    function __construct($language_id = 5){
//      $_SESSION['languages_id'] = (int)$language_id;
      $this->init();
    }
    
    private function init(){
        $last_query = go_db_query("SELECT max(items_options_values_id) as max FROM {$this->valuestable}");
        $last = go_db_fetch_array($last_query);
        $this->last_values_id = $last['max'];
        $this->option_values_array = $ddv = $this->get_options_values($_SESSION['languages_id']);
        $this->options_values = array();
        $this->options_values[] = array("items_options_values_id"=>"","items_options_values_name"=>"");
        foreach (  $ddv as $vid=>$vtext){
          $this->options[] = array("items_options_values_id"=>$vid, "items_options_values_name"=>array_shift($vtext));
        }
    }
    
    public function get_options_values($language_id = null){
      $where = '';
      if (!empty($language_id)) $where = 'where language_id = ' . (int)$language_id;
        $sql = go_db_query("SELECT * FROM {$this->valuestable}  {$where}  order by items_options_values_name");
        $ddv = array();
        while ( $row = go_db_fetch_array($sql)){
//            $this->last_values_id = $row['items_options_values_id'];
            if (!empty($row['items_options_values_name'])) $ddv[$row['items_options_values_id']][$row['language_id']] = go_db_prepare_output($row['items_options_values_name']); 
        };
      return $ddv;
    }
    
    public function render_value(){
      $c  ='<table class="table">';
//      $c .= $this->table_header(array("&nbsp;","&nbsp;"));
      $values = $this->get_options_values();
      foreach ($values as $k=>$v){
        $actions = '<br /><input type="submit" value="'.PICTURE_UPDATE.'"></form>&nbsp;&nbsp;';
        $actions .= '<form action="?action=delete_value" method="post">';
        $actions .=  go_fetch_hidden_field("id",$k).go_fetch_hidden_field("active",1);
        $actions .= '<input type="submit" value="'.PICTURE_DELETE.'"></form>';

        $c .= $this->table_row(array($this->form_value($k,$v,'update_value'), $actions));
      }
      $last_id = $this->last_values_id + 1;
      $actions = '<br /><br /><input type="submit" value="'.TEXT_ADD.'"></form>&nbsp;&nbsp;';
      $c .= $this->table_row(array($this->form_value($last_id,array(),'add_value'), $actions));
      $c .= "</table>";
      return $c;
    }
    
    private function form_value($id, $values,$action){
      $languages = go_get_languages();
      $content = sprintf('<form id="value_form_%s" method="post" action="?action=%s" >', $id,$action);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $language_id =  $languages[$i]['id'];
        $text = isset($values[$language_id])?$values[$language_id]:"";
        $content .=  "<br />".go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $languages[$i]['directory'] . '/picture/' . $languages[$i]['picture'], $languages[$i]['name']) . '&nbsp;';
        $content .= sprintf('<input type="text"  name="value[%s]"  value="%s">',  $language_id, $text);
      } 
      $content .=  go_fetch_hidden_field("id",$id).go_fetch_hidden_field("active",1);
      return $content;
    }
    
    public function save_value($id,$input){
      foreach ($input as $k=>$v){
        go_db_carry($this->valuestable, array("items_options_values_id"=>(int)$id,"language_id"=>(int)$k,"items_options_values_name"=>go_db_producing_input($v)));
//        $this->init();
      }
      go_forward(go_href_link(NAME_OF_FILE_CHARACTERISTICS));
    }
    
    public function update_value($id,$input){
      if ($input == false){
        go_db_query("Delete From {$this->valuestable} where items_options_values_id = '".(int)$id."'");
      }else{
        foreach ($input as $k=>$v){
          go_db_carry($this->valuestable, array("items_options_values_name"=>go_db_producing_input($v)),'update',"items_options_values_id = '".(int)$id."' and  language_id ='".(int)$k."'");
        }
      }
      go_forward(go_href_link(NAME_OF_FILE_CHARACTERISTICS));
    }
    
    public function delete_value($id,$input){
      if ($input == false){
        go_db_query("Delete From {$this->valuestable} where items_options_values_id = '".(int)$id."'");
      }
      go_forward(go_href_link(NAME_OF_FILE_CHARACTERISTICS));
    }
    private function table_header($data){
      $p = '<td class="text-center">%s</td>';
      $c = '<tr class="bg-info">';
      foreach ($data as $v) {
        $c .= sprintf($p,$v);
      }
      $c .= "</tr>";
      return $c;
    }
    
    private function table_row($data){
      $p = '<td class="col-md-6 text-center">%s</td>';
      $c ='<tr>';
      foreach ($data as $v) {
        $c .= sprintf($p,$v);
      }
      $c .= "</tr>";
      return $c;
    }
    private function getValuesName( $id ) {
    
      $sql = go_db_query( "SELECT * FROM {$this->valuestable} where items_options_values_id = '" . (int)$id . "' and language_id = '" . ( int )$_SESSION['languages_id'] . "'" );
      $dd = go_db_fetch_array( $sql );
      
      return $dd['items_options_values_name'];
    }
    public function getValuesById( $id = NULL ) {
    
      if (isset( $id )) {
        return $this->getValuesName( $id );
      } else {
        return '';
      }
    }
  }
?>