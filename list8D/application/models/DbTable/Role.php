<?php 

class List8D_Model_DbTable_Role extends Zend_Db_Table_Abstract {
    /** Table name */
    protected $_name = 'role';

    
    function getTableName() {
    	return $this->_name;
    } 
}