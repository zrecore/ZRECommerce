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
 * Zre_Db_Sqlite - Singleton class that provides adapter(s) to sqlite database files.
 *
 */
class Zre_Db_Sqlite
{
	private static $db;
	/**
	 * Singleton class implementation. Retreives the mysql pdo object used for this system.
	 * 
	 * @return Zre_Db_Adapter_Sqlite
	 */
	public static function getInstance($fileName)
	{
		if (!isset(self::$db))
		{
			self::$db = new Zre_Db_Adapter_Sqlite($fileName);
		}
		
		return self::$db;
	}
}
?>