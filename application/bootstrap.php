<?php
/**
 * This is the bootstrap file that initializes the Zend MVC for this 
 * web application.
 * 
 * @author ZRECommerce
 * 
 * @package Boot
 * @subpackage Boot
 * @category Boot
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Bootstrap - Zend_Appication_Bootstrap_Bootstrap class. Internal methods are
 * called in the order they are written within this class.
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	
	protected function _initApplication() {
    	
    }
    
	public function _initAutoLoader() {
		
		require_once('../library/Zre/Loader.php');
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->setFallBackAutoloader(true);
		
		$autoLoader->registerNamespace('Zre_');
		$autoLoader->unshiftAutoLoader(array('Zre_Loader', 'loadClass'), 'Zre');
		return $autoLoader;
	}
	public function _initSession() {
		Zend_Session::start();
	}
	
	public function _initHelpers() {
		
		Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers', 'Zend_Controller_Action_Helper');
		$view = new Zend_View();
	}
	
	public function _initModules() {
		
	}
	
	public function _initRegistry() {
		
		Zre_Registry_Session::load();
	}
	
	public function _initSettings() {
		
		$settings = Zre_Config::getSettingsCached();
		
		if (!$settings) {
			$settings_path = realpath('../application/settings/environment/') . DIRECTORY_SEPARATOR . 'settings.xml';
			$settings = Zre_Config::loadSettings($settings_path, true);
//			$settings = ($settings->runmode->use == 'production')?$settings->production:$settings->dev;
		}
		
		$this->settings = $settings;
		return $settings;
	}
	
	public function _initLocale() {
		date_default_timezone_set( (string)$this->settings->site->timezone );
		$zre_locale = new Zre_Locale('auto');
		return $zre_locale;
	}
	
	protected function _initView() {
		
		// Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('ZreCommerce');
        $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
	}
	
	public function _initHeaderPlugins() {
		// ...Header plugins
		$data = Plugin_Dataset_Plugins::listAll('position = ? AND enabled=1', 'header');
		$headerItems = array();
		
		foreach ($data as $plugin) {
			$pluginName = 'Plugin_' . $plugin['name'];
			$headerItems[] = new $pluginName();
		}
		
		Zre_Registry_Session::set('header_items', $headerItems);
	}
	
	public function _initFooterPlugins() {
		// ...Footer plugins
		$data = Plugin_Dataset_Plugins::listAll('position = ? AND enabled=1', 'footer');
		$footerItems = array();
		
		foreach ($data as $plugin) {
			$pluginName = 'Plugin_' . $plugin['name'];
			$footerItems[] = new $pluginName();
		}
		
		Zre_Registry_Session::set('footer_items', $footerItems);
	}
	
	protected function _initRouters() {
		$routerConfig = APPLICATION_PATH . '/settings/environment/routes.default.php';
		
		if (file_exists($routerConfig)) require_once $routerConfig;
	}
}
?>