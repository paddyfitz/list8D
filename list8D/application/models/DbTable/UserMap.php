<?php 

class List8D_Model_DbTable_UserMap extends Zend_Db_Table_Abstract {
    /** Table name */
    protected $_name = 'usermap';

    
    function getTableName() {
    	return $this->_name;
    } 
}