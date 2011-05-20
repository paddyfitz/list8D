<?php
/**
 * List8D
 *
 * LICENCE
 *
 * Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */
 
/**
* Root class for all things
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
abstract class List8D_Model_Root {

	//Extending classes need to implement TABLE_NAME
	//const TABLE_NAME;

	public $_dataValues = array();
	public $_dataTitles = array();
	public $_dataTypes = array();
	public $_data = array();
	protected $_id;
 	protected $_mapper;
	protected $_class;
	public $dataLoaded = false;
	protected $_duplicate;
	
	function __construct(){
		//$this->_db = Zend_Registry::get('dbResource')->getDbAdapter();
		//$this->_id = null;
		$this->_class = get_class($this);
	}

  	/**
	 * Used to call out this item as a duplicate.
	 *
	 * @param object $duplicate 
	 * @return object itself
	 */
	public function setDuplicate($duplicate) {
		$this->_duplicate = $duplicate;
		return $this;
	}
	
	/**
	 * If this is a duplicate, returns the duplicate object else false
	 *
	 * @return mixed - either false or the duplicate object
	 *
	 */
	public function getDuplicate() {
		if ($this->_duplicate == null) {
			return false;
		}
		return $this->_duplicate;
	}
	
	/**
	 * Returns the value of the id column of the object as represented in the database table
	 *
	 * @return integer id - the value of the id column in the database table pertaining to this item
	 */
	function getId(){

		  return $this->_id;
	}
	
	/**
	 * Sets the value of the object - maps to the id column in the appropriate database table
	 *
	 * @param integer $value - integer representing the id of the row
	 * @return object itself - returns the entire object
	 */ 
	function setId($value){
		//echo "setting ID to ".$value;
		$this->_id=$value;
		//echo "ID set to ".$this->_id;
		return $this;
	}
	
	/**
	 * Returns the List8D Class of the object
	 *
	 * @return string class - the classname
	 */
	function getClass(){
		return $this->_class;
	}
	
	/**
	 * Sets the List8D class of the object
	 *
	 * @param string $value - String representation of class name
	 * @return object itself
	 */
	function setClass($value){
		$this->_class=$value;
		return $this;
	}
	
	/**
	 * Saves the metadata of the object to the appropriate database table
	 *
	 */
	function saveData() {

		$this->getMapper()->saveData();	
		
		
	}
	
	/**
	 * Loads the metadata for the object from the database into the object instance
	 *
	 * @return array of object data
	 */
	protected function loadData(){
		//SELECT from ".self::TABLE_NAME."_data WHERE ".self::TABLE_NAME."_id=".$this->id."
		
		//return the data
		return array();
	}
	
	/**
	 * Returns an instantiated object of the class, populated with data from the row from the appropriate database table pertianing to that class with the given id
	 *
	 * @param integer $id - an integer representing the required id value
	 * @return object the instantiated object
	 */
	function getById($id) {
		return $this->getMapper()->getById($id);
	}
	
	/**
	 * Returns the metadata for the calling item as a results set
	 *
	 * @param integer $id - an integer representing the id of the object for which we wish to get the metadata
	 * @return resultset result set of metadata
	 *
	 */
	public function getDataObjectsById($id) {
		return $this->getMapper()->getDataObjectsById($id);
	}
	
	/**
	 * Returns an array of metadata organised into subarrays. If namespace is also given, returns metadata pertaining to that metatron only
	 *
	 * @param string $namespace - nullable. If set, only returns metadata pertaining to the metatron owning the namespace
	 * @param boolean $returnValue - defaults to false, if given, returns that specific entry pertaining to the metatron owning the namespace given in $namespace
	 * @return array of metadata
	 */
	function getData($namespace = null,$returnValue=false,$join=" "){
		
		if(!$this->dataLoaded && $this->_id != null){
			//fetch
			$this->_dataValues = $this->getMapper()->getData($this);
			
			foreach ($this->_dataValues as $key => $structure) {
				if (isset($this->_dataValues[$key])) {
					$this->_data[$key]['value'] = $this->_dataValues[$key];
				} else {
					$this->_data[$key]['value'] = null;
				}
			}

		}
		$data = $this->_data;
		//return from the  _data array
		if ($namespace==null) {
			return $this->_data;
		} else if (!empty($data[$namespace])) { 
			if ($returnValue) {	
				if (!empty($this->_data[$namespace]['value'])) {
					$value = $this->_data[$namespace]['value'];			
					if ($join !== false && is_array($value))
						$value = $this->_implode($join,$value);
					return $value;
				} else {
					return false;
				}
			} else {
				return $this->_data[$namespace];
			}
		} else {
			return false;
		}
	}
	
	public function getLoadedData() {
		return $this->_data;
	}
	
	public function _implode($join,$value) {
		
		foreach($value as &$v) {
			if (is_array($v)) {
				$v = $this->_implode($join,$v);
			}
		}
		
		return implode($value);
		
	}
	
	/**
	 * Returns the value of an item of metadata as specified by namespace
	 *
	 * @param string $namespace
	 * @return object
	 */
	function getDataValue($namespace=null,$join=" ") {
		if ($namespace==null) 
			return $this->getDataValues();
		else
			return $this->getData($namespace,true,$join);
	}
	
	public function getLoadedDataValues() {
		return $this->_dataValues;
	}
	
	
	/**
	 * Returns the raw metadata
	 * 
	 * @return array of metadata
	 */
	function getDataStructure($key=false) {
		return $this->_data;
	}
	
	//getDataTitles(), which this calls, does not exist. Let's not document this one...
	function getDataTitle($key=false) {
		
		$output = $this->getDataTitles();
		
		if (!$key) {
  		return $output;
  	} else {
  		return $output[$key];
  	}
		
	}
	
	//geDataTypes, which this calls, does not exist. Let's not document this one...
	function getDataType($key=false) {
		
		$output = $this->getDataTypes();
		
		if (!$key) {
  		return $output;
  	} else {
  		return $output[$key];
  	}
		
	}
	
	/**
	 * Returns the values of the metadata pertaining to the calling object
	 *
	 * @return array of values
	 *
	 */
	function getDataValues(){
		$this->getData();
		return $this->_dataValues;
	}
	
	/**
	 * Sets a specific item of metadata
	 *
	 * @param string $key - the key that the value pertains to
	 * @param string $value - the value for the specified key
	 * @return object itself
	 *
	 */
	function setData($key,$value) {
		
		if($key == "start" && method_exists($this,'setStart') ) {
			$this->setStart($value);
			//$this->_start = $value;
		}

		else if($key == "end" && method_exists($this,'setEnd')) {
			$this->setEnd($value);
			//$this->_end = $value;
		}
		else{
			$this->_dataValues[$key] = $value;
			$this->_data[$key]['value'] = $value;
		}
		
		return $this;
	}
	
	function addDataWhenArray($key,$value) {
		if (is_array($value) && isset($this->_dataValues[$key]) && is_array($this->_dataValues[$key])) {

			$old = $this->_dataValues[$key];
			$new = array_merge($old,$value);
			$this->_dataValues[$key] = $new;
			$this->_data[$key]['value'] = $new;
		} else {
			$this->setData($key,$value);
		}
		return $this;
	}
	
	/**
	 * Bulk-set items of metadata
	 *
	 * @param array $array - array of key/value pairs to save as metadata
	 *
	 */
	function setDataByArray($array) {
	
		foreach($array as $key => $value) {
			$this->setData($key, $value);
		}
		
		$this->dataLoaded = true;
		return $this;
		//surely we need to set dataLoaded = true?
	}
	
	/**
	 * Sets the appropriate mapper for the object
	 *
	 * @param string $mapper - name of the mapper object
	 * @return object itself
	 *
	 */
 	function setMapper($mapper)
 	{
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
	/**
	 * Gets the mapper for this object
	 *
	 * @return object the mapper
	 *
	 */
 	public function getMapper()
 	{ 
 	    if (null === $this->_mapper) {
 	    	if (isset($this->_mapperClass)) {
 	    		$mapper_class = $this->_mapperClass;
 	    	} else {
	 				$mapper_class = get_class($this)."Mapper";
 	      }
 	      $this->setMapper(new $mapper_class());
 	    }
 	    return $this->_mapper;
 	}
 	
	/**
	 * Persists the object to the database
	 *
	 */
 	public function save()
 	{
 	    $this->getMapper()->save($this);
 	}
	
	/**
	 * Deletes the object from the database
	 *
	 */
	function delete() {
	
		$this->getMapper()->delete($this);
		
	}
 	
	/**
	 * Find the object with the specified id from the appropriate database table
	 *
	 * @param integer $id - the ID of the row to return
	 * @return object itself
	 */
 	public function find($id)
 	{
 	    $this->getMapper()->find($id, $this);
 	    return $this;
 	}
 	
	//deprecated
 	public function fetchAll()
 	{
 	    return $this->getMapper()->fetchAll();
 	}
 	
	/**
	 * Used to determine whether the calling object is a list or not. Default false. Overriden by List class
	 *
	 * @return false
	 */
 	public function isList() {
 		return false;
 	}
	
	/**
	 * Logs the given data to the logging system
	 *
	 * @param array $data - the data to log
	 *
	 */
	public function log($data=array()) {
		$this->getMapper()->log($data);
	}
	
	
	public function findByData($a,$b=null,$c=null, $d=null, $e=null, $f=null) {

	 	// if arguments are key, value, multipleresults, needed matches, contains
		if (!is_array($a) && $b !== null) {
			$keyValues = array($a=>$b);
			if ($c) {
				$multipleResults = $c;
			}
			if ($d!==null) {
				$neededMatches = $d;
			} 
			if ($e!==null) {
				$contains = $e;
			} 
			if ($f!==null) {
				$limit = $f;
			} 
			
		} 
		
		// if arguments are keyValue, multipleresults, needed matches, contains
		else if (is_array($a)) {
			$keyValues = $a;
			if ($b) {
				$multipleResults = $b;
			}
			if ($c!==null) {
				$neededMatches = $c;
			} 
			if ($d!==null) {
				$contains = $d;
			} 
			if ($e!==null) {
				$limit = $e;
			} 
		}
		
		if (!isset($multipleResults))
			$multipleResults=false;
		
		if (!isset($contains))
			$contains=false;
			
		if (!isset($limit))
			$limit=false;
			
		// If needed matches is not set or set to all (0) then needed matches is number of key value pairs
		if (!isset($neededMatches) || $neededMatches === 'all' || $neededMatches === null) {
			$neededMatches = count($keyValues);
		} 
		
		// Needed matches cannot not be more than specified key value pairs
		if ($neededMatches > count($keyValues)) {
			$neededMatches = count($keyValues);
		}
		
		// Start the query
		$dataTable = $this->getMapper()->getDbDataTable();
		$query = $dataTable->select();
		
		// We only need the row_id
		$query->from($dataTable, array('row_id'));
		
		// Add where conditions
		foreach($keyValues as $key => $value) {
			$value = addslashes($value);
			if ($contains) {
				$query->orWhere("`key` = '$key' AND `value` LIKE '%$value%'");
				$query->orWhere("`key` = '$key' AND `value` LIKE '%".serialize($value)."%'");
			} else {
				$query->orWhere("`key` = '$key' AND `value`='$value'");
				$query->orWhere("`key` = '$key' AND `value`='".serialize($value)."'");
			}
			
		}
		
		// Group rows by row_id
		$query->group('row_id');
		
		// Having the minimum conditions
		$query->having('count(*) >= ?',$neededMatches);
		
		// order by key matches
		$query->order('count(*)');
		
		// limit the results
		if ($limit)
			$query->limit($limit);
		
		// Perform query
		$results = $dataTable->fetchAll($query);

		if (count($results)==0) {
			return false;
		}
		
		if ($multipleResults) {
			// Get results as array
			$return = array();
			foreach($results as $result) {
				$return[] = $this->getByID($result->row_id);
			}
		} else {
			$return = $this->getByID($results[0]->row_id);
		}

		return $return;
		
		//SELECT l.row_id, count(*) FROM list_data l WHERE ((l.key = "title" AND l.value = "Macroeconomics") OR (l.key = "code" AND l.value = "EC502")) GROUP BY l.row_id HAVING count(*) = 2
				
	} 
	
	function getEditForm($element = null) {

		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$form = new List8D_Form();
		// If we are editing a single data item 
		if (isset($element)) {
			
			if (
				(!$currentUser->isAllowed($this->getAccessResourceType().'-field-'.$element,'edit') &&
				!$currentUser->isAllowed($this->getAccessResourceType().'-fields','edit'))
				||
				(!$currentUser->isAllowed($this,'edit-tagged') && !$currentUser->isAllowed('list','edit'))
			) 
			
			throw new Zend_Controller_Action_Exception("You do not have permission to edit the field '{$element}' for {$this->getAccessResourceType()} with id '{$this->getId()}'.",403);
				
			// Get the data 
			$data = $this->getData($element);
			
			// Translate the data type into a form type
			switch($data['type']) {
				case "multiline":
					$type = "textarea";
					break;
				case "boolean":
					$type = 'checkbox';
					break;
				case "array":
				case "users":
					$type = 'textarray';
					break;
				default: 
					$type = "text";
					break;
			}
			
			if($type != 'textarray'){
				$elementSettings = array(
					'label'      => $data['title'].':',
		  		'filters'    => array('StringTrim'),
				);
			}
			else{
				$elementSettings = array(
					'label'      => $data['title'].':',
		  		'filters'    => array(),
				);
			}
			
			if (isset($data['value']))
        		$elementSettings['value'] = $data['value'];
      		elseif (isset($data['default']))
        		$elementSettings['value'] = $data['default'];
        
			// Add its element
			$form->addElement($type, $element, $elementSettings);
			
		} else {

			foreach($this->getData() as $namespace => $data) {

				//pre_dump($this->getData());

				if (
					($currentUser->isAllowed($this->getAccessResourceType().'-field-'.$element,'edit') ||
					$currentUser->isAllowed($this->getAccessResourceType().'-fields','edit'))
				) { 	
				
					if	 (!empty($data['type'])) {
						// Translate the data type into a form type
						switch($data['type']) {
							case "multiline":
								$type = "textarea";
								break;
							case "boolean":
								$type = 'checkbox';
								break;
							case "array":
							case "users":
								$type = 'array';
								break;
							case "radio":
								$type = 'radio';
								break;
							default: 
								$type = 'text';
								break;
						}
						
						//echo $data['title'].": ".$data['type'].", type = ".$type."<br />";
						
						//hmmm...
						if($type != 'array'){
							$elementSettings = array(
								'label'      => $data['title'].':',
	      			  			'filters'    => array('StringTrim'),
							);
						}
						else{
							$elementSettings = array(
								'label'      => $data['title'].':',
	      			  			'filters'    => array(),
							);
						}
						
						//pre_dump($elementSettings);
						
						if (isset($data['value']))
      			  			$elementSettings['value'] = $data['value'];
							
      					elseif (isset($data['default']))
      			  			$elementSettings['value'] = $data['default'];

						if($type == "radio")
							$elementSettings['multioptions'] = $data['options'];
      			
      					// get validators and required from data and put into form
      					if (isset($data['validators'])) {
							$elementSettings['validators'] = $data['validators'];
						}
						if (isset($data['required'])) {
							$elementSettings['required'] = $data['required'];
							$elementSettings['label'] .= ' *';
						}
						
						// Add its element
						$form->addElement($type, $namespace, $elementSettings);
						
						
					}
					
				}
				
			}
			
		}
		
		// Add submit button
		$form->addElement('submit', 'submit', array(
			'ignore'   => true,
      		'label'    => 'Save',
		));

		return $form;
		
	}
	
	function __set_state($data) {
	
	   $return = new $data['_class'];
	   foreach($data as $key => $value) {
    	   $return->$key = $value;
	   }
	   $return->dataLoaded = true;
	   return $return;
	}
	
	public function getAccessResourceType() {
		return null;
	}

	public function getAccessResource() {
		return $this;
	}
	
	/**
	* Duplicates a object, including  data and any items linked to that object
	* duplicateItems and duplicateRow all do nothing at this level but saves us writing
	* separate duplicate at the list or item level
	*
	* @param integer $list_id the list id
	* @return mixed the duplicate object
	*/
 	public function duplicate() {
 		
 		try {
			
	 		// start duplicate
			$class = get_class($this);
 			$duplicate = new $class;
 			
	 		// duplicate row fields
	 		$this->duplicateRow($duplicate);
	 		
 			// duplicate data
	 		$this->duplicateData($duplicate);
	 		
	 		// duplicate items
	 		$this->duplicateChildren($duplicate);
	 		
	 		$duplicate->setDuplicate($this->getId());
 		
 			return $duplicate;
 			
 		} catch (Exception $e) {
 		
 			return false;
 			
 		}
 		
 	}
 	
 	public function duplicateRow(&$duplicate) {
 		return $this;
 	}
 	
 	public function duplicateChildren(&$duplicate) {
 		return $this;
 	}
 	
	/**
	* Duplicates an object's data including its data
	*
	* @param integer $list_id the list id
	*/
 	public function duplicateData(&$duplicate) {
		
		$id = $this->getId();
 		$object = $this;
 		
 		// get data
 		$data = $this->getDataValues();
    $duplicate->dataLoaded = true;
 		// set data
 		$duplicate->setDataByArray($data);
 		
 		return $this;
 		
	}
}