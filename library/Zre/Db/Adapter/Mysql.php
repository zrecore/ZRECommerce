<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Database
 * @subpackage Database
 * @category Database
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Db_Adapter_Mysql - A mysql database adapter.
 *
 */
class Zre_Db_Adapter_Mysql extends Zend_Db_Adapter_Pdo_Mysql 
{
	public function __construct($config = null)
	{
		if(Zend_Session::isStarted())
	  	{
	  		$settings = Zre_Config::getSettingsCached();
    		
			if (!isset($config))
			{
		  		$config = new Zend_Config( array (
		  								'host'=>$settings->db->hostname,
		  								'username'=>$settings->db->username,
		  								'password'=>$settings->db->password,
		  								'dbname'=>$settings->db->database
		  		));
			}
	  	
	  	}
	  	
		parent::__construct( $config );
	}
}
?>