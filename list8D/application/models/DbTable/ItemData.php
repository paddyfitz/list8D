<?php 

class List8D_Model_DbTable_ItemData extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'item_data'; 
    
    
    function getTableName() {
    	return $this->_name;
    } 
}