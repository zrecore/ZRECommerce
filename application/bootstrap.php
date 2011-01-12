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
require_once 'Zend/Application/Bootstrap/ResourceBootstrapper.php';
require_once 'Zend/Application/Bootstrap/Bootstrapper.php';
require_once 'Zend/Application/Bootstrap/BootstrapAbstract.php';
require_once 'Zend/Application/Bootstrap/Bootstrap.php';

// Zend_Application
require_once 'Zend/Application.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {


    static function setupPaths() {
	if (!defined('BASE_PATH')) define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
	if (!defined('APPLICATION_PATH'))  define('APPLICATION_PATH', BASE_PATH . '/application');

	// Include path
	set_include_path(
		BASE_PATH . '/library'
		. PATH_SEPARATOR . get_include_path()
	);

	// Define application environment
	if (!defined('APPLICATION_ENV')) define('APPLICATION_ENV', 'test');
	defined('APPLICATION_ENV')
		|| define('APPLICATION_ENV',
		(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
		: 'production'));

    }

    public static function setupAutoLoader() {

//	require_once(BASE_PATH . '/library/Zre/Loader.php');
	require_once('Zend/Loader/Autoloader.php');
	$autoLoader = Zend_Loader_Autoloader::getInstance();
	$autoLoader->setFallBackAutoloader(true);

//	$autoLoader->registerNamespace('Zend_');
//	$autoLoader->registerNamespace('Zre_');
	
//	$autoLoader->unshiftAutoLoader(array('Zre_Loader', 'loadClass'), 'Zre');
	return $autoLoader;
    }

    public function _initAutoloader() {
	    self::setupAutoLoader();
    }
    public function _initSession() {
	Zend_Session::start();
    }

    public function _initHelpers() {

	Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/default/helpers', 'Zend_Controller_Action_Helper');
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
	    $settings_path = APPLICATION_PATH . '/settings/environment/settings.xml';
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

    public function _initView() {

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

    public function _initRouters() {
	$front = Zend_Controller_Front::getInstance();
	$router = $front->getRouter();

	// Article link
	$route = new Zend_Controller_Router_Route(
		'/a/:id/:title',
		array(
			'controller' => 'index',
			'action' => 'article',
			'module' => 'default'
		)
	);
	$router->addRoute('indexReadArticle', $route);

	// Category link
	$route = new Zend_Controller_Router_Route(
		'/c/:c/:title',
		array(
			'controller' => 'index',
			'action' => 'index',
			'module' => 'default'
		)
	);
	$router->addRoute('indexReadCategory', $route);

	// Order link. Provide a download link to a purchased download.
	$route = new Zend_Controller_Router_Route(
		'/orders/download/:order_id/:product_id',
		array(
			'controller' => 'orders',
			'action' => 'download',
			'module' => 'default'
		)
	);
	$router->addRoute('ordersDownload', $route);

	// Order link. Provide a download link to a purchased download.
	$route = new Zend_Controller_Router_Route(
		'/orders/file/:filename',
		array(
			'controller' => 'orders',
			'action' => 'file',
			'module' => 'default'
		)
	);
	$router->addRoute('ordersFile', $route);

	// Shop link.Provide a link to a product.
	$route = new Zend_Controller_Router_Route(
		'/p/:id/:title',
		array(
			'controller' => 'shop',
			'action' => 'product',
			'module' => 'default'
		)
	);
	$router->addRoute('shopProduct', $route);


	$front->setRouter($router);
    }
}