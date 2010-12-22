<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

/**
 * @todo This script to be run by 'Cron' every 24 hours.
 */
require_once(APPLICATION_PATH . '/bootstrap.php');

Bootstrap::setupPaths();
Bootstrap::setupAutoLoader();

class Orders_Cybersource {

    function __construct() {
	// Run!
	
    }
}

$cli = new Orders_Cybersource();