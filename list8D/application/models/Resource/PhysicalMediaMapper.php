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
* Mapper for Physical Media Resource object
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_Resource_PhysicalMediaMapper extends List8D_Model_ResourceMapper
{
	protected $_data_table_name = "List8D_Model_DbTable_ResourceData";
	protected $_table_name = "List8D_Model_DbTable_Resource";
	
	//maybe make this abstract
	/**
	 * Returns the internal data array
	 *
	 * @return array
	 */
	function getObjectDataArray(){
		$dataArray = array(
				'class' => "List8D_Model_Resource_PhysicalMedia",
			);
		return $dataArray;
	}
}