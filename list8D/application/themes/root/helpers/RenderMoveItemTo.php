<?php 

class List8D_Theme_Root_Helper_RenderMoveItemTo extends List8D_ViewHelper
{
  public function renderMoveItemTo($list=null,$item=null) {
    
    $itemsView = clone $this->view;
    if($list) {
      $itemsView->list = $list;
    }
    if($item) {
      $itemsView->item = $item;
    }
    
    return $itemsView->render("move-item-to.tpl.php");
    
  }
  
}