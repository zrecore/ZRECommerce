<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Acl
 * @subpackage Acl_Deny
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Dataset_Acl_Deny
 *
 */
class Zre_Dataset_Acl_Deny extends Data_Set_Abstract
{
	protected $_modelName = 'Zre_Dataset_Model_AclDeny';
	
	public function flush() {
		$table = $this->getModel();
		
		return $table->delete( $table->getAdapter()
									->quoteInto('acl_deny_id = ? OR 1 = 1', 0) );
	}
}
?>