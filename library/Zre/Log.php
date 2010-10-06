<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Log
 * @category Log
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Log - Provides log support.
 *
 */
class Zre_Log
{
	private static $_writer;
	private static $_log;
	
	public static function log($message, $priority)
	{
//		$db = new Zre_Db_Adapter_Mysql();
		$db = Zre_Db_Mysql::getInstance();
		self::$_writer = new Zend_Log_Writer_Db($db, 'zre_logs', array('priority'=>'priority','message' =>'message','date'=>'timestamp'));
		
		if(!isset(self::$_log))
		{
			self::$_log = new Zend_Log(self::$_writer);
		}
		
		switch ($priority)
		{
			case LOG_ALERT:
			case LOG_CRIT:
			case LOG_EMERG:
			case LOG_ERR:
//			case LOG_NOTICE:
			case LOG_DEBUG:
			case LOG_WARNING:
				self::$_log->log($message, $priority);
				break;
			default:
				break;
		}
	}
}
?>