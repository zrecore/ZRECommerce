<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Cache
 * @category Cache
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Cache - Singleton class that generates a Zend_Cache object
 * using the settings found in settings.xml
 *
 */
class Zre_Cache
{
	/**
	 * Internal Zend_Cache object
	 * 
	 * @var Zend_Cache_Core|Zend_Cache_FrontEnd
	 */
	private static $_cache;
	
	/**
	 * Internal helper function. Sets up the Zend Cache object, with the
	 * specified parameters.
	 */
	private static function __setOptions($lifetime, $dir, $auto_serialize = true)
	{
		
		$cache_front_options = array(
			'lifetime' => $lifetime,
			'automatic_serialization' => $auto_serialize
		);
		
		$cache_back_options = array(  'cache_dir' => $dir );
		$cache = Zend_Cache::factory( 'Core', 'File', $cache_front_options, $cache_back_options );
		
		self::$_cache = $cache;
				
	}
	public static function generateSafeId($name) {
		
		$params = Zend_Controller_Front::getInstance()->getRequest()->getUserParams();
		$params_implode = '';
		
		foreach($params as $key => $param) {
			if (is_string($param)) {
				$params_implode .= "{$key}{$param}";
			}
		}
		$name .= $params_implode . Zend_Session::getId();
		$name = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
		return $name;
	}
	public static function load($id)
	{
		$settings = ZRE_Config::getSettingsCached();
		
		
		if (!isset(self::$_cache)) {
			self::__setOptions($settings->cache->lifetime->ui, $settings->cache->dir);
		}
		
		if (self::$_cache->test($id))
		{
			return self::$_cache->load($id);
		} else {
			return null;
		}
	}
	/**
	 * Cache data.
	 * 
	 * @param string $id - The unique id for this data.
	 * @param mixed $data - The data to save.
	 * @param string $type -  The type of data being stored. Valid values are 'ui' or 'query' only.
	 */
	public static function save($id, $data, $type = 'ui')
	{
		$settings = ZRE_Config::getSettingsCached();
		
		
		if ($type == 'ui') {
			self::__setOptions($settings->cache->lifetime->ui, $settings->cache->dir);
		} else {
			self::__setOptions($settings->cache->lifetime->query, $settings->cache->dir);
		}
		
		self::$_cache->save($data, $id);
	}
	
	public static function test($id)
	{
		$settings = ZRE_Config::getSettingsCached();
		
		
		if (!isset(self::$_cache)) {
			self::__setOptions($settings->cache->lifetime->ui, $settings->cache->dir);
		}

		return self::$_cache->test($id);
	}
}
?>