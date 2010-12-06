<?php
/**
 * Checkout_Adapter_Paypal_ClientTest - A Test Suite for your Application
 *
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
//require_once 'Zend/Config/Writer/Xml.php';
//require_once 'Zend/Test/DbAdapter.php';
//
//require_once 'Zend/Db/Table.php';
require_once realpath(dirname(__FILE__) . '/../../../../application/bootstrap.php');

/**
 * AllTests class - aggregates all tests of this project
 */
class Checkout_Adapter_PaypalTest extends PHPUnit_Framework_TestCase {

	public function __construct() {

		Bootstrap::setupPaths();
		Bootstrap::setupAutoLoader();
		$file = APPLICATION_PATH . '/settings/environment/settings.xml';
		$settings = Zre_Config::loadSettings($file, true);

		$this->setupDatabase();
	}

	protected function setupDatabase() {
		$db = new Zend_Test_DbAdapter();
		$settings = Zre_Config::getSettingsCached();
		$pre = $settings->db->table_name_prepend;
		
		$db->setDescribeTable(
			$pre . 'orders',
			array (
			  'order_id' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => $pre . 'orders',
			    'COLUMN_NAME' => 'order_id',
			    'COLUMN_POSITION' => 1,
			    'DATA_TYPE' => 'int',
			    'DEFAULT' => NULL,
			    'NULLABLE' => false,
			    'LENGTH' => NULL,
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => true,
			    'PRIMARY_POSITION' => 1,
			    'IDENTITY' => true,
			  ),
			  'decision' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => $pre . 'orders',
			    'COLUMN_NAME' => 'decision',
			    'COLUMN_POSITION' => 2,
			    'DATA_TYPE' => 'varchar',
			    'DEFAULT' => NULL,
			    'NULLABLE' => false,
			    'LENGTH' => '32',
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => false,
			    'PRIMARY_POSITION' => NULL,
			    'IDENTITY' => false,
			  ),
			  'order_date' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => $pre . 'orders',
			    'COLUMN_NAME' => 'order_date',
			    'COLUMN_POSITION' => 3,
			    'DATA_TYPE' => 'datetime',
			    'DEFAULT' => NULL,
			    'NULLABLE' => false,
			    'LENGTH' => NULL,
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => false,
			    'PRIMARY_POSITION' => NULL,
			    'IDENTITY' => false,
			  ),
			  'status' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => $pre . 'orders',
			    'COLUMN_NAME' => 'status',
			    'COLUMN_POSITION' => 4,
			    'DATA_TYPE' => 'enum(\'pending\',\'shipped\',\'void\',\'exchanged\',\'refunded\',\'awaiting_return\',\'complete\')',
			    'DEFAULT' => 'pending',
			    'NULLABLE' => false,
			    'LENGTH' => NULL,
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => false,
			    'PRIMARY_POSITION' => NULL,
			    'IDENTITY' => false,
			  ),
			  'merchant' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => $pre . 'orders',
			    'COLUMN_NAME' => 'merchant',
			    'COLUMN_POSITION' => 5,
			    'DATA_TYPE' => 'varchar',
			    'DEFAULT' => NULL,
			    'NULLABLE' => false,
			    'LENGTH' => '32',
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => false,
			    'PRIMARY_POSITION' => NULL,
			    'IDENTITY' => false,
			  ),
			)
		);

		$db->setDescribeTable(
			$pre . 'orders_paypal',
			array (
				'orders_paypal_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_paypal',
					'COLUMN_NAME' => 'orders_paypal_id',
					'COLUMN_POSITION' => 1,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => true,
					'PRIMARY_POSITION' => 1,
					'IDENTITY' => true,
				),
				'order_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_paypal',
					'COLUMN_NAME' => 'order_id',
					'COLUMN_POSITION' => 2,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => false,
					'PRIMARY_POSITION' => NULL,
					'IDENTITY' => false,
				),
				'transaction_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_paypal',
					'COLUMN_NAME' => 'transaction_id',
					'COLUMN_POSITION' => 3,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => false,
					'PRIMARY_POSITION' => NULL,
					'IDENTITY' => false,
				)
			)
		);

		$db->setDescribeTable(
			$pre . 'orders_products',
			array (
				'order_product_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_products',
					'COLUMN_NAME' => 'order_product_id',
					'COLUMN_POSITION' => 1,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => true,
					'PRIMARY_POSITION' => 1,
					'IDENTITY' => true,
				),
				'order_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_products',
					'COLUMN_NAME' => 'order_id',
					'COLUMN_POSITION' => 2,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => false,
					'PRIMARY_POSITION' => NULL,
					'IDENTITY' => false,
				),
				'product_id' =>
				array (
					'SCHEMA_NAME' => NULL,
					'TABLE_NAME' => $pre . 'orders_products',
					'COLUMN_NAME' => 'product_id',
					'COLUMN_POSITION' => 3,
					'DATA_TYPE' => 'int',
					'DEFAULT' => NULL,
					'NULLABLE' => false,
					'LENGTH' => NULL,
					'SCALE' => NULL,
					'PRECISION' => NULL,
					'UNSIGNED' => NULL,
					'PRIMARY' => false,
					'PRIMARY_POSITION' => NULL,
					'IDENTITY' => false,
				)
			)
		);

		$db->setDescribeTable(
			$pre . 'product',
			array (
			  'product_id' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => 'zre_product',
			    'COLUMN_NAME' => 'product_id',
			    'COLUMN_POSITION' => 1,
			    'DATA_TYPE' => 'int',
			    'DEFAULT' => NULL,
			    'NULLABLE' => false,
			    'LENGTH' => NULL,
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => true,
			    'PRIMARY_POSITION' => 1,
			    'IDENTITY' => true,
			  ),
			  'article_id' =>
			  array (
			    'SCHEMA_NAME' => NULL,
			    'TABLE_NAME' => 'zre_product',
			    'COLUMN_NAME' => 'article_id',
			    'COLUMN_POSITION' => 2,
			    'DATA_TYPE' => 'int',
			    'DEFAULT' => NULL,
			    'NULLABLE' => true,
			    'LENGTH' => NULL,
			    'SCALE' => NULL,
			    'PRECISION' => NULL,
			    'UNSIGNED' => NULL,
			    'PRIMARY' => false,
			    'PRIMARY_POSITION' => NULL,
			    'IDENTITY' => false,
			  )
			)
		);

		$statements = array();

		// Return the order insert ID info.
		$statements[] = Zend_Test_DbStatement::createInsertStatement(123);
		

		// Return the orderPaypal insert info.
		$statements[] = Zend_Test_DbStatement::createInsertStatement(223);
		
		// Return the product read info.
		$statements[] = Zend_Test_DbStatement::createSelectStatement(
		array(
			array(
				'product_id' => 987,
				'order_id' => 123,
				'sold' => 0,
				'allotment' => 100
			)
		));

		// Return the product table update info.
		$statements[] = Zend_Test_DbStatement::createUpdateStatement(1);

		// Return the orderProduct insert info.
		$statements[] = Zend_Test_DbStatement::createInsertStatement(333);
		
		$statements = array_reverse($statements, true);

		foreach($statements as $stmt) {
			$db->appendStatementToStack($stmt);
		}

		// These are the IDs (in reverse order just like our mock
		// statements) that need to be returned by our mock adapter.
		$db->appendLastInsertIdToStack(333); // order_product
		$db->appendLastInsertIdToStack(223); // order_paypal
		$db->appendLastInsertIdToStack(123); // order

		$db->getProfiler()->setEnabled(true);
		Zend_Db_Table::setDefaultAdapter($db);
		Zre_Db_Mysql::setDefaultAdapter($db);
	}

	public function testPay() {
		$db = Zend_Db_Table::getDefaultAdapter();

		// Test the adapter's ::pay() method here.
		$paypal = new Checkout_Adapter_Paypal();
		$paymentData = new stdClass();

		$file = APPLICATION_PATH . '/settings/environment/settings.xml';
		$settings = Zre_Config::getSettingsCached();
		$ppl = $settings->merchant->paypal;

		$paymentData->creditCardType = 'Visa';
		$paymentData->accountNumber = $ppl->api_test_credit_card_number;
		$paymentData->expirationMonth = $ppl->api_test_expiration_month;
		$paymentData->expirationYear = $ppl->api_test_expiration_year;
		$paymentData->cvv2 = 321;
		$paymentData->firstName = 'Test';
		$paymentData->lastName = 'User';
		$paymentData->street1 = '1 Test Ln';
		$paymentData->street2 = 'Suite 101';
		$paymentData->city = 'Testerville';
		$paymentData->state = 'CA';
		$paymentData->postalCode = '54321';
		$paymentData->country = 'US';

		$sku = '987';
		$costOptions = new Cart_Container_Item_Options_Cost(array(1.00));

		$detailOptions = new Cart_Container_Item_Options_Detail(
			array(	'desc' => 'phpUnit Test Item description text.',
				'weight' => '5.00',
				'title' => 'phpUnit Test Item'
			)
		);
		$quantity = 1;

		$metricOptions = new Cart_Container_Item_Options_Metrics(array(10.5));

		$item = new Cart_Container_Item(
			$sku,
			$detailOptions,
			$costOptions,
			$metricOptions,
			$quantity
		);

		$cartContainer = new Cart_Container();
		$cartContainer->addItem($item);

		$result = $paypal->pay($cartContainer, $paymentData);

		$queries = $db->getProfiler()->getQueryProfiles();
		
		$queriesString = '';

		foreach($queries as $q) {
			$queriesString .= $q->getQuery() . "\n\n";
		}

		$this->assertEquals(123, $result, $queriesString);
	}
}