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
 * Data_Set_Abstract
 *
 */
abstract class Data_Set_Abstract {
	/**
	 * The internal data model name.
	 * @var string
	 */
	protected $_modelName;
	/**
	 * Constructor. Create a new Data set using the specified data model.
	 * @param $modelName The data model to load.
	 * @param $primaryIdColumn The primary ID column to use.
	 * @return void
	 */
	public function init($modelName=null, $primaryIdColumn=null) {
		try {
			$this->_modelName = $modelName;
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Get the associated model
	 * @return Zend_Db_Table_Abstract
	 */
	public function getModel() {
		try {
			return Data::getModel($this->_modelName);
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Retrieves information by means of the internal data model.
	 * @param  $key The database table info to retrieve
	 * @return mixed
	 */
	public function info($key = null) {
		try {
			return $this->getModel()->info($key);
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Create a new entry.
	 * @param array $data Column-value pairs
	 * @return mixed The primary key of the row inserted.
	 */
	public function create(array $data) {
		try {
			$model = $this->getModel();
			$data = Data::filterColumns($data, $model);
			
			return $model->insert($data);
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Fetches rows by primary key.
	 * @param mixed $key The value(s) of the primary keys.
	 * @return Zend_Db_Table_Rowset_Abstract matching the criteria
	 * @throws Zend_Db_Table_Exception
	 */
	public function read($key) {
		try {
			$model = $this->getModel();
			$data = $model->find($key);
			
			return $data;
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Update an entry.
	 * @param array $data Column-value pairs.
	 * @param array|string $where
	 * @return int The number of rows updated.
	 */
	public function update(array $data, $where) {
		try {
			$model = $this->getModel();
			$primaryKeys = self::info('primary');
			if (!is_array($primaryKeys)) $primaryKeys = array($primaryKeys);
			
			if (is_numeric($where) && count($primaryKeys) == 1) {
				$where = $model->getAdapter()->quoteInto($primaryKeys[1] . ' = ?', $where);
			}
			$data = Data::filterColumns($data, $model, $primaryKeys);
			return $model->update($data, $where);
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * Delete an entry
	 * @param array|string $where SQL WHERE clause(s)
	 * @return int The number of rows deleted
	 */
	public function delete($where, $value) {
		try {
			$model = $this->getModel();
			
			return $model->delete($model->getAdapter()->quoteInto($where, $value));
		} catch (Exception $e) {
			Debug::logException($e);
			throw $e;
		}
	}
	/**
	 * List rows by column key/value pairs
	 * 
	 * The $columns parameter should contain key/value pairs, representing
	 * the table column, and the value to use in the query (respectively).
	 * 
	 * The $options parameter can contain the following options:
	 * <code>
	 * 	$options = array(
	 * 		'order' => 'quantity DESC', // column name, followed by ASC or DESC
	 * 		'limit' => array(
	 * 						'page' => 3, // what page of results to display
	 * 						'rowCount' => 20, // how many rows compose a page
	 * 					)
	 * 	);
	 * </code>
	 * 
	 * @param array $columns
	 * @param array $options
	 * @param bool $asArray
	 * @return array|Zend_Db_Table_Rowset_Abstract
	 */
	public function listAll($columns = null, $options = null, $asArray = true) {
		$model = $this->getModel();
		
		$select = self::appendOptions( $model->select(), $columns, $options );
		
		$results = $model->fetchAll($select);
		
		return $asArray ? $results->toArray() : $results;
	}
	
	public static function appendOptions($select, $columns = null, $options = null) {
		if (isset($options)) {
			foreach ($options as $optionName => $optionValue) {
				switch ($optionName) {
					
					case 'setIntegrityCheck':
						$select = $select->setIntegrityCheck($optionValue);
						break;
					case 'from':
						
						if (is_string($optionValue)) {
							$select = $select->from($optionValue);
						} else {
							$select = $select->from(
								isset($optionValue['name']) ? $optionValue['name'] : null, 
								isset($optionValue['cols']) ? $optionValue['cols'] : null, 
								isset($optionValue['schema']) ? $optionValue['schema'] : null
							);
						}
						break;
						
				}
			}
		}
		if (isset($columns)) {
			foreach ($columns as $col => $val) {
				if (is_array($val)) {
					if (isset($val['operator']) && isset($val['value'])) {
						$select = $select->where( "$col " . $val['operator'] . " ?", $val);
					}
				} else {
					$select = $select->where($col . ' = ?', $val);
				}
			}
		}
		
		if (isset($options)) {
			foreach ($options as $optionName => $optionValue) {
				switch ($optionName) {
					case 'leftJoin':
						$select = $select->joinLeft(
							isset($optionValue['name']) ? $optionValue['name'] : null, 
							isset($optionValue['cond']) ? $optionValue['cond'] : null, 
							isset($optionValue['cols']) ? $optionValue['cols'] : null, 
							isset($optionValue['schema']) ? $optionValue['schema'] : null
						);
						break;
					case 'join':
						$select = $select->join($optionValue['name'], $optionValue['cond'], $optionValue['cols'], $optionValue['schema']);
						break;
					case 'rightJoin':
						$select = $select->joinRight($optionValue['name'], $optionValue['cond'], $optionValue['cols'], $optionValue['schema']);
						break;
					case 'leftJoinUsing':
						$select = $select->joinLeftUsing(
							isset($optionValue['name']) ? $optionValue['name'] : null,
							isset($optionValue['join']) ? $optionValue['join'] : null,
							isset($optionValue['columns']) ? $optionValue['columns'] : null
						);
						break;
					case 'rightJoinUsing':
						$select = $select->joinRightUsing(
							isset($optionValue['name']) ? $optionValue['name'] : null,
							isset($optionValue['join']) ? $optionValue['join'] : null,
							isset($optionValue['columns']) ? $optionValue['columns'] : null
						);
						break;
					case 'joinUsing':
						$select = $select->joinUsing(
							isset($optionValue['name']) ? $optionValue['name'] : null,
							isset($optionValue['join']) ? $optionValue['join'] : null,
							isset($optionValue['columns']) ? $optionValue['columns'] : null
						);
						break;
						
					case 'order':
						$select = $select->order($optionValue);
						break;	
					case 'limit':
						if (isset($optionValue['page']) && isset($optionValue['rowCount'])) {
							$select = $select->limitPage($optionValue['page'], $optionValue['rowCount']);
						} elseif (isset($optionValue['offset']) && isset($optionValue['count'])) {
							$select = $select->limit($optionValue['count'], $optionValue['offset']);
						}
						break;
				}
			}
		}
		return $select;
	} 
}
