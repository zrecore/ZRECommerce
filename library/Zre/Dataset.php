<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Config
 * @category Config
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Dataset - Contains common Dataset helper methods.
 *
 */
class Zre_Dataset {
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
}
?>