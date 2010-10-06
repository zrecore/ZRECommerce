<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Plugins
 * @subpackage Plugins_Dataset
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Plugin_Dataset_Plugin - Provides CRUD operations related to plugins
 */
class Plugin_Dataset_Plugins extends Zre_Dataset_Abstract {
	
	private static $_pluginsDbmodel;
	/**
	 * Returns the Zend_Db_Table object
	 *
	 * @return Zend_Db_Table
	 */
	private static function getDbTable() {
		if (!isset(self::$_pluginsDbmodel)) {
			self::$_pluginsDbmodel = new Plugin_Dataset_Model_Plugins();
		}
		return self::$_pluginsDbmodel;
	}
	
	public static function create( $data = array() ) {
		$data = Zre_Dataset::filterColumns( $data, self::getDbTable() );
		$data['id'] = self::getNextId();
		return self::getDbTable()->insert( $data );
	}
	
	public static function read( $data = array() ) {
		
		$data = Zre_Dataset::filterColumns( $data, self::getDbTable() );
		$id = (int)$data['id'];
		
		$query = self::getDbTable()->select()->where('id = ?', $id);
		$result = self::getDbTable()->fetchAll( $query );
		
		$data = $result->toArray();
		
		return $data;
		
	}
	
	public static function update( $data = array() ) {
		
		$data = Zre_Dataset::filterColumns( $data, self::getDbTable() );
		
		$id = $data['id'];
		unset($data['id']);
		
		return self::getDbTable()->update( $data, 
			self::getDbTable()->getAdapter()->quoteInto('id=?', $id) );
		
	}
	
	public static function delete( $data = array() ) {
		$data = Zre_Dataset::filterColumns( $data, self::getDbTable() );
		$id = $data['id'];
		
		return self::getDbTable()->delete( self::getDbTable()->getAdapter()->quoteInto('id = ?', $id) );
	}
	
	public static function listAll( $where=null, $value = null ) {
		$select = self::getDbTable()->select();
		if (isset($where)) {
			$select = $select->where( $where, $value );
		}
		$result = self::getDbTable()->fetchAll( $select );
					
		return $result->toArray();
	}
	
	public static function getNextId() {
		$table = self::getDbTable();
		
		$query = "SELECT COUNT(*) as total FROM " . $table->info('name');
		
		$db = $table->getAdapter();
		$result = $db->query( $query );
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$newId = ((int)$data[0]['total']);
		
		return $newId;
	}
}
?>