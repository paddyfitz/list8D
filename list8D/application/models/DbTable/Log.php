<?php 

class List8D_Model_DbTable_Log extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'change_log';
    
    function getTableName() {
    	return $this->_name;
    } 
    
}