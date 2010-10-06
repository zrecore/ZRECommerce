<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Registry
 * @subpackage Registry
 * @category Registry
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Registry_Session - Provides a mechanism to save and load variables
 * into a zend session namespace. The contents of the registry remain
 * persistent until either the session expires (and is therefore no longer used),
 * or the session namespace is explicitly flushed.
 *
 */
class Zre_Registry_Session
{
	const DEFAULT_NAMESPACE = 'ZRE_REGISTRY_SESSION';
	public static $data = array();
	/**
	 * Loads data, if available, from a session namespace.
	 *
	 */
	public static function load()
	{
		if (Zend_Session::isStarted())
		{
			if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
			{
				$namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
				self::$data = unserialize($namespace->data);
			}
		}
	}
	/**
	 * Saves internal data to a session namespace.
	 *
	 */
	public static function save()
	{
		if (Zend_Session::isStarted())
		{
			if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE)) Zend_Session::namespaceUnset(self::DEFAULT_NAMESPACE);
			
			$namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
			$namespace->data = serialize(self::$data);
			
		}
	}
	/**
	 * Unsets data associated with the specified key '$index'. 
	 * If no key is specified, all internal data and the session namespace is unset.
	 *
	 * @param string $index
	 */
	public static function flush($index = null)
	{
		if (isset($index))
		{
			if (isset(self::$data[$index])) unset(self::$data[$index]);
			
		} else {
			// Destroy our session namespace
			if (Zend_Session::isStarted() && Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE)) Zend_Session::namespaceUnset(self::DEFAULT_NAMESPACE);
			// Reset our internal data array.
			self::$data = array();
		}
		self::save();
	}
	/**
	 * Retrieves the value associated with the key '$index'.
	 *
	 * @param string $index
	 * @return mixed
	 */
	public static function get($index)
	{
		return self::$data[$index];
	}
	/**
	 * Sets a value, using '$index' as the key, and '$value' as the value.
	 *
	 * @param string $index
	 * @param mixed $value
	 */
	public static function set($index, $value)
	{
		self::$data[$index] = $value;
	}
	/**
	 * Returns boolean 'true' if the specified key '$index' exists. Otherwise, 'false' is returned.
	 *
	 * @param string $index
	 */
	public static function isRegistered($index)
	{
		return isset(self::$data[$index]);
	}
}
?>