<?php
class Node {
  
  public $categories_name;
  public $categories_id;
  public $parent_id;
  public $level;
  public $children;
  
  
  public function __construct($parent_id, $categories_id, $categories_name, $level) {
    $this->parent_id = $parent_id;
    $this->level = $level;
    $this->categories_id = $categories_id;
    $this->categories_name = $categories_name;
    $this->children = array();
  }
  
  public function addChild($node, $categories_id) {
    $this->children[$categories_id] = $node;
  }
  
  public function deleteChild($i) {
    if(exists($this->children[$i])) {
      unset($this->children[$i]);
    }
  }
}
?>