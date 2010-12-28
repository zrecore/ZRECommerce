<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Locale
 * @category Locale
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Locale - Provides locale and translation support.
 *
 */
class Zre_Locale
{
	const DEFAULT_NAMESPACE = 'Zre_Locale';
	
	public function __construct($locale = 'auto')
	{
		if (Zend_Session::isStarted())
		{
			$settings = Zre_Config::getSettingsCached();
			
			if (!$settings)
			{
				throw new Zend_Exception('Zre_Locale::__construct() - Website settings must be loaded in the bootstrap to continue.');
			} else {
				if ( !Zend_Session::namespaceIsset( self::DEFAULT_NAMESPACE ) )
				{
					
					try {
						$locale = new Zend_Locale($locale);
					} catch (Zend_Locale_Exception $e) {
						
						$locale = new Zend_Locale( (string)$settings->site->locale ); 
					}
					
					$translate = new Zend_Translate('gettext', BASE_PATH . '/languages/' . $locale->getLanguage() . '.mo' );
					
					$frontendOptions = array(
						'lifetime' => (int)$settings->cache->lifetime->ui,
						'automatic_serialization' => true
					);
					$backendOptions = array( 'cache_dir' => $settings->cache->dir );
					$cache = Zend_Cache::factory('Page',
                             'File',
                             $frontendOptions,
                             $backendOptions);
					
					$translate->setCache($cache);
					$locale->setCache($cache);
					
					$namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
					$namespace->locale = $locale;
					$namespace->translate = $translate;
					$namespace->last_locale_string = $locale;
					
					Zend_Registry::set('Zend_Locale', $locale);                                      
					Zend_Registry::set('Zend_Translate', $translate);
					
				} else {
					
					
					$namespace = (object)Zend_Session::namespaceGet( self::DEFAULT_NAMESPACE );
					Zend_Registry::set('Zend_Locale', $namespace->locale);
					Zend_Registry::set('Zend_Translate', $namespace->translate);
					
				}
			}
		} else {
			throw new Zend_Exception('Zre_Locale::__construct() - Sessions must be started to use auto locale setup.', 1);
		}
		
	}
	
	public function destroy()
	{
		if (Zend_Session::isStarted())
		{
			if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
			{
				Zend_Session::namespaceUnset(self::DEFAULT_NAMESPACE);
				
				if (Zend_Registry::isRegistered('Zend_Locale')) Zend_Registry::offsetUnset('Zend_Locale');
				if (Zend_Registry::isRegistered('Zend_Translate')) Zend_Registry::offsetUnset('Zend_Translate');
				
				return true;
			}
		}
		return false;
	}
}
?>