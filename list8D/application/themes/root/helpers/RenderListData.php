<?php

class List8D_Theme_Root_Helper_RenderListData extends List8D_ViewHelper {
	
    
    public function renderListData($list,$options=array()) {
    
			$listDataValues = $list->getData();
			$listDataTitles = $list->getDataTitle();

			$output = array();
			
			foreach($listDataTitles as $key => $value) {
				$output[$value] = $listDataValues[$key];
			}
			
			return $this->view->dl($output);
    
    }
    
}