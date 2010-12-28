<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

/**
 * @todo This script to be run by 'Cron' every 24 hours.
 */
require_once(APPLICATION_PATH . '/bootstrap.php');

Bootstrap::setupPaths();
Bootstrap::setupAutoLoader();

class Orders_Paypal {

    function bootstrap() {
	Bootstrap::setupPaths();
	$application = new Zend_Application(
	    APPLICATION_ENV,
	    APPLICATION_PATH . '/settings/application.ini'
	);

	$application->bootstrap();

	Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
    }
    function __construct() {
	// Run!
	$this->bootstrap();
    }
}

$cli = new Orders_Paypal();