<?php 

class List8D_Model_DbTable_TagMap extends Zend_Db_Table_Abstract {
    /** Table name */
    protected $_name = 'tagmap';
    
    
    function getTableName() {
    	return $this->_name;
    } 
}