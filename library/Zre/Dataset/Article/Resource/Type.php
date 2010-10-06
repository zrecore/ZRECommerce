<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Dataset
 * @subpackage Dataset
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Dataset_Article_Resource_Type - CRUD implementation of article resource
 * listing dataset(s).
 *
 */
class Zre_Dataset_Article_Resource_Type extends Zre_Dataset_Abstract 
{
	/**
	 * Create a new resource type.
	 *
	 * @param string $type
	 * @return boolean
	 */
	public static function create( 	$type )
	{
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();
												
		if ( !self::exists( $type ) ) {
			$newId = self::getNextId();
			$articleResourceTypeTable->insert( array('id'=>$newId,'type' => $type) );
		}
		
		return true;
	}
	
	public static function exists( $type ) {
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();
		$resourceData = $articleResourceTypeTable->fetchAll( $articleResourceTypeTable
													->select()
													->from('articleResourceType')
													->where('type = ?', $type)
													->limit(1));
													
		if ( $resourceData->count() == 0 ) {
			return false;
		} else {
			return true;
		}
	}
	public static function getNextId() {
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();
		
		$query = "SELECT COUNT(*) as total FROM articleResourceType";
		
		$db = $articleResourceTypeTable->getAdapter();
		$result = $db->query( $query );
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$newId = ((int)$data[0]['total']);
		
		return $newId;
	}
	/**
	 * Returns an array of resource types
	 *
	 * @return array
	 */
	public static function read()
	{
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();
		
		$resourceData = $articleResourceTypeTable->fetchAll( $articleResourceTypeTable->select() )->toArray();
		return $resourceData;
	}
	/**
	 * Update an article resource type. Returns the amount of rows affected.
	 *
	 * @param array $data
	 * @return int
	 */
	public static function update( 	$data = array() )
	{
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();
		
		$data = Zre_Dataset::filterColumns( $data, $articleResourceTypeTable );
		$id = null;
		
		if (isset($data)) {
			$id = $data['id'];
			unset($data['id']);
		} else {
			return 0;
		}
		
		return $articleResourceTypeTable->update(
			$data, 
			$articleResourceTypeTable->
				getAdapter()->
				quoteInto('id = ?', $id));
		
	}
	/**
	 * Remove an article resource type
	 *
	 * @param int $type
	 */
	public static function delete( 	$type  )
	{
		$articleResourceTypeTable = new Zre_Dataset_Model_ArticleResourceType();

		$articleResourceTypeTable->delete( $articleResourceTypeTable->getAdapter()
						   ->quoteInto('type = ?', $type) );
	}
}