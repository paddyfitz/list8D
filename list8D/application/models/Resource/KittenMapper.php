<?php 

class List8D_Model_Resource_KittenMapper extends List8D_Model_ResourceMapper
{

	protected $_data_table_name = "List8D_Model_DbTable_ResourceData";
	protected $_table_name = "List8D_Model_DbTable_Resource";
	
	//maybe make this abstract
	function getObjectDataArray(){
		$dataArray = array(
				'class' => "List8D_Model_Resource_Kitten",
			);
		return $dataArray;
	}
	
}
