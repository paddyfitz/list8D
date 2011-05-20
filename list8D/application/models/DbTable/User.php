<?php 

class List8D_Model_DbTable_User extends Zend_Db_Table_Abstract {
    /** Table name */
    protected $_name = 'user';

    
    function getTableName() {
    	return $this->_name;
    } 
}