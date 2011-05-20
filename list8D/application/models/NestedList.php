<?php

class List8D_Model_NestedList extends List8D_Model_List {

	protected $_typeName = 'Nested list';
	protected $_mapperClass = 'List8D_Model_ListMapper';
	protected $_expanded = "Heading with nested sublist";

	public function getTypeName() {
		return $this->_typeName;
	}
	
	public function getExpanded() {
		return $this->_expanded;
	}
	
	public $_data = array(
    'title'=>array(
	  	'title'=>'Title',
	  	'type'=>'text',
	  	'required'=>true,		
	  ),
    'start'=>array(
	  	'title'=>'Start date',
	  	'type'=>'date',
	  ),
    'end'=>array(
	  	'title'=>'End date',
	  	'type'=>'date',
	  ),
    'is_published'=>array(
	  	'title'=>'Published',
	  	'type'=>'boolean',
	  	'default'=>true,
	  ),
    'public_notes'=>array(
	  	'title'=>'Student notes',
	  	'type'=>'multiline',
	  ),
    'private_notes'=>array(
	  	'title'=>'Librarian notes',
	  	'type'=>'multiline',
	  ),
  );
  
  public function isNestedList() {
  	return true;
  }
  
}