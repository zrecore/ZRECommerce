<?php
/**
 * ControllerTests - A Test Suite for controllers.
 * 
 * @author aalbino
 */

require_once 'application/default/controllers/IndexControllerTest.php';
require_once 'application/default/controllers/OrdersControllerTest.php';

require_once 'application/admin/controllers/AclControllerTest.php';
require_once 'application/admin/controllers/ArticlesControllerTest.php';
require_once 'application/admin/controllers/BackupControllerTest.php';
require_once 'application/admin/controllers/IndexControllerTest.php';
require_once 'application/admin/controllers/LoginControllerTest.php';
require_once 'application/admin/controllers/LogsControllerTest.php';
require_once 'application/admin/controllers/OrdersControllerTest.php';
require_once 'application/admin/controllers/PluginsControllerTest.php';
// @todo require the Products controller.
require_once 'application/admin/controllers/SearchControllerTest.php';
require_once 'application/admin/controllers/SettingsControllerTest.php';
require_once 'application/admin/controllers/UsersControllerTest.php';

/**
 * ControllerTests class - aggregates all controller tests of this project
 */
class ControllerTests extends PHPUnit_Framework_TestSuite 
{
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'ControllerTests' );
		
		$this->addTestSuite ( 'IndexControllerTest' );
		$this->addTestSuite ( 'OrdersControllerTest' );
		
//		$this->addTestSuite ( 'Admin_AclControllerTest' );
//		$this->addTestSuite ( 'Admin_ArticlesControllerTest' );
//		$this->addTestSuite ( 'Admin_BackupControllerTest' );
		$this->addTestSuite ( 'Admin_IndexControllerTest' );
		$this->addTestSuite ( 'Admin_LoginControllerTest' );
//		$this->addTestSuite ( 'Admin_LogsControllerTest' );
//		$this->addTestSuite ( 'Admin_OrdersControllerTest' );
//		$this->addTestSuite ( 'Admin_PluginsControllerTest' );
//		$this->addTestSuite ( 'Admin_ProductsControllerTest' );
//		$this->addTestSuite ( 'Admin_SearchControllerTest' );
//		$this->addTestSuite ( 'Admin_SettingsControllerTest' );
//		$this->addTestSuite ( 'Admin_UsersControllerTest' );
		
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

