<?php 

class List8D_Model_DbTable_Resource extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'resource';
    
    protected $_dependentTables = array('List8D_Model_DbTable_ResourceData');
    
    
    function getTableName() {
    	return $this->_name;
    } 
    
    
}