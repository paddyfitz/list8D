<?php 

class List8D_Model_DbTable_Item extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'item';
    
    function fetchChildrenOf ($id) {
    	
    	return $this->fetchAll(
	    	$this->select()
  	      ->where('list_id = ?',$id)
    	);	
    }
    
    function getTableName() {
    	return $this->_name;
    }   
}