<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Data
 * @subpackage Data_Set
 * @category Data
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Data_Set
 *
 */
class Data_Set {
	/**
	 * Filters out columns that do not exist in the specified Zend_Db_Table
	 * object. Returns the filtered version of the $data associative array.
	 *
	 * @param array $data
	 * @param Zend_Db_Table $tableObject
	 * 
	 * @return array
	 */
	public static function filterColumns($data, $tableObject) {
		$tableInfo = $tableObject->info();
		$tableCols = $tableInfo['cols'];
		
		foreach ($data as $key => $value) {
			if (!in_array($key, $tableCols)) {
				unset($data[$key]);
			}
		}
		
		return $data;
	}
	/**
	 * Generates a form using the specified Data_Set_Abstract object.
	 *
	 * @param unknown_type $dataset
	 */
	public static function createForm( $dataset ) {
		
	}
	/**
	 * Retrieves the next available id in a table.
	 *
	 * @param Zend_Db_Table $table
	 */
	public static function getNextId( $table ) {
		
	}
}
?>