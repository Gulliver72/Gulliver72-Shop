<?php
  class Tree {
  
  public $tree;
  public $activeCat;
  private $topnode;
  private $dataSet;
  
  
  public function __construct($data, $activeCat = '') {
    $this->topnode = new Node(null, 0, "topnode", (int)-1);
    $this->dataSet = $data;
    $this->activeCat = $activeCat;
    $this->createTree($this->topnode);
    $this->currentTree($this->topnode);
  }
  
  private function createTree($node) {
    foreach($this->dataSet as $key => $value) {
      if($value['parent_id'] == $node->categories_id) {
        if ($value['parent_id'] == 0) {
          $level = 0;
        } else {
          $level = $node->level+1;
        }
        $node->addChild(new Node($value['parent_id'], $value['categories_id'], '', $level), $value['categories_id']);
        unset($this->dataSet[$key]);
      }
    }
    foreach($node->children as &$c) {
      $this->createTree($c);
    }
  }

  private function currentTree($node) {
    $this->tree = array();
    for($i = 0; $i < count($node->children); $i++) {
      if ( isset($node->children) ) $this->tree[] = $node->children;
    }
  }
  
  public function getNodeById($categories_id, $node = null) {
    $node = $node == null ? $this->topnode : $node;
    if($node->categories_id == $categories_id) {
      return $node;
    }
    else {
      foreach($node->children as $c) {
        $res = &$this->getNodeById($categories_id, $c);
        if($res != null) {
          return $res;
        }
      }
    }
  }
  
  public function getTree() {
    return $this->tree;
  }
}
?>