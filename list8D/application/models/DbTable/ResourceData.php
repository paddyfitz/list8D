<?php 

class List8D_Model_DbTable_ResourceData extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'resource_data';
    
    protected $_referenceMap = array(
        'Resource' => array(
            'columns' => 'row_id',
            'refTableClass' => 'List8D_Model_DbTable_Resource',
            'refColumns' => 'id'
        )
    );
    
    function getTableName() {
    	return $this->_name;
    } 
}