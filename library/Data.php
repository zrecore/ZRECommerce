<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Data
 * @category Data
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * Data
 *
 */
class Data {
	/**
	 * Returns the specified data model.
	 * 
	 * @param string $modelName
	 * @return Zend_Db_Table_Abstract
	 */
	public static function getModel($modelName) {
		$model = new $modelName();
		return $model;
	}
	/**
	 * Filter key/value pairs against a model's column names.
	 * 
	 * @param array $data
	 * @param Zend_Db_Table_Abstract $model
	 * @param array|null $denyCols Deny these columns
	 * @param array|null $allowCols Allow these columns
	 * @return array
	 */
	public static function filterColumns(array $data, Zend_Db_Table_Abstract $model, array $denyCols = null, array $allowCols = null) {
		$columns = $model->info('cols');
		$newData = array();
		
		$isAllowed = true;
		$isNotDenied = true;
		$isColumn = false;
		
		if (!isset($allowCols)) $allowCols = $columns;
		
		foreach ($data as $key => $value) {
			
			$isAllowed = true;
			$isNotDenied = true;
			$isColumn = false;
			
			if (isset($allowCols)) $isAllowed = in_array($key, $allowCols);
			if (isset($denyCols)) $isNotDenied = !in_array($key, $denyCols);
			
			$isColumn = (bool) in_array($key, $columns);
			
			if ($isAllowed && $isColumn && $isNotDenied) {
				$newData[$key] = $value;	
			}
			
		}
		
		return $newData;
	}
	
	public static function serializeParam(&$params, $paramKey) {
		if (isset($params[$paramKey])) {
			$hasNull = false;
			if (is_string($params[$paramKey]) && $params[$paramKey] != '') {
				$params[$paramKey] = array($params[$paramKey]);	
			}
			
			foreach($params[$paramKey] as $parent) {
				if ($parent == '') $hasNull = true;
			}
			if (!$hasNull) {
				$params[$paramKey] = serialize( $params[$paramKey] );
			} else {
				$params[$paramKey] = '';
			}
		} else {
			$params[$paramKey] = '';
		}
	}
}