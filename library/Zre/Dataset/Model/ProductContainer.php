<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Datamodel
 * @subpackage Datamodel
 * @category Datamodel
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Dataset_Model_ProductContainer - Provides common queries related to
 * product containers (aka, product directories)
 *
 */
class Zre_Dataset_Model_ProductContainer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'product_container';
	
	public function __construct($config = array())
	{
		$settings = Zre_Config::getSettingsCached();
    	
    	
		$this->_name = $settings->db->table_name_prepend . $this->_name;
		$this->_primary = 'id';
		$this->setDefaultAdapter( Zre_Db_Mysql::getInstance() );
		
		parent::__construct($config);
	}

}