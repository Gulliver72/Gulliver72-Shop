<?php
  Class SearchSelectBox {
    private $table = "searchselectbox";
    private $optionTable = "searchselect";
    public $last_id = 1;
    public $options = array();
    public $option_array = array();  
    public $language_id;
    function __construct($language_id){
      $this->language_id = (int)$language_id;
    //  $this->reposition();
      $this->reclaibration();
      $this->init();
    }
    private function reposition(){
       $query = go_db_query("select distinct(position_id) as pos From {$this->table} order by position_id");
       $pos = array();
       while ( $row=go_db_fetch_array($query)){
          $pos[] = $row['pos'];
       }
       asort($pos);
       foreach ($pos as $k=>$v){
        go_db_carry($this->table,array( "position_id"=>$k+1),'update',"position_id='".$v."'");
       }
    }
    private function reclaibration(){
            $languages_query = go_db_query("select languages_id from " . DB_TBL_LANGUAGES . " where code='".DEFAULT_LANGUAGE."'");
            $language = go_db_fetch_array($languages_query);
       $sql = go_db_query("select * From {$this->table} where language_id=0");
       while ( $row=go_db_fetch_array($sql)){
          $context = $this->catchOption($row['searchselectbox_id'] );
        go_db_carry($this->table, array("language_id"=>$language['languages_id'], "position_id"=>$this->find_max_pos() + 1 , "optionText"=>$context),'update',"searchselectbox_id=".$row['searchselectbox_id'] );
       }   
      
    }
    private function catchOption($id){
      $context ="";
        $query2=go_db_query("SELECT * FROM  {$this->optionTable}  where selectbox = '".$id."'");
        while ( $row2=go_db_fetch_array($query2)){
           if (!empty($context)) $context .= "\n";
           $context .= $row2['searchtext'];
        }
       return $context;
    }
    private function init(){
        $this->option_array = $dd = $this->get_options($this->language_id);
        $this->options = array();
        //$this->options[] = array("id"=>"","text"=>"");
        foreach (  $dd as $id=>$text){
          $this->options[] = array("id"=>$id, "text"=>array_shift($text));
        }
    }
    public function get_options($language_id = null){
        $sql = go_db_query("SELECT * FROM {$this->table}  order by position_id");
        $dd = array();
        while ( $row = go_db_fetch_array($sql)){
            $this->last_id = $row['searchselectbox_id'];
            $dd[$row['position_id']][$row['language_id']] = $row; 
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
    public function render_option(){
      $c  ='<table style="width: 100%;">';
      $c .= $this->table_header(array('',TABLE_HEADING_SEARCHSELECTBOX_LIMIT,TABLE_HEADING_ACTION,''));
      $options = $this->get_options() ;
      $row = 1;
      foreach ($options as $k=>$v) {
        if (!isset($v) || !isset($v['searchselectbox_id'])) $v['searchselectbox_id'] = '';
        $actions = '<input type="submit" value="'.PICTURE_UPDATE.'"></form>&nbsp;&nbsp;';
        $actions .= '<form action="?action=delete_option" method="post">';
        $actions .=  go_fetch_hidden_field("id",$v['searchselectbox_id']).go_fetch_hidden_field("position_id",$k).go_fetch_hidden_field("active",1);
        $actions .= '<input type="submit" value="'.PICTURE_DELETE.'"></form>';
        $actions .= $this->position_selector($k);

        $c .= $this->table_row(array($k, $this->form_option_name($k,$v,'update_option'), $this->form_option($k,$v), $actions));
        $row++;
      }
      $last_id = $this->last_id + 1;
      $actions = '<input type="submit" value="'.IMAGE_NEW_SEARCHSELECTBOX.'"></form>&nbsp;&nbsp;';
      $c .= $this->table_row(array($row, $this->form_option_name($last_id,array('searchselectbox_id' => ''),'add_option'), $this->form_option($last_id,array('searchselectbox_id' => '')), $actions));
      $c .= "</table>";
      return $c;
    }
    private function form_option_name($position_id, $options, $action){
      $languages = go_get_languages();
      $content = sprintf('<form id="option_form_%s" method="post" action="?action=%s" >', $options['searchselectbox_id'], $action);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $language_id =  $languages[$i]['id'];
        $title = "";
        if ( isset($options[$language_id])){
          $title = $options[$language_id]['selectboxtext'];
        }   
        $content .=  "<br />".go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $languages[$i]['directory'] . '/picture/' . $languages[$i]['picture'], $languages[$i]['name']) . '&nbsp;';
        $content .= '<ul>';
        $content .= sprintf('<li style="list-style-type:none"><input type="text"  name="option[%s]"  value="%s"></li>',  $language_id, $title);
        $content .= "</ul>";
      } 
      $content .=  go_fetch_hidden_field("position_id",$position_id).go_fetch_hidden_field("active",1);
      return $content;
    }
    private function form_option($position_id, $options){
      $languages = go_get_languages();
      $content = '';
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $language_id =  $languages[$i]['id'];
        $context = "";
        if ( isset($options[$language_id])){
          $context = $options[$language_id]['optionText'];
        }   
        $content .=  "<br />".go_picture(FOLDER_RELATIV_LANG_TEMPLATES . $languages[$i]['directory'] . '/picture/' . $languages[$i]['picture'], $languages[$i]['name']) . '&nbsp;';
        $content .= '<ul>';
        $content .= sprintf('<li style="list-style-type:none"><textarea  name="context[%s]">%s</textarea></li>',  $language_id, $context);
        $content .= "</ul>";
      } 
      $content .=  go_fetch_hidden_field("position_id",$position_id).go_fetch_hidden_field("active",1);
      return $content;
    }
    public function save_option($position_id,$option,$context){
        $position = go_db_fetch_array(go_db_query("select MAX(position_id) as maxpos from {$this->table}"));
      foreach ($option as $k=>$v){
        go_db_carry($this->table,array("language_id"=>$k,"selectboxtext"=>$v, "optionText"=>$context[$k], "position_id"=>$position['maxpos']+1));
        //go_db_carry($this->table, array("Id"=>(int)$id,"language_id"=>(int)$k,"Text"=>go_db_producing_input($v)));
      }
      $this->init();
    }
    public function update_option($position_id,$option,$context){
      go_db_query("Delete From {$this->table} where position_id=".(int)$position_id );
      foreach ($option as $k=>$v){
        go_db_carry($this->table,array("language_id"=>$k,"selectboxtext"=>$v, "optionText"=>$context[$k], "position_id"=>$position_id));
      }  
      $this->init();
    }
    public function delete_option($position_id){
      go_db_query("Delete From {$this->table} where position_id=".(int)$position_id );
      $this->reposition();
    }
    public function move_option($position_id,$moveTo){
      go_db_carry($this->table,array("position_id"=>99),'update',"position_id='".(int)$position_id."'");
      if ( $position_id < $moveTo) {
        $start = $position_id;
        $end = $moveTo;
        go_db_query("Update {$this->table}  set position_id = position_id - 1  where position_id  between {$start} and {$end}" );
      }elseif ( $position_id > $moveTo) {
        $start = $moveTo;
        $end = $position_id ;
        go_db_query("Update {$this->table}  set position_id = position_id +1  where position_id  between {$start} and {$end}" );
      }  
      go_db_carry($this->table,array("position_id"=>$moveTo),'update',"position_id='99'");
      $this->reposition();
    }
    private function table_header($data){
      $p = '<th>%s</th>';
                            $c = '<tr>';
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
    private function position_selector($position_id){
        $positions = array();
        $output = '<form action="?action=move_option" method="post">';
        for ($i=1; $i<=$this->find_max_pos();$i++){
          $positions[] = array("id"=>$i, "text"=>$i);
        }
        $output .= "<br />".TEXT_MOVE_TO.":".go_fetch_pull_down_menu("moveTo", $positions,$position_id,  'onChange="this.form.submit();"');
        $output .=  go_fetch_hidden_field("position_id",$position_id)."</form>";
        return $output;
    }
    private function find_max_pos(){
        $position = go_db_fetch_array(go_db_query("select MAX(position_id) as maxpos from {$this->table}"));
        return $position['maxpos'];
    }
  }
?>
