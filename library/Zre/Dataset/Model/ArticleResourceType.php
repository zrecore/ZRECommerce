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
 * Zre_Dataset_Model_ArticleResourceType - Provides common queries related
 * to the article resource type list.
 *
 */
class Zre_Dataset_Model_ArticleResourceType extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'articleResourceType';
	protected $_primary = 'id';
	
	public function __construct($config = array())
	{
		$settings = Zre_Config::getSettingsCached();
		
    	$this->_name = $settings->db->table_name_prepend . $this->_name;
    	
		$this->setDefaultAdapter( Zre_Db_Mysql::getInstance() );

//		$this->setDefaultAdapter( new Zre_Db_Adapter_Sqlite( $this->_name . '.sq3' ) );
		
		parent::__construct($config);
	}

}
