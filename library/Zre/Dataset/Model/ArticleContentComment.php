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
 * Zre_Dataset_Model_ArticleContentComment - Provides common queries related to
 * article comments. 
 */

class Zre_Dataset_Model_ArticleContentComment extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'article_content_comment';
	
	public function __construct($config = array())
	{
		$settings = Zre_Config::getSettingsCached();
    	
    	
		$this->_name = $settings->db->table_name_prepend . $this->_name;
		$this->_primary = 'id';
		$this->setDefaultAdapter( Zre_Db_Mysql::getInstance() );
		
		parent::__construct($config);
	}

}
