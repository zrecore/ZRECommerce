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
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Plugin_Dataset_Model_Plugin - Provides common queries related to plugins
 */
class Plugin_Dataset_Model_Plugins extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'plugins';
	protected $_primary = 'id';
	
	public function __construct($config = array())
	{
		$this->setDefaultAdapter( new Zre_Db_Adapter_Sqlite( $this->_name . '.sq3' ) );
		
		parent::__construct($config);
	}
}
