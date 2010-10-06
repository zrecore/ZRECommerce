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
 * Zre_Db_Mysql - Singleton class that provides an adapter to the mysql database.
 *
 */
class Zre_Db_Mysql
{
	private static $db;
	/**
	 * Singleton class implementation. Retreives the mysql pdo object used for this system.
	 * 
	 * @return Zre_Db_Adapter_Mysql
	 */
	public static function getInstance()
	{
		if (!isset(self::$db))
		{
			self::$db = new Zre_Db_Adapter_Mysql();
		}
		
		return self::$db;
	}
}
?>