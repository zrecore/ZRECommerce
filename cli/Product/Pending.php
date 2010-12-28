<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

/**
 * @todo This script to be run by 'Cron' every 10 minutes.
 */
require_once(APPLICATION_PATH . '/bootstrap.php');

Bootstrap::setupPaths();
Bootstrap::setupAutoLoader();

class Product_Pending {

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
	
	$records = $this->getRecords();

	foreach($records as $row) {
	    $row->delete();
	}
    }

    function getRecords() {
	$productPending = new Zre_Dataset_ProductPending();
	$asArray = false;
	$columns = null;

	// Expire any records that have been inactive for more than 15 minutes.
	$columns = array(
	    'access_date' => array(
		'operator' => '<',
		'value' => new Zend_Db_Expr('DATE_SUB( NOW(), INTERVAL 30 MINUTE)')
	    )
	);
	$options = null;
	$results = null;
	try {
	    $results = $productPending->listAll($columns, $options, $asArray);
	} catch (Exception $e) {
	    echo Debug::sqlQueries() . "\n\n" . (string)$e;
	}
	return $results;
    }
}

$cli = new Product_Pending();