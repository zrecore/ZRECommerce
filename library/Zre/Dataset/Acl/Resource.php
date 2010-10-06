<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Acl
 * @subpackage Acl_Resource
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Dataset_Acl_Resource
 *
 */
class Zre_Dataset_Acl_Resource extends Data_Set_Abstract
{
	/**
	 * The Zend_Db_Table object
	 *
	 * @var Zre_Dataset_Model_AclResource
	 */
	protected $_modelName = 'Zre_Dataset_Model_AclResource';
	
	public function flush() {
		$table = $this->getModel();
		
		return $table->delete( $table->getAdapter()
									->quoteInto('id = ? OR 1 = 1', 0) );
	}
}
?>