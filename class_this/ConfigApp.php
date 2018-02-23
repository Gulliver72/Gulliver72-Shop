<?php
  class ConfigApp {
  
    private $data; // obj
    public $configs; // obj
  
    function __construct() {
    
//      $this->configs = new stdClass();
      $this->data = $this->getConfig();
//      $this->prepareConfig();
    }
    
    private function getConfig() {
    
      $this->data = set_it_up::ObjectBuilder()->get();
    }
    
    private function prepareConfig() {
    
      $data = $this->data;
      
      for ($i = 0; $i < count($data); $i++) {
        $this->configs->$data[$i]['set_it_up_key'] = new stdClass($data[$i]);
      }
    }
  
  }
?>