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
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Db_Adapter_Sqlite - A mysql database adapter.
 *
 */
class Zre_Db_Adapter_Sqlite extends Zend_Db_Adapter_Pdo_Sqlite 
{
	/**
	 * Internal variable that holds the sqlite file path.
	 *
	 * @var string
	 */
	private $_fileName;
	
	/**
	 * Constructor. 
	 * 
	 * @param string $fileName - The file path of the sqlite file relative to 
	 * 				 			 the db->sqlite_directory specified in 
	 * 							 /application/default/settings/environment/settings.xml
	 */
	public function __construct($fileName)
	{
		$settings = Zre_Config::getSettingsCached();
    	
		$config = array('dbname'=> $settings->db->sqlite_directory . DIRECTORY_SEPARATOR . $fileName);
		
		parent::__construct($config);
	}
}
?>