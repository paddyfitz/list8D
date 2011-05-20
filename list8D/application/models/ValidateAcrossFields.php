<?php

/**
 * @see Zend_Validate_Abstract
 */
//require_once 'Zend/Validate/Abstract.php';

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
* Class to describe a custom form validator for cross-field dependencies
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_ValidateAcrossFields extends Zend_Validate_Abstract {
 	 
  /**
   * Key to test against
   *
   * @var string|array
   */
  protected $_contextKey;
  
  /**
   * Label of the key to test against
   *
   * @var string|array
   */
  protected $_contextLabel;
 
  /**
   * String to test for
   *
   * @var string
   */
  protected $_testValue;
  
  /**
   * Validation failure message template definitions
   *
   * @var array
   */
  protected $_messageTemplates = array(
  	'keyNotFound'  => 'Parent field does not exist in form input',
  	'keyIsEmpty' => '',
  );
 
  /**
   * FieldDepends constructor
   *
   * @param string $contextKey Name of parent field to test against
   * @param string $testValue Value of multi option that, if selected, child field required
   */
  public function __construct($contextKey, $contextLabel, $testValue = null) {
    $this->setTestValue($testValue);
    $this->setContextKey($contextKey);
    $this->setContextLabel($contextLabel);
  }
 
  /**
   * Defined by Zend_Validate_Interface
   *
   * Wrapper around doValid()
   *
   * @param  string $value
   * @param  array  $context
   * @return boolean
   */
  public function isValid($value, $context = null) {
 
    $contextKey = $this->getContextKey();
 
    // If context key is an array, doValid for each context key
    if (is_array($contextKey)) {
      foreach ($contextKey as $ck) {
        $this->setContextKey($ck);
        if(!$this->doValid($value, $context)) {
          return false;
        }
      }
    } else {
      if(!$this->doValid($value, $context)) {
        return false;
      }
    }
    return true;
  }
 
  /**
   * Returns true if dependant field value is not empty when parent field value
   * indicates that the dependant field is required
   *
   * @param  string $value
   * @param  array  $context
   * @return boolean
   */
  public function doValid($value, $context = null) {
  
    $testValue  = $this->getTestValue();
    $contextKey = $this->getContextKey();
    $contextLabel = $this->getContextLabel();
    $value      = (string) $value;
    $this->_setValue($value);
    
    $this->_messageTemplates['keyIsEmpty'] = "This field is required with {$contextLabel}";
 
    if ((null === $context) || !is_array($context) || !array_key_exists($contextKey, $context)) {
      $this->_error('keyNotFound');
      return false;
    }
 
    if (is_array($context[$contextKey])) {
      $parentField = $context[$contextKey][0];
    } else {
      $parentField = $context[$contextKey];
    }
 		
    if ($value == '' && $parentField != '') {
      $this->_error('keyIsEmpty');
      return false;
    }
 		
    if ($testValue) {
      if ($testValue == ($parentField) && empty($value)) {
        $this->_error('keyIsEmpty');
        return false;
      }
    }
    else {
      if (!empty($parentField) && empty($value)) {
        $this->_error('keyIsEmpty');
        return false;
      }
    }
 
    return true;
  }
 
  /**
   * @return string
   */
  protected function getContextKey() {
    return $this->_contextKey;
  }
  
  /**
   * @return string
   */
  protected function getContextLabel() {
    return $this->_contextLabel;
  }
 
  /**
   * @param string $contextKey
   */
  protected function setContextKey($contextKey) {
    $this->_contextKey = $contextKey;
  }
  
    /**
   * @param string $contextLabel
   */
  protected function setContextLabel($contextLabel) {
    $this->_contextLabel = $contextLabel;
  }
 
  /**
   * @return string
   */
  protected function getTestValue () {
    return $this->_testValue;
  }
 
  /**
   * @param string $testValue
   */
  protected function setTestValue ($testValue) {
    $this->_testValue = $testValue;
  }
}