<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Acl
 * @subpackage Acl_Role
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Dataset_Acl_Role
 *
 */
class Zre_Dataset_Acl_Role extends Data_Set_Abstract
{
	protected $_modelName = 'Zre_Dataset_Model_AclRole';
	
	public function flush() {
		$table = $this->getModel();
		
		return $table->delete( $table->getAdapter()
									->quoteInto('id = ? OR 1 = 1', 0) );
	}
}
?>