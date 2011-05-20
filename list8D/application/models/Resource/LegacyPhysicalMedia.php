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
* Class to describe a physical media resource
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
class List8D_Model_Resource_LegacyPhysicalMedia extends List8D_Model_Resource {

	protected $_typeName = "Physical Media";
	protected $_type = "LegacyPhysicalMedia";
	
	public $_data = array(
	  'title'=>array(
	  	'title'=>"Title",
	  	'type'=>'text',			
	  ),
	  'author'=>array(
	  	'title'=>'Author',
	  	'type'=>'text'
	  ),
	  'edition'=>array(
	  	'title'=>'Edition',
	  	'type'=>'text',
	  ),
	  'publisher'=>array(
	  	'title'=>'Publisher',
	  	'type'=>'text',
	  ),
	  'publication_date'=>array(
	  	'title'=>'Publication date',
	  	'type'=>'text',
	  ),
	  'meta_url'=>array(
	  	'title'=>'Links to external data pages',
	  	'type'=>'link_array',
	  ),
	);
	function __construct() {

		parent::__construct();

		$_data['ean'] = "";
		$_data['title'] = "";
		$_data['year'] = "";

	}
	
	/**
	 * Returns a string with the title of the item
	 *
	 * @return string name of physical media
	 */
	function getTitle() {
		return $this->getData('title', true);
	}
	
	function getType() {
		return "LegacyPhysicalMedia";
	}
	
	function useTypeReference() {
		return "PhysicalMedia";
	}
	
}
