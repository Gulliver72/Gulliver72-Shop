<?php

  class Options {
  
    private $last_options_id = 1;
    private $options = array();
    private $option_array = array();
    private $language_id;
    private $renderedOptions;
    
    function __construct( $language_id = '') {
    
      if ($language_id == '') $language_id = $_SESSION['languages_id'];
      $this -> language_id = ( int )$language_id;
      $this -> init();
    } 
    
    private function init() {
    
      $last_query = go_db_query( "SELECT max(items_options_id) as max FROM " . DB_TBL_ITEMS_OPTIONS );
      $last = go_db_fetch_array( $last_query );
      $this -> last_options_id = $last['max'];
      $this -> option_array = $dd = $this -> getOptionsArray( $this -> language_id );
      $this -> options[] = array( "items_options_id" => "", "items_options_name" => "" );
      foreach ( $dd as $id => $text ) {
        $this -> options[] = array( "items_options_id" => $id, "items_options_name" => array_shift( $text ) );
      } 
//      $this -> render_option();
    }
    
    private function getOptionsArray( $language_id = null ) {
    
      $where = '';
      if ( !empty( $language_id ) ) $where = 'where language_id = ' . ( int )$language_id;
      $sql = go_db_query( "SELECT * FROM " . DB_TBL_ITEMS_OPTIONS . " {$where}  order by items_options_name" );
      $dd = array();
      while ( $row = go_db_fetch_array( $sql ) ) {
        if ( !empty( $row['items_options_name'] ) ) $dd[$row['items_options_id']][$row['language_id']] = go_db_prepare_output( $row['items_options_name'] );
      } 
      return $dd;
    }
    
    private function render_option() {
    
      $c = '<table class="table">';
//      $c .= $this -> table_header( array( "&nbsp;", "&nbsp;" ) );
      $options = $this -> getOptionsArray();
      foreach ( $options as $k => $v ) {
        $actions = '<br /><input type="submit" value="' . PICTURE_UPDATE . '"></form>&nbsp;&nbsp;';
        $actions .= '<form action="?action=delete_option" method="post">';
        $actions .= go_fetch_hidden_field( "id", $k ) . go_fetch_hidden_field( "opt_act", 1 );
        $actions .= '<input type="submit" value="' . PICTURE_DELETE . '"></form>';
        
        $c .= $this -> table_row( array( $this -> form_option( $k, $v, 'update_option' ), $actions ) );
      } 
      $last_id = $this -> last_options_id + 1;
      $actions = '<br /><br /><input type="submit" value="' . TEXT_ADD . '"></form>&nbsp;&nbsp;';
      $c .= $this -> table_row( array( $this -> form_option( $last_id, array(), 'add_option' ), $actions ) );
      $c .= "</table>";
      $this -> renderedOptions = $c;
    }
    
    private function form_option( $id, $options, $action ) {
    
      $languages = languages::ArrayBuilder()->get();
      $content = sprintf( '<form id="option_form_%s" method="post" action="?action=%s" >', $id, $action );
      for ( $i = 0; $i < count($languages); $i++ ) {
        $language_id = $languages[$i]['languages_id'];
        $text = isset( $options[$language_id] )?$options[$language_id]:"";
        $content .= "<br />" . go_picture( FOLDER_RELATIV_LANG_TEMPLATES . $languages[$i]['directory'] . '/picture/' . $languages[$i]['picture'], $languages[$i]['name'] ) . '&nbsp;';
        $content .= sprintf( '<input type="text"  name="option[%s]"  value="%s">', $language_id, $text );
      } 
      $content .= go_fetch_hidden_field( "id", $id ) . go_fetch_hidden_field( "opt_act", 1 );
      return $content;
    }
    
    public function getRenderedOptions() {
    
      return $this -> renderedOptions;
    }
    
    public function save_option( $id, $input ) {
    
      foreach ( $input as $k => $v ) {
        go_db_carry( DB_TBL_ITEMS_OPTIONS, array( "items_options_id" => ( int )$id, "language_id" => ( int )$k, "items_options_name" => go_db_producing_input( $v ) ) );
      } 
      $this -> getOptionsArray( $this -> language_id );
      $this -> render_option();
    }
    
    public function update_option( $id, $input ) {
    
      if ( $input == false ) {
        go_db_query( "Delete From '" . DB_TBL_ITEMS_OPTIONS . "' where items_options_id = '" . ( int )$id . "'" );
      } else {
        foreach ( $input as $k => $v ) {
          go_db_carry( DB_TBL_ITEMS_OPTIONS, array( "items_options_name" => go_db_producing_input( $v ) ), 'update', "items_options_id = '" . ( int )$id . "' and  language_id ='" . ( int )$k . "'" );
        } 
      } 
      $this -> getOptionsArray( $this -> language_id );
      $this -> render_option();
    }
    
    private function table_header( $data ) {
    
      $p = '<td>%s</td>';
      $c = '<tr class="bg-info">';
      foreach ( $data as $v ) {
        $c .= sprintf( $p, $v );
      } 
      $c .= "</tr>";
      return $c;
    }
    
    private function table_row( $data ) {
    
      $p = '<td class="col-md-6 text-center">%s</td>';
      $c = '<tr>';
      foreach ( $data as $v ) {
        $c .= sprintf( $p, $v );
      } 
      $c .= "</tr>";
      return $c;
    } 
    private function getOptionsName( $id ) {
    
      $sql = go_db_query( "SELECT * FROM " . DB_TBL_ITEMS_OPTIONS . " where items_options_id = '" . (int)$id . "' and language_id = '" . ( int )$_SESSION['languages_id'] . "'" );
      $dd = go_db_fetch_array( $sql );
      
      return $dd['items_options_name'];
    }
    public function getOptionById( $id = NULL ) {
    
      if (isset( $id )) {
        return $this->getOptionsName( $id );
      } else {
        return '';
      }
    }
  } 
?>