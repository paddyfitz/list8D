<?php 

class List8D_Model_DbTable_ListData extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'list_data';
    
    function getTableName() {
    	return $this->_name;
    } 
}