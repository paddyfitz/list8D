<?php 

class List8D_Model_DbTable_Tag extends Zend_Db_Table_Abstract {
    /** Table name */
    protected $_name = 'tag';

    
    function getTableName() {
    	return $this->_name;
    } 
}