<?php 

class List8D_Model_DbTable_List extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'list';
    
    function fetchTrunks() {
    	
    	return $this->fetchAll(
	    	$this->select()
  	      ->where('row_id IS NULL')
    	);
    	
    }
    
    function getTableName() {
    	return $this->_name;
    } 
}